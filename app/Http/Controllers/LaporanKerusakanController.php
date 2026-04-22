<?php

namespace App\Http\Controllers;

use App\Models\LaporanKerusakan;
use App\Models\Kategori;
use App\Models\Petugas;
use App\Models\User;
use App\Notifications\StatusUpdateNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanKerusakanController extends Controller
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

    private function simpanFoto(Request $request)
    {
        if (!$request->hasFile('gambar')) {
            return null;
        }
        $files = $request->file('gambar');
        if (!is_array($files)) {
            $files = [$files];
        }
        $paths = [];
        foreach ($files as $file) {
            $paths[] = $file->store('laporan/kerusakan', 'public');
        }
        return $paths;
    }

    public function index(Request $request)
    {
        $data = LaporanKerusakan::with(['pelapor', 'kategori', 'petugas'])->latest()->get();
        if ($this->adalahApi($request)) {
            return response()->json(['data' => $data]);
        }
        abort(404);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kategori'      => 'required|exists:kategori,id_kategori',
            'judul_laporan'    => 'required|string|max:255',
            'deskripsi'        => 'required|string',
            'lokasi_kerusakan' => 'required|string|max:255',
        ]);

        $data = $request->only('id_kategori', 'judul_laporan', 'deskripsi', 'lokasi_kerusakan');
        $data['id_user_pelapor']  = Auth::id() ?? $request->id_user_pelapor;
        $data['status_perbaikan'] = 'dilaporkan';
        $data['tanggal_lapor']    = now()->toDateString();

        $foto = $this->simpanFoto($request);
        if ($foto) {
            $data['gambar'] = $foto;
        }

        $laporan = LaporanKerusakan::create($data);

        // Notifikasi ke semua admin
        $this->kirimNotifikasiKeAdmin(
            'Laporan Kerusakan Baru',
            'Kerusakan "' . $laporan->judul_laporan . '" di ' . $laporan->lokasi_kerusakan . ' dilaporkan oleh ' . (Auth::user()->mahasiswa?->nama ?? Auth::user()->email),
            route('petugas.kerusakan.show', $laporan->id_laporan)
        );

        if ($this->adalahApi($request)) {
            return response()->json(['message' => 'Berhasil', 'data' => $laporan], 201);
        }
        return redirect()->route('mahasiswa.kerusakan.index')
            ->with('success', 'Laporan kerusakan berhasil dikirim!');
    }

    public function show(Request $request, $id)
    {
        $laporanKerusakan = LaporanKerusakan::with(['pelapor', 'kategori', 'petugas'])->findOrFail($id);
        if ($this->adalahApi($request)) {
            return response()->json(['data' => $laporanKerusakan]);
        }
        
        // BRANCHING: Based on URL Path (Prefix)
        // /mahasiswa/* -> Monitoring View (Visual)
        // /petugas/* or /admin/* -> Management View (Form)
        if ($request->is('mahasiswa/*')) {
            return view('front.laporan_kerusakan.show', compact('laporanKerusakan'));
        }

        $semuaPetugas = Petugas::all();
        return view('admin.laporan_kerusakan.show', compact('laporanKerusakan', 'semuaPetugas'));
    }

    public function update(Request $request, $id)
    {
        $laporanKerusakan = LaporanKerusakan::findOrFail($id);
        
        // Proteksi: Laporan yang sudah selesai tidak boleh diubah lagi
        if ($laporanKerusakan->status_perbaikan === 'selesai') {
            return back()->with('error', 'Laporan sudah selesai dan dikunci, tidak bisa diubah lagi.');
        }

        $data = $request->only('status_perbaikan', 'catatan_petugas', 'judul_laporan', 'deskripsi', 'lokasi_kerusakan', 'id_petugas');

        // Jika menugaskan petugas baru
        if (isset($data['id_petugas']) && $data['id_petugas'] != $laporanKerusakan->id_petugas) {
            $data['status_perbaikan'] = 'menunggu_petugas';
            $petugasBaru = Petugas::find($data['id_petugas']);
            if ($petugasBaru && $petugasBaru->id_user) {
                $this->kirimNotifikasi(
                    $petugasBaru->id_user,
                    'Penugasan Baru',
                    'Anda ditugaskan untuk laporan kerusakan: "' . $laporanKerusakan->judul_laporan . '"',
                    route('petugas.kerusakan.show', $laporanKerusakan->id_laporan)
                );
            }
        }

        // Handle foto bukti pengerjaan
        if ($request->hasFile('foto_bukti_pengerjaan')) {
            $files = $request->file('foto_bukti_pengerjaan');
            if (!is_array($files)) {
                $files = [$files];
            }
            $paths = [];
            foreach ($files as $file) {
                $paths[] = $file->store('laporan/bukti_pengerjaan', 'public');
            }
            $data['foto_bukti_pengerjaan'] = $paths;
        }

        $laporanKerusakan->update($data);
        $status = $data['status_perbaikan'] ?? $laporanKerusakan->status_perbaikan;

        // Notifikasi ke pelapor
        $this->kirimNotifikasi(
            $laporanKerusakan->id_user_pelapor,
            'Laporan Kerusakan',
            'Status "' . $laporanKerusakan->judul_laporan . '": ' . str_replace('_', ' ', $status),
            route('mahasiswa.kerusakan.show', $laporanKerusakan->id_laporan)
        );

        if ($this->adalahApi($request)) {
            return response()->json(['message' => 'Berhasil', 'data' => $laporanKerusakan]);
        }
        return back()->with('success', 'Status berhasil diperbarui!');
    }

    public function destroy(Request $request, $id)
    {
        LaporanKerusakan::findOrFail($id)->delete();
        if ($this->adalahApi($request)) {
            return response()->json(['message' => 'Berhasil dihapus']);
        }
        return back()->with('success', 'Laporan dihapus!');
    }

    // index untuk publik
    public function mahasiswaIndex(Request $request)
    {
        $query = LaporanKerusakan::with(['pelapor', 'kategori'])->latest();

        // Filter Pencarian
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('judul_laporan', 'like', "%$search%")
                  ->orWhere('id_laporan', 'like', "%$search%")
                  ->orWhere('lokasi_kerusakan', 'like', "%$search%");
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

        $kategoris = Kategori::kerusakan()->orderBy('nama_kategori')->get();
        return view('front.laporan_kerusakan.index', compact('allItems', 'kategoris'));
    }

    // form lapor kerusakan
    public function mahasiswaCreate()
    {
        $kategoris = Kategori::kerusakan()->orderBy('nama_kategori')->get();
        return view('front.laporan_kerusakan.create', compact('kategoris'));
    }

    // daftar untuk admin atau petugas
    public function petugasIndex(Request $request)
    {
        $user = Auth::user();
        if ($user->role === 'petugas') {
            $petugasId = $user->petugas->id_petugas ?? null;
            $query = LaporanKerusakan::where('id_petugas', $petugasId)
                ->with(['pelapor', 'kategori'])
                ->latest();
        } else {
            $query = LaporanKerusakan::with(['pelapor', 'kategori', 'petugas'])
                ->latest();
        }

        // Filter Pencarian
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('judul_laporan', 'like', "%$search%")
                  ->orWhere('id_laporan', 'like', "%$search%")
                  ->orWhere('lokasi_kerusakan', 'like', "%$search%");
            });
        }

        // Filter Kategori
        if ($request->filled('category')) {
            $query->where('id_kategori', $request->category);
        }

        // Filter Status Perbaikan
        if ($request->filled('status')) {
            $query->where('status_perbaikan', $request->status);
        }

        $laporan = $query->paginate(10);
        $kategoris = Kategori::kerusakan()->orderBy('nama_kategori')->get();

        return view('admin.laporan_kerusakan.index', compact('laporan', 'kategoris'));
    }
}
