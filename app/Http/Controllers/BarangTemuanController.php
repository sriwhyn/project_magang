<?php

namespace App\Http\Controllers;

use App\Models\BarangTemuan;
use App\Models\LaporanKehilangan;
use App\Models\Kategori;
use App\Models\Admin;
use App\Models\RiwayatPelanggaran;
use App\Models\User;
use App\Models\PengajuanBanding;
use App\Models\KlaimBarang;
use App\Notifications\StatusUpdateNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarangTemuanController extends Controller
{
    private function adalahApi(Request $request)
    {
        return $request->wantsJson() || $request->is('api/*');
    }

    private function simpanFoto(Request $request, $namaField = 'foto', $folder = 'barang/temuan')
    {
        if (!$request->hasFile($namaField)) {
            return null;
        }
        $files = $request->file($namaField);
        if (!is_array($files)) {
            $files = [$files];
        }
        $paths = [];
        foreach ($files as $file) {
            $paths[] = $file->store($folder, 'public');
        }
        return $paths;
    }

    private function kirimNotifikasi($idUser, $judul, $pesan, $url = '#')
    {
        $user = User::find($idUser);
        if ($user) {
            try {
                $user->notify(new StatusUpdateNotification($judul, $pesan, $url));
            } catch (\Exception $e) {
                // Log error if needed, but allow process to continue
                \Illuminate\Support\Facades\Log::warning("Gagal mengirim email ke {$user->email}: " . $e->getMessage());
            }
        }
        return $user;
    }

    // notif ke admin
    private function kirimNotifikasiKeAdmin($judul, $pesan, $url = '#')
    {
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            try {
                $admin->notify(new StatusUpdateNotification($judul, $pesan, $url));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Gagal mengirim email ke Admin {$admin->email}: " . $e->getMessage());
            }
        }
    }

    // cocokkan otomatis dengan laporan kehilangan
    private function cocokkanDenganLaporanKehilangan(BarangTemuan $barang)
    {
        // Temukan laporan kehilangan yang aktif dan kategorinya sama
        $laporanCocok = LaporanKehilangan::where('status', 'menunggu')
            ->where('id_kategori', $barang->id_kategori)
            ->get();
 
        foreach ($laporanCocok as $laporan) {
            // 1. VALIDASI TANGGAL (Penyaring Mutlak)
            // Barang tidak mungkin ditemukan SEBELUM dilaporkan hilang
            if ($barang->tanggal_ditemukan->lt($laporan->tanggal_hilang)) {
                continue;
            }

            $skor = 0;

            // 2. NOMOR SERI (Kecocokan Mutlak +100)
            if (!empty($laporan->nomor_seri) && !empty($barang->nomor_seri)) {
                if (strtolower(trim($laporan->nomor_seri)) === strtolower(trim($barang->nomor_seri))) {
                    $skor += 100;
                }
            }

            if ($skor < 100) {
                // 3. NAMA BARANG (Bobot 40% / 40 Poin)
                if (!empty($laporan->nama_barang) && !empty($barang->nama_barang)) {
                    similar_text(strtolower($laporan->nama_barang), strtolower($barang->nama_barang), $persen);
                    $skor += round($persen / 100 * 40);
                }

                // 4. MERK / BRAND (Bobot 15% / 15 Poin)
                if (!empty($laporan->merk) && !empty($barang->merk)) {
                    similar_text(strtolower($laporan->merk), strtolower($barang->merk), $persen);
                    $skor += round($persen / 100 * 15);
                }

                // 5. CIRI KHUSUS / DESKRIPSI (Bobot 25% / 25 Poin)
                $descLaporan = strtolower($laporan->ciri_khusus . ' ' . $laporan->deskripsi);
                $descTemuan = strtolower($barang->deskripsi_singkat);
                if (!empty($descLaporan) && !empty($descTemuan)) {
                    similar_text($descLaporan, $descTemuan, $persen);
                    $skor += round($persen / 100 * 25);
                }

                // 6. WARNA (Bobot 10% / 10 Poin)
                if (!empty($laporan->warna) && !empty($barang->warna)) {
                    similar_text(strtolower($laporan->warna), strtolower($barang->warna), $persen);
                    $skor += round($persen / 100 * 10);
                }

                // 7. LOKASI (Bobot 10% / 10 Poin)
                if (!empty($laporan->lokasi_hilang) && !empty($barang->lokasi_nama)) {
                    similar_text(strtolower($laporan->lokasi_hilang), strtolower($barang->lokasi_nama), $persen);
                    $skor += round($persen / 100 * 10);
                }
            }

            // AMBANG BATAS: Minimal 60% untuk mengirim notifikasi
            if ($skor >= 60) {
                // Jangan update status secara otomatis di sini agar tidak membingungkan pengguna.
                // Status 'dicocokkan' hanya boleh dipicu oleh aksi manual penemu.

                $this->kirimNotifikasi(
                    $laporan->id_user,
                    'Potensi Barang Ditemukan!',
                    'Barang "' . $barang->nama_barang . '" yang ditemukan sangat mirip dengan laporan kehilangan Anda (Kecocokan: ' . $skor . '%). Silakan cek dan ajukan klaim.',
                    route('mahasiswa.temuan.index')
                );

                // Notifikasi ke admin untuk awareness
                $this->kirimNotifikasiKeAdmin(
                    'Pencocokan Otomatis (' . $skor . '%)',
                    'Temuan "' . $barang->nama_barang . '" cocok dengan laporan milik ' . ($laporan->user->mahasiswa?->nama ?? $laporan->user->email),
                    route('petugas.temuan.show', $barang->id_barang)
                );
            }
        }
    }

    public function index(Request $request)
    {
        $data = BarangTemuan::with(['pelapor', 'kategori'])->latest()->get();
        if ($this->adalahApi($request)) {
            return response()->json(['data' => $data]);
        }
        abort(404);
    }

    // mahasiswa lapor barang temuan
    public function store(Request $request)
    {
        $request->validate([
            'id_kategori'       => 'required|exists:kategori,id_kategori',
            'nama_barang'       => 'required|string|max:255',
            'deskripsi_singkat' => 'required|string',
            'lokasi_nama'       => 'required|string|max:255',
            'tanggal_ditemukan' => 'required|date',
            'warna'             => 'required|string|max:100',
        ]);

        // PROTEKSI: Cek jika laporan kehilangan sudah diklaim orang lain
        if ($request->id_laporan_kehilangan) {
            $checkLaporan = \App\Models\LaporanKehilangan::find($request->id_laporan_kehilangan);
            if ($checkLaporan && !in_array($checkLaporan->status, ['menunggu', 'diproses'])) {
                return redirect()->route('mahasiswa.kehilangan.index')
                    ->with('error', 'Maaf, laporan kehilangan tersebut sudah dalam proses penanganan oleh orang lain.');
            }
        }

        $data = $request->only(
            'id_kategori', 'nama_barang', 'deskripsi_singkat', 'lokasi_nama',
            'tanggal_ditemukan', 'warna', 'latitude', 'longitude',
            'merk', 'tipe_model', 'nomor_seri', 'ukuran', 'id_laporan_kehilangan'
        );

        $data['id_user_pelapor']     = Auth::id() ?? $request->id_user_pelapor;
        $data['id_admin_penerima']   = Admin::first()?->id_admin; // Selalu ke admin pertama
        $data['status_verifikasi']   = 'belum';
        $data['is_published']        = false;
        $data['status_klaim']        = 'belum';
        $data['status_penyerahan']   = 'menunggu_diserahkan';
        $data['deadline_penyerahan'] = null; // Deadline diset oleh admin nanti

        $foto = $this->simpanFoto($request);
        if ($foto) {
            $data['foto'] = $foto;
        }

        $barang = BarangTemuan::create($data);

        // Jika laporan penemuan ini berasal dari pencocokan manual (klik tombol di detail kehilangan)
        if ($barang->id_laporan_kehilangan) {
            $laporan = \App\Models\LaporanKehilangan::find($barang->id_laporan_kehilangan);
            if ($laporan) {
                $laporan->update(['status' => 'dicocokkan']);
                
                // Auto-create Klaim Barang menggunakan data laporan kehilangan
                KlaimBarang::create([
                    'id_kategori'           => $laporan->id_kategori,
                    'id_barang'             => $barang->id_barang,
                    'id_user_pengaju'       => $laporan->id_user,
                    'nama_pemilik'          => $laporan->user->mahasiswa?->nama ?? $laporan->user->email,
                    'merk'                  => $laporan->merk,
                    'tipe_model'            => $laporan->tipe_model,
                    'nomor_seri'            => $laporan->nomor_seri,
                    'warna'                 => $laporan->warna,
                    'deskripsi_ciri_khusus' => $laporan->ciri_khusus,
                    'tanggal_hilang'        => $laporan->tanggal_hilang,
                    'lokasi_hilang'         => $laporan->lokasi_hilang,
                    'status_klaim'          => 'menunggu',
                    'tanggal_pengajuan'     => now(),
                    'skor_kecocokan'        => 100, // Manual link = 100% match
                ]);

                // Update status di Barang Temuan juga menjadi 'proses' karena sudah ada klaim otomatis
                $barang->update(['status_klaim' => 'proses']);

                // Notifikasi ke pemilik barang
                $this->kirimNotifikasi(
                    $laporan->id_user,
                    'Barang Anda Ditemukan!',
                    'Seseorang melaporkan telah menemukan "' . $barang->nama_barang . '". Klaim otomatis telah dibuat untuk Anda. Silakan hubungi Admin untuk verifikasi fisik.',
                    route('mahasiswa.kehilangan.show', $laporan->id_laporan)
                );
            }
        }

        // Notifikasi ke admin bahwa ada barang temuan baru
        $this->kirimNotifikasiKeAdmin(
            'Barang Temuan Baru',
            'Barang "' . $barang->nama_barang . '" ditemukan oleh ' . (Auth::user()->mahasiswa?->nama ?? Auth::user()->email) . '. Menunggu penyerahan fisik.',
            route('petugas.temuan.show', $barang->id_barang)
        );

        if ($this->adalahApi($request)) {
            return response()->json(['message' => 'Berhasil', 'data' => $barang], 201);
        }
        return redirect()->route('mahasiswa.temuan.index')
            ->with('success', 'Barang berhasil dilaporkan! Masuk antrean verifikasi Admin.');
    }

    public function show(Request $request, $id)
    {
        $barangTemuan = BarangTemuan::with(['pelapor', 'kategori', 'klaimBarang'])->findOrFail($id);
        if ($this->adalahApi($request)) {
            return response()->json(['data' => $barangTemuan]);
        }
        
        // Monitoring View
        if ($request->is('mahasiswa/*')) {
            return view('front.barang_temuan.show', compact('barangTemuan'));
        }

        // Administrative view
        return view('admin.barang_temuan.show', compact('barangTemuan'));
    }

    // update status (admin)
    public function update(Request $request, $id)
    {
        abort_if(Auth::user()->role !== 'admin', 403, 'Aksi khusus Admin.');
        $barangTemuan = BarangTemuan::findOrFail($id);
        
        // LOGIKA LOCK: Jika sudah diverifikasi & ada deadline, tidak boleh ubah-ubah lagi 
        // KECUALI ada banding yang disetujui ATAU sedang ingin mengonfirmasi barang diterima (sudah_diterima).
        if ($barangTemuan->status_verifikasi == 'terverifikasi' && $barangTemuan->deadline_penyerahan) {
            $isConfirmingReceipt = ($request->status_penyerahan === 'sudah_diterima');
            
            $hasApprovedBanding = PengajuanBanding::where('id_barang', $barangTemuan->id_barang)
                ->where('status', 'disetujui')
                ->exists();
            
            if (!$hasApprovedBanding && !$isConfirmingReceipt) {
                return back()->with('error', 'Status dan Deadline sudah dikunci. Gunakan fitur Banding untuk perubahan data.');
            }
        }

        $data = $request->all();

        // Jika admin menerima barang
        if (($data['status_penyerahan'] ?? null) === 'sudah_diterima') {
            $data['is_published']      = true;
            $data['status_verifikasi'] = 'terverifikasi';
            $data['deadline_penyerahan'] = null; // Deadline mati
            $data['status_perpanjangan'] = null; // Status perpanjangan juga selesai
            $poin = 5;
            $data['poin_reward'] = $poin;

            $pelapor = User::find($barangTemuan->id_user_pelapor);
            if ($pelapor) {
                $pelapor->increment('poin', $poin);
            }

            $this->kirimNotifikasi(
                $barangTemuan->id_user_pelapor,
                'Barang Diterima',
                'Barang "' . $barangTemuan->nama_barang . '" diterima admin. Anda mendapat ' . $poin . ' poin.',
                route('mahasiswa.temuan.show', $barangTemuan->id_barang)
            );
        }

        if (isset($data['status_verifikasi']) && $data['status_verifikasi'] !== $barangTemuan->status_verifikasi) {
            $this->kirimNotifikasi(
                $barangTemuan->id_user_pelapor,
                'Status Barang',
                'Barang "' . $barangTemuan->nama_barang . '" sekarang: ' . $data['status_verifikasi'],
                route('mahasiswa.temuan.show', $barangTemuan->id_barang)
            );
        }

        // Jika ada input deadline (dari form verifikasi yang baru)
        if ($request->filled('deadline_tanggal') && $request->filled('deadline_jam')) {
            $deadlineStr = $request->deadline_tanggal . ' ' . $request->deadline_jam;
            // Gunakan timezone Asia/Jakarta agar sesuai dengan lokasi user 
            $deadline = \Carbon\Carbon::parse($deadlineStr, 'Asia/Jakarta');
            $now = \Carbon\Carbon::now('Asia/Jakarta');
            
            if ($deadline->lte($now)) {
                return back()->with('error', 'Gagal: Waktu deadline tidak boleh di masa lalu. Jam tersebut sudah lewat!');
            }
            
            $data['deadline_penyerahan'] = $deadline;
            $data['notifikasi_deadline_terkirim'] = false;
            $data['notifikasi_reminder_terkirim'] = false;
        }

        $barangTemuan->update($data);

        // Jika barang baru diterima dan terhubung dengan laporan kehilangan
        if (($data['status_penyerahan'] ?? null) === 'sudah_diterima' && $barangTemuan->id_laporan_kehilangan) {
            $laporan = LaporanKehilangan::find($barangTemuan->id_laporan_kehilangan);
            if ($laporan) {
                $this->kirimNotifikasi(
                    $laporan->id_user,
                    'Barang Ready di Admin!',
                    'Barang "' . $laporan->nama_barang . '" sudah diterima Admin. Silakan buat "Klaim Barang" untuk proses pengambilan.',
                    route('mahasiswa.temuan.show', $barangTemuan->id_barang)
                );
            }
        }

        // Jika barang baru diterima, jalankan pencocokan otomatis
        if (($data['status_penyerahan'] ?? null) === 'sudah_diterima') {
            $this->cocokkanDenganLaporanKehilangan($barangTemuan);
        }

        if ($this->adalahApi($request)) {
            return response()->json(['message' => 'Berhasil', 'data' => $barangTemuan]);
        }
        return back()->with('success', 'Status berhasil diperbarui!');
    }

    public function setDeadline(Request $request, $id)
    {
        abort_if(Auth::user()->role !== 'admin', 403, 'Aksi khusus Admin.');
        $request->validate([
            'deadline_penyerahan' => 'required|date',
        ]);

        $deadline = \Carbon\Carbon::parse($request->deadline_penyerahan, 'Asia/Jakarta');
        $now = \Carbon\Carbon::now('Asia/Jakarta');

        if ($deadline->lte($now)) {
            return back()->with('error', 'Gagal: Waktu deadline tidak boleh di masa lalu. Jam tersebut sudah lewat!');
        }

        // Double-check di server: pastikan waktu benar-benar belum lewat
        $deadline = \Carbon\Carbon::parse($request->deadline_penyerahan);
        if ($deadline->lte(now())) {
            return back()->with('error', 'Waktu deadline sudah lewat. Pilih waktu yang belum lewat.');
        }

        $barang = BarangTemuan::findOrFail($id);
        $barang->update([
            'deadline_penyerahan'          => $request->deadline_penyerahan,
            'notifikasi_deadline_terkirim' => false,
            'notifikasi_reminder_terkirim' => false,
        ]);

        $this->kirimNotifikasi(
            $barang->id_user_pelapor,
            'Deadline Penyerahan',
            'Deadline untuk "' . $barang->nama_barang . '" telah ditetapkan: ' . $deadline->format('d M Y, h:i A') . '.',
            route('mahasiswa.temuan.show', $barang->id_barang)
        );

        return back()->with('success', 'Deadline berhasil ditetapkan: ' . $deadline->format('d M Y, h:i A'));
    }

    public function requestPerpanjangan(Request $request, $id)
    {
        $request->validate(['alasan_perpanjangan' => 'required|string|max:1000']);
        $barang = BarangTemuan::where('id_user_pelapor', Auth::id())->findOrFail($id);

        $updateData = [
            'alasan_perpanjangan' => $request->alasan_perpanjangan,
            'status_perpanjangan' => 'menunggu',
        ];

        if ($request->hasFile('foto_kendala')) {
            $updateData['foto_kendala'] = $request->file('foto_kendala')->store('barang/kendala', 'public');
        }

        $barang->update($updateData);

        // Notifikasi ke admin bahwa ada permintaan perpanjangan
        $this->kirimNotifikasiKeAdmin(
            'Permintaan Perpanjangan',
            (Auth::user()->mahasiswa?->nama ?? Auth::user()->email) . ' meminta perpanjangan waktu untuk barang "' . $barang->nama_barang . '".',
            route('petugas.temuan.show', $barang->id_barang)
        );

        return back()->with('success', 'Permintaan perpanjangan berhasil diajukan.');
    }

    public function respondPerpanjangan(Request $request, $id)
    {
        abort_if(Auth::user()->role !== 'admin', 403, 'Aksi khusus Admin.');
        $request->validate([
            'status_perpanjangan' => 'required|in:disetujui,ditolak',
            'deadline_penyerahan' => 'required_if:status_perpanjangan,disetujui|nullable|date|after:now',
        ], [
            'deadline_penyerahan.after' => 'Deadline baru harus lebih dari waktu sekarang.',
        ]);

        // Double-check server-side menggunakan timezone Jakarta
        if ($request->status_perpanjangan === 'disetujui' && $request->deadline_penyerahan) {
            $deadline = \Carbon\Carbon::parse($request->deadline_penyerahan, 'Asia/Jakarta');
            $now = \Carbon\Carbon::now('Asia/Jakarta');
            if ($deadline->lte($now)) {
                return back()->with('error', 'Waktu deadline sudah lewat (WIB). Pilih waktu yang belum lewat.');
            }
        }

        $barang = BarangTemuan::findOrFail($id);
        $updateData = ['status_perpanjangan' => $request->status_perpanjangan];
        if ($request->status_perpanjangan === 'disetujui' && $request->deadline_penyerahan) {
            $updateData['deadline_penyerahan'] = $request->deadline_penyerahan;
        }
        $barang->update($updateData);

        $this->kirimNotifikasi(
            $barang->id_user_pelapor,
            'Perpanjangan ' . ucfirst($request->status_perpanjangan),
            'Permintaan perpanjangan untuk "' . $barang->nama_barang . '" telah ' . $request->status_perpanjangan . '.',
            route('mahasiswa.temuan.show', $barang->id_barang)
        );

        return back()->with('success', 'Perpanjangan ' . $request->status_perpanjangan . '.');
    }

    public function markPenipuan(Request $request, $id)
    {
        abort_if(Auth::user()->role !== 'admin', 403, 'Aksi khusus Admin.');
        $barang = BarangTemuan::findOrFail($id);

        RiwayatPelanggaran::create([
            'id_user'           => $barang->id_user_pelapor,
            'jenis_pelanggaran' => 'penipuan_temuan',
            'tingkat'           => 'berat',
            'poin'              => 10,
            'deskripsi'         => 'Pelaporan tidak valid: "' . $barang->nama_barang . '"',
            'tanggal'           => now(),
        ]);

        $user = User::find($barang->id_user_pelapor);
        if ($user) {
            $user->decrement('poin', 10);
        }

        $this->kirimNotifikasi(
            $barang->id_user_pelapor,
            'Pelanggaran',
            'Laporan "' . $barang->nama_barang . '" ditandai penipuan. Poin dikurangi 10.',
            route('mahasiswa.temuan.show', $barang->id_barang)
        );

        $barang->update(['status_verifikasi' => 'belum', 'is_published' => false]);
        return back()->with('success', 'Ditandai sebagai penipuan.');
    }

    public function destroy(Request $request, $id)
    {
        $barang = BarangTemuan::findOrFail($id);
        
        // Hanya pelapor atau admin yang bisa hapus
        if (Auth::user()->role !== 'admin' && Auth::id() !== $barang->id_user_pelapor) {
            abort(403, 'Anda tidak memiliki hak untuk menghapus laporan ini.');
        }

        // INTEGRITY LOCK: Jika sudah diverifikasi, sudah di admin, atau ada klaim aktif
        $hasActiveClaims = $barang->klaimBarang()->where('status_klaim', '!=', 'ditolak')->exists();
        if (Auth::user()->role !== 'admin') {
            if ($barang->status_verifikasi == 'terverifikasi' || $barang->status_penyerahan == 'sudah_diterima' || $hasActiveClaims) {
                return back()->with('error', 'Laporan tidak bisa dihapus karena sudah masuk tahap verifikasi/klaim. Silakan hubungi Admin jika ada kesalahan mendesak.');
            }
        }

        $barang->delete();
        
        if ($this->adalahApi($request)) {
            return response()->json(['message' => 'Berhasil dihapus']);
        }
        return redirect()->route('mahasiswa.temuan.index')->with('success', 'Barang berhasil dihapus!');
    }

    // AJUKAN BANDING (Mahasiswa)
    public function submitBanding(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'required|string|max:1000',
            'foto'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $barang = BarangTemuan::where('id_user_pelapor', Auth::id())->findOrFail($id);

        $foto_path = null;
        if ($request->hasFile('foto')) {
            $foto_path = $request->file('foto')->store('bandings', 'public');
        }

        PengajuanBanding::create([
            'id_user'       => Auth::id(),
            'id_barang'     => $barang->id_barang,
            'alasan'        => $request->alasan,
            'foto'          => $foto_path,
            'jenis_banding' => 'perpanjangan_deadline',
            'status'        => 'menunggu'
        ]);

        $this->kirimNotifikasiKeAdmin(
            'Pengajuan Banding Baru',
            'Seseorang mengajukan banding untuk barang "' . $barang->nama_barang . '".',
            route('petugas.temuan.show', $barang->id_barang)
        );

        return back()->with('success', 'Banding berhasil diajukan. Menunggu tinjauan Admin.');
    }

    // PROSES BANDING (Admin)
    public function approveBanding(Request $request, $id_banding)
    {
        abort_if(Auth::user()->role !== 'admin', 403, 'Aksi khusus Admin.');
        $banding = PengajuanBanding::findOrFail($id_banding);
        $banding->update(['status' => $request->status]); // disetujui / ditolak

        $this->kirimNotifikasi(
            $banding->id_user,
            'Banding ' . ucfirst($request->status),
            'Pengajuan banding Anda telah ' . $request->status . '.',
            route('mahasiswa.temuan.show', $banding->id_barang)
        );

        return back()->with('success', 'Banding telah ' . $request->status);
    }

    // index untuk publik
    public function mahasiswaIndex(Request $request)
    {
        $myItems = collect();
        if (Auth::check()) {
            $myItems = BarangTemuan::where('id_user_pelapor', Auth::id())
                ->with('kategori')
                ->latest()
                ->get();
        }

        $query = BarangTemuan::where('is_published', true)
            ->where('status_verifikasi', 'terverifikasi')
            ->where('status_penyerahan', 'sudah_diterima')
            ->with('kategori')
            ->latest('tanggal_ditemukan');

        // Filter Pencarian
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'like', "%$search%")
                  ->orWhere('id_barang', 'like', "%$search%")
                  ->orWhere('merk', 'like', "%$search%");
            });
        }

        // Filter Kategori
        if ($request->filled('category')) {
            $query->where('id_kategori', $request->category);
        }

        if (Auth::guest()) {
            if (request()->has('page') && request()->page > 1) {
                return redirect()->route('login')->with('error', 'Login untuk melihat lebih banyak laporan.');
            }
            $barang = $query->paginate(3);
        } else {
            $barang = $query->paginate(12);
        }

        $kategoris = Kategori::kehilangan()->orderBy('nama_kategori')->get();
        return view('front.barang_temuan.index', compact('barang', 'myItems', 'kategoris'));
    }

    // form lapor temuan
    public function mahasiswaCreate(Request $request)
    {
        $kategoris = Kategori::kehilangan()->orderBy('nama_kategori')->get();
        $prefilled = $request->all();
        return view('front.barang_temuan.create', compact('kategoris', 'prefilled'));
    }

    // daftar untuk admin
    public function petugasIndex(Request $request)
    {
        $query = BarangTemuan::with(['pelapor', 'kategori'])->latest();

        // Filter Pencarian
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'like', "%$search%")
                  ->orWhere('id_barang', 'like', "%$search%")
                  ->orWhere('merk', 'like', "%$search%");
            });
        }

        // Filter Kategori
        if ($request->filled('category')) {
            $query->where('id_kategori', $request->category);
        }

        // Filter Status Penyerahan
        if ($request->filled('status')) {
            $query->where('status_penyerahan', $request->status);
        }

        $barangTemuan = $query->paginate(10);
        $kategoris = Kategori::kehilangan()->orderBy('nama_kategori')->get();

        return view('admin.barang_temuan.index', compact('barangTemuan', 'kategoris'));
    }

    // admin tambah barang langsung
    public function petugasCreate()
    {
        $kategoris = Kategori::kehilangan()->orderBy('nama_kategori')->get();
        // Ambil laporan kehilangan yang masih aktif untuk dihubungkan secara manual
        $laporanKehilangan = LaporanKehilangan::whereIn('status', ['menunggu', 'dicocokkan'])
            ->with('user')
            ->latest()
            ->get();

        return view('admin.barang_temuan.create', compact('kategoris', 'laporanKehilangan'));
    }

    // admin simpan barang langsung
    public function petugasStore(Request $request)
    {
        $request->validate([
            'id_kategori'           => 'required|exists:kategori,id_kategori',
            'nama_barang'           => 'required|string|max:255',
            'deskripsi_singkat'     => 'required|string',
            'lokasi_nama'           => 'required|string|max:255',
            'tanggal_ditemukan'     => 'required|date',
            'warna'                 => 'required|string|max:100',
            'id_laporan_kehilangan' => 'nullable|exists:laporan_kehilangan,id_laporan',
        ]);

        $data = $request->all();
        $data['id_user_pelapor']   = Auth::id() ?? $request->id_user_pelapor;
        $data['status_verifikasi'] = 'terverifikasi';
        $data['status_klaim']      = 'belum';
        $data['status_penyerahan'] = 'sudah_diterima';
        $data['is_published']      = true;

        $foto = $this->simpanFoto($request);
        if ($foto) {
            $data['foto'] = $foto;
        }

        $barang = BarangTemuan::create($data);

        // Jika dihubungkan langsung dengan laporan kehilangan
        if ($request->id_laporan_kehilangan) {
            $laporan = LaporanKehilangan::find($request->id_laporan_kehilangan);
            if ($laporan) {
                $laporan->update(['status' => 'dicocokkan']);
                
                // Auto-create Klaim Barang (Admin side)
                KlaimBarang::create([
                    'id_kategori'           => $laporan->id_kategori,
                    'id_barang'             => $barang->id_barang,
                    'id_user_pengaju'       => $laporan->id_user,
                    'nama_pemilik'          => $laporan->user->mahasiswa?->nama ?? $laporan->user->email,
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

                $barang->update(['status_klaim' => 'proses']);

                // Beri tahu pemilik
                $this->kirimNotifikasi(
                    $laporan->id_user,
                    'Barang Ditemukan Admin!',
                    'Kabar baik! Barang "' . $laporan->nama_barang . '" Anda sudah diamankan oleh Admin. Klaim otomatis telah dibuat, silakan datang untuk verifikasi pengambilan.',
                    route('mahasiswa.temuan.show', $barang->id_barang)
                );
            }
        } else {
            // Jalankan pencocokan otomatis jika tidak dihubungkan manual
            $this->cocokkanDenganLaporanKehilangan($barang);
        }

        return redirect()->route('petugas.temuan.index')
            ->with('success', 'Barang temuan berhasil direkam dan diproses!');
    }
}
