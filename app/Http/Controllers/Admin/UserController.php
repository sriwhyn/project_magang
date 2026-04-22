<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\StatusUpdateNotification;

class UserController extends Controller
{
    // manajemen mahasiswa
    public function indexMahasiswa()
    {
        $mahasiswas = User::whereIn('role', ['mahasiswa'])
            ->with(['mahasiswa', 'pengajuanBanding' => function ($q) {
                $q->latest();
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.users.mahasiswa', compact('mahasiswas'));
    }

    // manajemen dosen
    public function indexDosen()
    {
        $dosens = User::whereIn('role', ['dosen'])
            ->with(['dosen'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.users.dosen', compact('dosens'));
    }

    // verifikasi KTM
    public function auditIndex()
    {
        $pendingUsers = User::whereIn('role', ['mahasiswa', 'dosen'])
            ->where('status_akun', 'nonaktif')
            ->with(['mahasiswa', 'dosen'])
            ->orderBy('created_at', 'asc')
            ->paginate(15);

        return view('admin.users.verify', compact('pendingUsers'));
    }

    // update status dan skor
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'status_akun' => 'required|in:aktif,nonaktif',
            'poin' => 'required|integer|min:0',
        ]);

        $oldStatus = $user->status_akun;

        if ($request->status_akun == 'aktif' && $user->status_akun == 'nonaktif') {
            \App\Models\PengajuanBanding::where('id_user', $user->id_user)
                ->where('status', 'menunggu')
                ->update(['status' => 'disetujui']);

            // Notifikasi aktivasi akun
            try {
                $user->notify(new StatusUpdateNotification(
                    'Akun Diaktifkan',
                    'Selamat! Akun Anda telah diaktifkan kembali oleh Admin. Anda sekarang dapat mengakses semua fitur sistem.',
                    route('mahasiswa.dashboard')
                ));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Email error (activation): " . $e->getMessage());
            }
        } elseif ($request->status_akun == 'nonaktif' && $user->status_akun == 'aktif') {
            // Notifikasi penonaktifan akun
            try {
                $user->notify(new StatusUpdateNotification(
                    'Akun Dinonaktifkan',
                    'Akun Anda telah dinonaktifkan sementara oleh Admin. Silakan hubungi admin jika ini adalah kesalahan.',
                    route('banned.index')
                ));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Email error (ban): " . $e->getMessage());
            }
        }

        $user->update([
            'status_akun' => $request->status_akun,
            'poin' => $request->poin,
        ]);

        return redirect()->back()->with('success', 'Status akun dan Poin berhasil diperbarui.');
    }

    // verifikasi NIM
    public function verifyNim(User $user)
    {
        if ($user->role === 'mahasiswa') {
            if (!$user->mahasiswa) {
                return back()->with('error', 'Data mahasiswa tidak ditemukan.');
            }
            $user->mahasiswa->update([
                'nim_verified' => !$user->mahasiswa->nim_verified,
            ]);
            $isVerified = $user->mahasiswa->nim_verified;
            $idNumber = $user->mahasiswa->nim;
        } elseif ($user->role === 'dosen') {
            if (!$user->dosen) {
                return back()->with('error', 'Data dosen tidak ditemukan.');
            }
            $user->dosen->update([
                'nip_verified' => !$user->dosen->nip_verified,
            ]);
            $isVerified = $user->dosen->nip_verified;
            $idNumber = $user->dosen->nip;
        } else {
            return back()->with('error', 'Role pengguna tidak valid untuk verifikasi.');
        }

        if ($isVerified) {
            // Aktifkan akun jika sebelumnya nonaktif
            if ($user->status_akun === 'nonaktif') {
                $user->update(['status_akun' => 'aktif']);
            }

            try {
                $user->notify(new StatusUpdateNotification(
                    'Akun Disetujui',
                    'Selamat! Akun Anda telah berhasil diverifikasi oleh admin. Sekarang Anda sudah dapat login dan menggunakan sistem.',
                    route('home')
                ));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Email error (verification): " . $e->getMessage());
            }
        }

        $status = $isVerified ? 'terverifikasi (Audit Berhasil)' : 'dibatalkan verifikasinya';
        $label = $user->role === 'mahasiswa' ? 'NIM' : 'NIP';
        return back()->with('success', "{$label} {$idNumber} berhasil {$status}.");
    }

    // blokir pengguna
    public function banUser(User $user)
    {
        $user->update(['status_akun' => 'nonaktif']);

        // Notifikasi Blokir karena Audit Gagal
        try {
            $user->notify(new StatusUpdateNotification(
                'Audit Gagal & Akun Diblokir',
                'Audit data KTM Anda gagal karena ketidaksesuaian informasi. Akun Anda telah diblokir sementara.',
                route('banned.index')
            ));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Email error (reject audit): " . $e->getMessage());
        }

        return back()->with('success', "Akun " . ($user->mahasiswa->nama ?? $user->email) . " telah DIBAN karena data tidak sesuai.");
    }

    // tolak banding
    public function tolakBanding(User $user)
    {
        \App\Models\PengajuanBanding::where('id_user', $user->id_user)
            ->where('status', 'menunggu')
            ->update(['status' => 'ditolak']);

        // Notifikasi Banding Ditolak
        try {
            $user->notify(new StatusUpdateNotification(
                'Pengajuan Banding Ditolak',
                'Maaf, pengajuan banding Anda telah ditolak oleh Admin setelah peninjauan kembali.',
                route('banned.index')
            ));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Email error (reject banding): " . $e->getMessage());
        }

        return back()->with('success', 'Pengajuan banding berhasil ditolak.');
    }
}
