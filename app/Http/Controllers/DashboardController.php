<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LaporanKehilangan;
use App\Models\LaporanKerusakan;
use App\Models\BarangTemuan;
use App\Models\KlaimBarang;
use App\Models\RiwayatPelanggaran;
use App\Models\User;

class DashboardController extends Controller
{
    // dashboard khusus admin
    public function adminDashboard()
    {
        $user = Auth::user();

        // Stats Global untuk Admin
        $stats = [
            'kehilangan'        => LaporanKehilangan::count(),
            'kerusakan'         => LaporanKerusakan::count(),
            'temuan'            => BarangTemuan::count(),
            'klaim'             => KlaimBarang::count(),
            'klaim_pending'     => KlaimBarang::where('status_klaim', 'menunggu')->count(),
            'kerusakan_pending' => LaporanKerusakan::where('status_perbaikan', 'dilaporkan')->count(),
            'temuan_menunggu'   => BarangTemuan::where('status_penyerahan', 'menunggu_diserahkan')->count(),
            'kehilangan_baru'   => LaporanKehilangan::where('status', 'menunggu')->count(),
        ];

        $recentKehilangan = LaporanKehilangan::with(['user', 'kategori'])
            ->latest()
            ->take(5)
            ->get();

        $recentTemuan = BarangTemuan::where('status_penyerahan', 'menunggu_diserahkan')
            ->with(['pelapor', 'kategori'])
            ->latest()
            ->take(5)
            ->get();

        $recentKlaim = KlaimBarang::where('status_klaim', 'menunggu')
            ->with(['pengaju', 'barangTemuan'])
            ->latest()
            ->take(5)
            ->get();

        $recentKerusakan = LaporanKerusakan::where('status_perbaikan', 'dilaporkan')
            ->with(['pelapor', 'kategori'])
            ->latest()
            ->take(5)
            ->get();

        $notifikasi = $user->unreadNotifications->take(5);

        return view('dashboard.admin', compact(
            'stats', 'recentKehilangan', 'recentTemuan', 'recentKlaim', 'recentKerusakan', 'notifikasi'
        ));
    }

    // dashboard petugas staff
    public function petugasDashboard()
    {
        $user = Auth::user();
        $petugasId = $user->petugas?->id_petugas ?? null;

        // Statistik fokus tugas kerja petugas
        $stats = [
            'total_tugas'    => LaporanKerusakan::where('id_petugas', $petugasId)->count(),
            'tugas_pending'  => LaporanKerusakan::where('id_petugas', $petugasId)->whereNotIn('status_perbaikan', ['selesai'])->count(),
            'tugas_selesai'  => LaporanKerusakan::where('id_petugas', $petugasId)->where('status_perbaikan', 'selesai')->count(),
            'tugas_aktif'    => LaporanKerusakan::where('id_petugas', $petugasId)->where('status_perbaikan', 'dikerjakan')->count(),
        ];

        // Laporan Pekerjaan (Tugas Petugas)
        $recentKerusakan = LaporanKerusakan::where('id_petugas', $petugasId)
            ->with(['pelapor', 'kategori'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.petugas', compact('stats', 'recentKerusakan'));
    }


    // dashboard mahasiswa
    public function mahasiswa()
    {
        $user   = Auth::user();
        $idUser = $user->id_user;

        $stats = [
            'kehilangan' => LaporanKehilangan::where('id_user', $idUser)->count(),
            'kerusakan'  => LaporanKerusakan::where('id_user_pelapor', $idUser)->count(),
            'klaim'      => KlaimBarang::where('id_user_pengaju', $idUser)->count(),
            'temuan'     => BarangTemuan::where('id_user_pelapor', $idUser)->count(),
        ];

        $riwayatPelanggaran = RiwayatPelanggaran::where('id_user', $idUser)->latest()->get();

        $publicTemuan = BarangTemuan::where('status_verifikasi', 'terverifikasi')
            ->where('is_published', true)
            ->where('status_penyerahan', 'sudah_diterima')
            ->where('status_klaim', 'belum')
            ->with('kategori')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.mahasiswa', compact('stats', 'riwayatPelanggaran', 'publicTemuan'));
    }

    // profil mahasiswa
    public function mahasiswaProfile()
    {
        $user   = Auth::user();
        $idUser = $user->id_user;

        $stats = [
            'kehilangan' => LaporanKehilangan::where('id_user', $idUser)->count(),
            'kerusakan'  => LaporanKerusakan::where('id_user_pelapor', $idUser)->count(),
            'klaim'      => KlaimBarang::where('id_user_pengaju', $idUser)->count(),
            'temuan'     => BarangTemuan::where('id_user_pelapor', $idUser)->count(),
        ];

        $riwayatPelanggaran = RiwayatPelanggaran::where('id_user', $idUser)->latest()->get();

        $poinRewards = BarangTemuan::where('id_user_pelapor', $idUser)
            ->where('poin_reward', '>', 0)
            ->select('nama_barang', 'poin_reward', 'updated_at')
            ->latest('updated_at')->get()
            ->map(fn($item) => [
                'type'        => 'reward',
                'description' => "Reward penyerahan: {$item->nama_barang}",
                'poin'        => $item->poin_reward,
                'date'        => $item->updated_at,
            ]);

        $poinPenalties = RiwayatPelanggaran::where('id_user', $idUser)
            ->select('jenis_pelanggaran', 'deskripsi', 'poin', 'tanggal')
            ->latest('tanggal')->get()
            ->map(fn($item) => [
                'type'        => 'penalty',
                'description' => strtoupper($item->jenis_pelanggaran) . ': ' . $item->deskripsi,
                'poin'        => $item->poin,
                'date'        => \Carbon\Carbon::parse($item->tanggal),
            ]);

        $riwayatPoin = $poinRewards->merge($poinPenalties)->sortByDesc('date')->values();

        $recentKehilangan = LaporanKehilangan::where('id_user', $idUser)->with('kategori')->latest()->take(5)->get();
        $recentTemuan = BarangTemuan::where('id_user_pelapor', $idUser)->with('kategori')->latest()->take(5)->get();

        $publicTemuan = BarangTemuan::where('status_verifikasi', 'terverifikasi')
            ->where('is_published', true)
            ->where('status_penyerahan', 'sudah_diterima')
            ->whereIn('status_klaim', ['belum'])
            ->with('kategori')
            ->latest()
            ->take(5)
            ->get();

        // Klaim Saya: semua klaim milik user
        $klaimSaya = KlaimBarang::where('id_user_pengaju', $idUser)
            ->with(['barangTemuan', 'kategori'])
            ->latest()
            ->get();

        $leaderboard = User::where('role', 'mahasiswa')
            ->orderByDesc('poin')
            ->take(5)
            ->get();

        return view('dashboard.mahasiswa_profil', compact(
            'stats', 'riwayatPelanggaran', 'riwayatPoin', 'recentKehilangan', 'recentTemuan', 'publicTemuan', 'klaimSaya', 'leaderboard'
        ));
    }

    // riwayat notifikasi
    public function notifications()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->paginate(20);

        return view('notifications.index', compact('notifications'));
    }
}
