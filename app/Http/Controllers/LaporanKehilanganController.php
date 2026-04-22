<?php

namespace App\Http\Controllers;

use App\Models\LaporanKehilangan;
use App\Models\BarangTemuan;
use App\Models\Kategori;
use App\Models\User;
use App\Notifications\StatusUpdateNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanKehilanganController extends Controller
{
    private function adalahApi(Request $request)
    {
        return $request->wantsJson() || $request->is('api/*');
    }

    //notif ke user tertentu
    // Notifikasi ke user
    private function kirimNotifikasi($idUser, $judul, $pesan, $url = '#')
    {
        $user = User::find($idUser);
        if ($user) {
            try {
                $user->notify(new StatusUpdateNotification($judul, $pesan, $url));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Email failed: " . $e->getMessage());
            }
        }
    }

    // Notifikasi ke admin
    private function kirimNotifikasiKeAdmin($judul, $pesan, $url = '#')
    {
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            try {
                $admin->notify(new StatusUpdateNotification($judul, $pesan, $url));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Admin email failed: " . $e->getMessage());
            }
        }
    }

    // Simpan foto
    private function simpanFoto(Request $request)
    {
        if (!$request->hasFile('foto')) {
            return null;
        }
        $files = $request->file('foto');
        if (!is_array($files)) {
            $files = [$files];
        }
        $paths = [];
        foreach ($files as $file) {
            $paths[] = $file->store('laporan/kehilangan', 'public');
        }
        return $paths;
    }



    public function store(Request $request)
    {
        $request->validate([
            'id_kategori'    => 'required|exists:kategori,id_kategori',
            'nama_barang'    => 'required|string|max:255',
            'warna'          => 'required|string|max:100',
            'deskripsi'      => 'required|string',
            'tanggal_hilang' => 'required|date',
            'ciri_khusus'    => 'required|string',
            'tipe_model'     => 'nullable|string|max:255',
            'ukuran'         => 'nullable|string|max:100',
            'lokasi_hilang'  => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        $data['id_user'] = Auth::id() ?? $request->id_user;
        $data['status']  = 'menunggu';

        $foto = $this->simpanFoto($request);
        if ($foto) {
            $data['foto'] = $foto;
        }

        $laporan = LaporanKehilangan::create($data);

        // Notifikasi ke semua admin
        $this->kirimNotifikasiKeAdmin(
            'Laporan Kehilangan Baru',
            'Barang "' . $laporan->nama_barang . '" dilaporkan hilang oleh ' . (Auth::user()->mahasiswa?->nama ?? Auth::user()->email),
            route('petugas.kehilangan.show', $laporan->id_laporan)
        );

        if ($this->adalahApi($request)) {
            return response()->json(['message' => 'Berhasil', 'data' => $laporan], 201);
        }
        return redirect()->route('mahasiswa.kehilangan.index')
            ->with('success', 'Laporan kehilangan berhasil dikirim!');
    }

    public function show(Request $request, $id)
    {
        $laporan = LaporanKehilangan::with(['user', 'kategori'])->findOrFail($id);
        if ($this->adalahApi($request)) {
            return response()->json(['data' => $laporan]);
        }
        
        // Monitoring View
        if ($request->is('mahasiswa/*')) {
            return view('front.laporan_kehilangan.show', ['laporanKehilangan' => $laporan]);
        }

        // Administrative view
        return view('admin.laporan_kehilangan.show', ['laporanKehilangan' => $laporan]);
    }

    public function update(Request $request, $id)
    {
        abort_if(Auth::user()->role !== 'admin', 403, 'Hanya Admin yang dapat merubah status laporan.');

        $request->validate([
            'status' => 'required|in:menunggu,diproses,selesai,ditolak,dicocokkan',
        ]);

        $laporan = LaporanKehilangan::findOrFail($id);
        $laporan->update($request->only('status'));

        // Notifikasi ke pelapor
        $this->kirimNotifikasi(
            $laporan->id_user,
            'Laporan Kehilangan',
            'Status laporan "' . $laporan->nama_barang . '": ' . $request->status,
            route('mahasiswa.kehilangan.show', $laporan->id_laporan)
        );

        if ($this->adalahApi($request)) {
            return response()->json(['message' => 'Berhasil', 'data' => $laporan]);
        }
        return back()->with('success', 'Status berhasil diperbarui!');
    }

    public function destroy(Request $request, $id)
    {
        $laporan = LaporanKehilangan::findOrFail($id);
        
        // Mahasiswa hanya bisa menghapus laporan miliknya sendiri
        if (Auth::user()->role !== 'admin' && $laporan->id_user !== Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menghapus laporan ini.');
        }

        // INTEGRITY LOCK: Jika sudah diproses, dicocokkan, atau selesai
        if (Auth::user()->role !== 'admin') {
            if ($laporan->status !== 'menunggu') {
                return back()->with('error', 'Laporan tidak bisa dihapus karena sudah dalam tahap penanganan Admin atau sudah ditemukan. Hubungi Admin jika perlu pembatalan khusus.');
            }
        }

        $laporan->delete();
        
        if ($this->adalahApi($request)) {
            return response()->json(['message' => 'Berhasil dihapus']);
        }
        return redirect()->route('mahasiswa.kehilangan.index')->with('success', 'Laporan berhasil dihapus!');
    }

    // ambil data untuk index
    public function mahasiswaIndex(Request $request)
    {
        $query = LaporanKehilangan::with('kategori')->latest();

        // Filter Pencarian
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'like', "%$search%")
                  ->orWhere('id_laporan', 'like', "%$search%")
                  ->orWhere('merk', 'like', "%$search%");
            });
        }

        // Filter Kategori
        if ($request->filled('category')) {
            $query->where('id_kategori', $request->category);
        }

        if (\Illuminate\Support\Facades\Auth::guest()) {
            if (request()->has('page') && request()->page > 1) {
                return redirect()->route('login')->with('error', 'Login untuk melihat lebih banyak laporan.');
            }
            $allItems = $query->paginate(3);
        } else {
            $allItems = $query->paginate(12);
        }

        $kategoris = Kategori::kehilangan()->orderBy('nama_kategori')->get();
        return view('front.laporan_kehilangan.index', compact('allItems', 'kategoris'));
    }

    // form buat laporan baru
    public function mahasiswaCreate()
    {
        $kategoris = Kategori::kehilangan()->orderBy('nama_kategori')->get();
        return view('front.laporan_kehilangan.create', compact('kategoris'));
    }

    // daftar laporan untuk admin
    public function petugasIndex(Request $request)
    {
        $query = LaporanKehilangan::with(['user', 'kategori'])->latest();

        // Filter Pencarian
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'like', "%$search%")
                  ->orWhere('id_laporan', 'like', "%$search%")
                  ->orWhere('merk', 'like', "%$search%");
            });
        }

        // Filter Kategori
        if ($request->filled('category')) {
            $query->where('id_kategori', $request->category);
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $laporan = $query->paginate(10);
        $kategoris = Kategori::kehilangan()->orderBy('nama_kategori')->get();

        return view('admin.laporan_kehilangan.index', compact('laporan', 'kategoris'));
    }

    // tandai barang ditemukan orang lain
    public function markAsFound(Request $request, $id)
    {
        $laporan = LaporanKehilangan::findOrFail($id);
        
        if ($laporan->id_user === Auth::id()) {
            return back()->with('error', 'Gunakan tombol Hapus jika Anda menemukan barang milik sendiri.');
        }

        // Cek apakah user ini sudah melaporkan penemuan untuk laporan ini
        $exists = \App\Models\BarangTemuan::where('id_laporan_kehilangan', $laporan->id_laporan)
            ->where('id_user_pelapor', Auth::id())
            ->exists();
        
        if ($exists) {
            return back()->with('error', 'Anda sudah melaporkan penemuan untuk barang ini. Silakan serahkan fisik barang ke Admin.');
        }

        // 1. Buat draf Barang Temuan (Jembatan)
        $barangTemuan = \App\Models\BarangTemuan::create([
            'id_user_pelapor'   => Auth::id(),
            'id_kategori'       => $laporan->id_kategori,
            'id_laporan_kehilangan' => $laporan->id_laporan,
            'nama_barang'       => '[PENEMUAN] ' . $laporan->nama_barang,
            'warna'             => $laporan->warna,
            'merk'              => $laporan->merk,
            'deskripsi_singkat' => 'Dilaporkan ditemukan oleh user melalui portal publik. Terhubung dengan Laporan Kehilangan #' . $laporan->id_laporan,
            'lokasi_nama'       => 'Menunggu konfirmasi lokasi penyerahan',
            'tanggal_ditemukan' => now(),
            'status_verifikasi' => 'belum',
            'status_penyerahan' => 'menunggu_diserahkan',
        ]);

        // 2. Update status laporan kehilangan menjadi dicocokkan
        $laporan->update(['status' => 'dicocokkan']);

        // 3. Notifikasi ke pemilik laporan
        $this->kirimNotifikasi(
            $laporan->id_user,
            'Barang Anda Ditemukan!',
            'Seseorang mengklaim telah menemukan "' . $laporan->nama_barang . '". Barang sedang diproses untuk diserahkan ke Admin.',
            route('mahasiswa.kehilangan.show', $laporan->id_laporan)
        );

        // 4. Notifikasi ke Admin
        $this->kirimNotifikasiKeAdmin(
            'Klaim Penemuan (Jembatan)',
            Auth::user()->email . ' mengklaim menemukan barang untuk Laporan #' . $laporan->id_laporan . '. Menunggu penyerahan fisik.',
            route('petugas.temuan.show', $barangTemuan->id_barang)
        );

        return back()->with('success', 'Berhasil melaporkan penemuan! Silakan segera serahkan fisik barang ke kantor Admin/Security untuk diverifikasi.');
    }
    public function markFound($id)
    {
        $laporan = LaporanKehilangan::findOrFail($id);
        
        // 1. Cek duplikasi akses: Jika sudah dicocokkan/selesai
        if (!in_array($laporan->status, ['menunggu', 'diproses'])) {
            return redirect()->back()->with('error', 'Maaf, laporan ini sudah dalam proses penemuan atau sudah diklaim oleh orang lain.');
        }

        // 2. Cek jika pelapornya sendiri yang klik
        if ($laporan->id_user === Auth::id()) {
            return redirect()->back()->with('error', 'Anda adalah pelapor barang ini. Gunakan menu Edit jika barang sudah ditemukan sendiri.');
        }

        // Redirect ke form Lapor Temuan dengan meneruskan ID laporan kehilangan ini
        return redirect()->route('mahasiswa.temuan.create', [
            'id_laporan_kehilangan' => $laporan->id_laporan,
            'nama_barang' => $laporan->nama_barang,
            'id_kategori' => $laporan->id_kategori,
            'warna' => $laporan->warna,
            'merk' => $laporan->merk,
            'tipe_model' => $laporan->tipe_model
        ])->with('success', 'Satu langkah lagi! Silakan lengkapi detail penemuan untuk barang ' . $laporan->nama_barang . ' agar segera diverifikasi Admin.');
    }
}
