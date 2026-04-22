<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanKehilanganController;
use App\Http\Controllers\LaporanKerusakanController;
use App\Http\Controllers\BarangTemuanController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\KlaimBarangController;
use App\Http\Controllers\Admin\PetugasController;
use App\Http\Controllers\BannedController;
use App\Http\Controllers\ProfileController;
use App\Models\LaporanKehilangan;
use App\Models\BarangTemuan;
use App\Models\LaporanKerusakan;

// home
Route::get('/', function () {
    $kehilangans = LaporanKehilangan::query()
        ->with('kategori')
        ->latest()
        ->take(3)
        ->get();

    $temuans = BarangTemuan::query()
        ->where([
            ['status_verifikasi', '=', 'terverifikasi'],
            ['is_published', '=', true],
            ['status_penyerahan', '=', 'sudah_diterima'],
        ])
        ->with('kategori')
        ->latest()
        ->take(3)
        ->get();

    $kerusakans = LaporanKerusakan::query()
        ->with(['kategori', 'pelapor'])
        ->latest()
        ->take(3)
        ->get();

    return view('welcome', compact('kehilangans', 'temuans', 'kerusakans'));
})->name('home');

// tamu
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.post');

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// landing
Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/laporan-kehilangan', [LaporanKehilanganController::class, 'mahasiswaIndex'])->name('kehilangan.index');
    Route::get('/laporan-kerusakan', [LaporanKerusakanController::class, 'mahasiswaIndex'])->name('kerusakan.index');
    Route::get('/barang-temuan', [BarangTemuanController::class, 'mahasiswaIndex'])->name('temuan.index');
});

// auth
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // pengaturan
    Route::get('/pengaturan', [ProfileController::class, 'settings'])->name('profile.settings');
    Route::post('/pengaturan/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // blokir
    Route::get('/banned', [BannedController::class, 'index'])->name('banned.index');
    Route::post('/banned', [BannedController::class, 'store'])->name('banned.store');

    // notif
    Route::get('/notifications/{id}/read', function ($id) {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();
        return response()->json(['ok' => true]);
    })->name('notifications.read');

    Route::get('/notifications/read-all', function () {
        Auth::user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.readAll');

    Route::get('/notifications', [DashboardController::class, 'notifications'])->name('notifications.index');



    // mahasiswa
    Route::middleware('role:mahasiswa,dosen,petugas,admin')->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'mahasiswaProfile'])->name('dashboard');
        Route::get('/profil', [DashboardController::class, 'mahasiswaProfile'])->name('profile');

        // Laporan Kehilangan (Actions)
        Route::get('/laporan-kehilangan/create', [LaporanKehilanganController::class, 'mahasiswaCreate'])->name('kehilangan.create');
        Route::post('/laporan-kehilangan', [LaporanKehilanganController::class, 'store'])->name('kehilangan.store');
        Route::get('/laporan-kehilangan/{id}', [LaporanKehilanganController::class, 'show'])->name('kehilangan.show');
        Route::delete('/laporan-kehilangan/{id}', [LaporanKehilanganController::class, 'destroy'])->name('kehilangan.destroy');
        Route::get('/laporan-kehilangan/{id}/mark-found', [LaporanKehilanganController::class, 'markFound'])->name('kehilangan.markFound');

        // Laporan Kerusakan (Actions)
        Route::get('/laporan-kerusakan/create', [LaporanKerusakanController::class, 'mahasiswaCreate'])->name('kerusakan.create');
        Route::post('/laporan-kerusakan', [LaporanKerusakanController::class, 'store'])->name('kerusakan.store');
        Route::get('/laporan-kerusakan/{laporan_kerusakan}', [LaporanKerusakanController::class, 'show'])->name('kerusakan.show');

        // Barang Temuan (Actions)
        Route::get('/barang-temuan/create', [BarangTemuanController::class, 'mahasiswaCreate'])->name('temuan.create');
        Route::post('/barang-temuan', [BarangTemuanController::class, 'store'])->name('temuan.store');
        Route::get('/barang-temuan/{barang_temuan}', [BarangTemuanController::class, 'show'])->name('temuan.show');
        Route::delete('/barang-temuan/{barang_temuan}', [BarangTemuanController::class, 'destroy'])->name('temuan.destroy');
        Route::post('/barang-temuan/{barang_temuan}/perpanjangan', [BarangTemuanController::class, 'requestPerpanjangan'])->name('temuan.perpanjangan');
        Route::post('/barang-temuan/{barang_temuan}/banding', [BarangTemuanController::class, 'submitBanding'])->name('temuan.banding.submit');

        // Klaim Barang
        Route::get('/klaim-barang', [KlaimBarangController::class, 'mahasiswaIndex'])->name('klaim.index');
        Route::get('/klaim-barang/create', [KlaimBarangController::class, 'mahasiswaCreate'])->name('klaim.create');
        Route::post('/klaim-barang', [KlaimBarangController::class, 'store'])->name('klaim.store');
        Route::get('/klaim-barang/{klaim_barang}', [KlaimBarangController::class, 'show'])->name('klaim.show');
    });

    // admin
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
        
        // kategori
        Route::resource('kategori', KategoriController::class)->except(['show']);
        Route::resource('/akun-petugas', PetugasController::class)->names('akun_petugas')->except(['show']);
        Route::get('/users/mahasiswa', [UserController::class, 'indexMahasiswa'])->name('users.mahasiswa');
        Route::get('/users/dosen', [UserController::class, 'indexDosen'])->name('users.dosen');
        Route::put('/users/{user}', [UserController::class, 'updateUser'])->name('users.update');
        Route::post('/users/{user}/tolak-banding', [UserController::class, 'tolakBanding'])->name('users.tolak_banding');
        Route::post('/users/{user}/verify-nim', [UserController::class, 'verifyNim'])->name('users.verify_nim');

        // audit
        Route::get('/verifikasi-akun', [UserController::class, 'auditIndex'])->name('users.verify.index');
        Route::post('/verifikasi-akun/{user}/activate', [UserController::class, 'verifyNim'])->name('users.verify.activate');
        Route::post('/verifikasi-akun/{user}/reject', [UserController::class, 'banUser'])->name('users.verify.reject');
    });

    // petugas
    Route::middleware('role:admin,petugas')->prefix('petugas')->name('petugas.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'petugasDashboard'])->name('dashboard');

        // Laporan Kehilangan
        Route::get('/laporan-kehilangan', [LaporanKehilanganController::class, 'petugasIndex'])->name('kehilangan.index');
        Route::get('/laporan-kehilangan/{laporan_kehilangan}', [LaporanKehilanganController::class, 'show'])->name('kehilangan.show');
        Route::put('/laporan-kehilangan/{laporan_kehilangan}', [LaporanKehilanganController::class, 'update'])->name('kehilangan.update');

        // Laporan Kerusakan
        Route::get('/laporan-kerusakan', [LaporanKerusakanController::class, 'petugasIndex'])->name('kerusakan.index');
        Route::get('/laporan-kerusakan/{laporan_kerusakan}', [LaporanKerusakanController::class, 'show'])->name('kerusakan.show');
        Route::put('/laporan-kerusakan/{laporan_kerusakan}', [LaporanKerusakanController::class, 'update'])->name('kerusakan.update');

        // Barang Temuan
        Route::get('/barang-temuan', [BarangTemuanController::class, 'petugasIndex'])->name('temuan.index');
        Route::get('/barang-temuan/create', [BarangTemuanController::class, 'petugasCreate'])->name('temuan.create');
        Route::post('/barang-temuan', [BarangTemuanController::class, 'petugasStore'])->name('temuan.store');
        Route::get('/barang-temuan/{barang_temuan}', [BarangTemuanController::class, 'show'])->name('temuan.show');
        Route::put('/barang-temuan/{barang_temuan}', [BarangTemuanController::class, 'update'])->name('temuan.update');
        Route::post('/barang-temuan/{barang_temuan}/deadline', [BarangTemuanController::class, 'setDeadline'])->name('temuan.deadline');
        Route::post('/barang-temuan/{barang_temuan}/perpanjangan/respond', [BarangTemuanController::class, 'respondPerpanjangan'])->name('temuan.perpanjangan.respond');
        Route::post('/barang-temuan/{barang_temuan}/penipuan', [BarangTemuanController::class, 'markPenipuan'])->name('temuan.penipuan');
        Route::post('/barang-temuan/banding/{id_banding}/proses', [BarangTemuanController::class, 'approveBanding'])->name('temuan.banding.proses');

        // Klaim Barang
        Route::get('/klaim-barang', [KlaimBarangController::class, 'petugasIndex'])->name('klaim.index');
        Route::get('/klaim-barang/{klaim_barang}', [KlaimBarangController::class, 'show'])->name('klaim.show');
        Route::put('/klaim-barang/{klaim_barang}', [KlaimBarangController::class, 'update'])->name('klaim.update');
    });

});
