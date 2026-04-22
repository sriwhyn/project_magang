<?php

namespace App\Http\Controllers;

use App\Models\KlaimBarang;
use App\Models\BarangTemuan;
use App\Models\Kategori;
use App\Models\User;
use App\Notifications\StatusUpdateNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KlaimBarangController extends Controller
{
    private function adalahApi(Request $request)
    {
        return $request->wantsJson() || $request->is('api/*');
    }

    private function kirimNotifikasi($idUser, $judul, $pesan, $url = '#')
    {
        $user = User::find($idUser);
        if ($user) {
            $user->notify(new StatusUpdateNotification($judul, $pesan, $url));
        }
    }

    // notif ke admin
    private function kirimNotifikasiKeAdmin($judul, $pesan, $url = '#')
    {
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new StatusUpdateNotification($judul, $pesan, $url));
        }
    }

    public function index(Request $request)
    {
        $klaim = KlaimBarang::with(['kategori', 'barangTemuan', 'pengaju'])->latest()->get();
        if ($this->adalahApi($request)) {
            return response()->json(['data' => $klaim]);
        }
        abort(404);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kategori'           => 'required|exists:kategori,id_kategori',
            'id_barang'             => 'required|exists:barang_temuan,id_barang',
            'nama_pemilik'          => 'required|string|max:255',
            'deskripsi_ciri_khusus' => 'required|string',
            'tanggal_hilang'        => 'required|date|before_or_equal:today',
            'lokasi_hilang'         => 'required|string|max:255',
        ]);

        $data = $request->all();
        $data['id_user_pengaju']   = Auth::id() ?? $request->id_user_pengaju;
        $data['status_klaim']      = 'menunggu';
        $data['tanggal_pengajuan'] = now();

        if ($request->hasFile('foto_bukti')) {
            $data['foto_bukti'] = $request->file('foto_bukti')->store('klaim/bukti', 'public');
        }

        $barangTemuan = BarangTemuan::find($request->id_barang);

        // Validasi Mutlak: Barang harus sudah ada di Admin
        if ($barangTemuan && $barangTemuan->status_penyerahan !== 'sudah_diterima') {
            return back()->withInput()
                ->withErrors(['error' => 'Maaf, barang ini belum diserahkan oleh penemu ke kantor. Anda baru bisa mengklaim setelah barang sudah diamankan oleh petugas.']);
        }

        // Validasi Eksklusivitas: Jika barang sudah dihubungkan dengan laporan kehilangan tertentu
        if ($barangTemuan && $barangTemuan->id_laporan_kehilangan) {
            $laporan = $barangTemuan->laporanKehilangan;
            if ($laporan && $laporan->id_user !== Auth::id()) {
                return back()->withInput()
                    ->withErrors(['error' => 'Maaf, barang ini sudah dicocokkan dengan laporan kehilangan milik orang lain. Anda tidak diperkenankan mengklaim barang ini.']);
            }
        }

        // Tidak boleh klaim barang sendiri
        if ($barangTemuan && $barangTemuan->id_user_pelapor == Auth::id()) {
            return back()->withInput()
                ->withErrors(['error' => 'Anda tidak dapat mengajukan klaim untuk barang yang Anda laporkan sendiri.']);
        }

        // Hitung skor kecocokan
        $data['skor_kecocokan'] = $barangTemuan ? $this->hitungSkor($data, $barangTemuan) : 0;
        $klaim = KlaimBarang::create($data);

        if ($barangTemuan) {
            $barangTemuan->update(['status_klaim' => 'proses']);
        }

        // Notifikasi ke semua admin tentang klaim baru
        $this->kirimNotifikasiKeAdmin(
            'Klaim Baru Masuk',
            'Klaim untuk barang "' . ($barangTemuan->nama_barang ?? '-') . '" oleh ' . (Auth::user()->mahasiswa?->nama ?? Auth::user()->email) . ' (skor: ' . $data['skor_kecocokan'] . '%)',
            route('petugas.klaim.show', $klaim->id_klaim)
        );

        if ($this->adalahApi($request)) {
            return response()->json(['message' => 'Klaim berhasil diajukan', 'data' => $klaim], 201);
        }
        return redirect()->route('mahasiswa.klaim.index')
            ->with('success', 'Klaim berhasil diajukan!');
    }

    // hitung skor kecocokan
    private function hitungSkor(array $klaim, BarangTemuan $temuan)
    {
        $skor = 0;

        if (!empty($klaim['warna']) && !empty($temuan->warna)) {
            similar_text(strtolower($klaim['warna']), strtolower($temuan->warna), $persen);
            $skor += round($persen / 100 * 20);
        }

        if (!empty($klaim['deskripsi_ciri_khusus']) && !empty($temuan->deskripsi_singkat)) {
            similar_text(strtolower($klaim['deskripsi_ciri_khusus']), strtolower($temuan->deskripsi_singkat), $persen);
            $skor += round($persen / 100 * 30);
        }

        if (isset($klaim['id_kategori']) && $klaim['id_kategori'] == $temuan->id_kategori) {
            $skor += 20;
        }

        if (!empty($klaim['lokasi_hilang']) && !empty($temuan->lokasi_nama)) {
            similar_text(strtolower($klaim['lokasi_hilang']), strtolower($temuan->lokasi_nama), $persen);
            $skor += round($persen / 100 * 20);
        }

        if (!empty($klaim['tanggal_hilang']) && !empty($temuan->tanggal_ditemukan)) {
            $selisihHari = abs(now()->parse($klaim['tanggal_hilang'])->diffInDays($temuan->tanggal_ditemukan));
            if ($selisihHari <= 1) $skor += 10;
            elseif ($selisihHari <= 7) $skor += 7;
            elseif ($selisihHari <= 30) $skor += 4;
        }

        return $skor;
    }

    public function show(Request $request, $id)
    {
        $klaimBarang = KlaimBarang::with(['pengaju', 'barangTemuan.kategori', 'kategori'])->findOrFail($id);
        if ($this->adalahApi($request)) {
            return response()->json(['data' => $klaimBarang]);
        }
        
        // Monitoring View
        if ($request->is('mahasiswa/*')) {
            return view('front.klaim_barang.show', compact('klaimBarang'));
        }

        // Administrative View
        return view('admin.klaim_barang.show', compact('klaimBarang'));
    }

    public function update(Request $request, $id)
    {
        abort_if(Auth::user()->role !== 'admin', 403, 'Hanya Admin yang dapat memproses klaim.');
        $klaimBarang = KlaimBarang::findOrFail($id);

        $klaimBarang->update([
            'status_klaim'       => $request->status_klaim ?? $klaimBarang->status_klaim,
            'catatan_admin'      => $request->catatan_admin ?? $klaimBarang->catatan_admin,
            'tanggal_verifikasi' => now(),
        ]);

        if ($request->status_klaim === 'disetujui') {
            $klaimBarang->barangTemuan?->update(['status_klaim' => 'selesai']);
        }
        if ($request->status_klaim === 'ditolak') {
            $klaimBarang->barangTemuan?->update(['status_klaim' => 'belum']);
        }

        if ($request->status_klaim) {
            $this->kirimNotifikasi(
                $klaimBarang->id_user_pengaju,
                'Klaim ' . ucfirst($request->status_klaim),
                'Klaim Anda telah ' . $request->status_klaim . '.',
                route('mahasiswa.klaim.show', $klaimBarang->id_klaim)
            );
        }

        if ($this->adalahApi($request)) {
            return response()->json(['message' => 'Berhasil', 'data' => $klaimBarang]);
        }
        return back()->with('success', 'Status klaim berhasil diperbarui!');
    }

    public function destroy(Request $request, $id)
    {
        KlaimBarang::findOrFail($id)->delete();
        if ($this->adalahApi($request)) {
            return response()->json(['message' => 'Berhasil dihapus']);
        }
        return back()->with('success', 'Klaim dihapus!');
    }

    // daftar klaim saya
    public function mahasiswaIndex(Request $request)
    {
        $query = KlaimBarang::where('id_user_pengaju', Auth::id())
            ->with(['barangTemuan', 'kategori'])
            ->latest();

        // Filter Pencarian
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('nama_pemilik', 'like', "%$search%")
                  ->orWhereHas('barangTemuan', function($bq) use ($search) {
                      $bq->where('nama_barang', 'like', "%$search%");
                  });
            });
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status_klaim', $request->status);
        }

        $klaim = $query->paginate(10);

        return view('front.klaim_barang.index', compact('klaim'));
    }

    public function mahasiswaCreate(Request $request)
    {
        $kategoris = Kategori::kehilangan()->orderBy('nama_kategori')->get();

        if (!$request->id_barang) {
            return redirect()->route('home')->withErrors([
                'error' => 'Pilih barang temuan yang ingin Anda klaim secara langsung dari beranda web!'
            ]);
        }

        $selectedBarang = BarangTemuan::with('laporanKehilangan')->findOrFail($request->id_barang);

        // VALIDASI JIT (Just-In-Time) CLAIMING: Jika user adalah pemilik laporan yang cocok
        if ($selectedBarang->id_laporan_kehilangan) {
            $laporan = $selectedBarang->laporanKehilangan;
            if ($laporan && $laporan->id_user === Auth::id()) {
                // Cek apakah sudah ada klaim
                $existingKlaim = KlaimBarang::where('id_barang', $selectedBarang->id_barang)
                    ->where('id_user_pengaju', Auth::id())
                    ->first();
                
                if ($existingKlaim) {
                    return redirect()->route('mahasiswa.klaim.show', $existingKlaim->id_klaim)
                        ->with('info', 'Laporan Anda sudah terhubung. Klaim ini sedang dalam tinjauan Admin.');
                }

                // Jika belum ada klaim (untuk data lama), buatkan otomatis sekarang
                $klaim = KlaimBarang::create([
                    'id_kategori'           => $laporan->id_kategori,
                    'id_barang'             => $selectedBarang->id_barang,
                    'id_user_pengaju'       => Auth::id(),
                    'nama_pemilik'          => Auth::user()->mahasiswa?->nama ?? Auth::user()->email,
                    'merk'                  => $laporan->merk,
                    'tipe_model'            => $laporan->tipe_model,
                    'nomor_seri'            => $laporan->nomor_seri,
                    'warna'                 => $laporan->warna,
                    'deskripsi_ciri_khusus' => $laporan->ciri_khusus,
                    'tanggal_hilang'        => $laporan->tanggal_hilang,
                    'lokasi_hilang'         => $laporan->lokasi_hilang,
                    'status_klaim'          => 'menunggu',
                    'tanggal_pengajuan'     => now(),
                    'skor_kecocokan'        => 100,
                ]);

                $selectedBarang->update(['status_klaim' => 'proses']);

                return redirect()->route('mahasiswa.klaim.show', $klaim->id_klaim)
                    ->with('success', 'Klaim otomatis diaktifkan berdasarkan laporan kehilangan Anda!');
            }
            
            // Jika orang lain (User C) mencoba klaim barang yang sudah dipesan si A, 
            // Kita tetap izinkan sekarang (sesuai permintaan user: bisa klaim barang yang orang lain sudah klaim)
        }

        // Cek apakah user sudah memiliki klaim aktif (menunggu) untuk barang ini
        $existingKlaimUser = KlaimBarang::where('id_barang', $selectedBarang->id_barang)
            ->where('id_user_pengaju', Auth::id())
            ->where('status_klaim', 'menunggu')
            ->first();

        if ($existingKlaimUser) {
            return redirect()->route('mahasiswa.klaim.show', $existingKlaimUser->id_klaim)
                ->with('info', 'Anda sudah mengajukan klaim untuk barang ini. Mohon tunggu verifikasi Admin.');
        }

        // Validasi Umum: Hanya barang terverifikasi, published, sudah di admin, dan status klaim BUKAN selesai
        if ($selectedBarang->status_verifikasi != 'terverifikasi' || !$selectedBarang->is_published || $selectedBarang->status_penyerahan != 'sudah_diterima' || $selectedBarang->status_klaim == 'selesai') {
            abort(404, 'Barang tidak tersedia untuk klaim baru.');
        }

        if ($selectedBarang->id_user_pelapor == Auth::id()) {
            return redirect()->route('home')
                ->withErrors(['error' => 'Anda tidak bisa mengklaim barang temuan Anda sendiri.']);
        }

        return view('front.klaim_barang.create', compact('kategoris', 'selectedBarang'));
    }

    // daftar klaim admin
    public function petugasIndex(Request $request)
    {
        $query = KlaimBarang::with(['pengaju', 'barangTemuan', 'kategori'])->latest();

        // Filter Pencarian
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('nama_pemilik', 'like', "%$search%")
                  ->orWhereHas('barangTemuan', function($bq) use ($search) {
                      $bq->where('nama_barang', 'like', "%$search%");
                  });
            });
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status_klaim', $request->status);
        }

        $klaim = $query->paginate(10);
        return view('admin.klaim_barang.index', compact('klaim'));
    }
}
