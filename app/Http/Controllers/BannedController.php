<?php

namespace App\Http\Controllers;

use App\Models\PengajuanBanding;
use App\Models\User;
use App\Notifications\StatusUpdateNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BannedController extends Controller
{
    public function index()
    {
        if (Auth::user()->status_akun == 'aktif') {
            return redirect('/');
        }
        
        // Cari banding yang ada untuk menampilkan status
        $banding = PengajuanBanding::where('id_user', Auth::id())->latest()->first();
        return view('front.banned', compact('banding'));
    }

    public function store(Request $request)
    {
        $request->validate(['alasan' => 'required|string|max:1000']);
        
        $banding = PengajuanBanding::create([
            'id_user' => Auth::id(),
            'alasan' => $request->alasan,
            'status' => 'menunggu'
        ]);

        // notifikasi ke admin
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            /** @var \App\Models\User $admin */
            $admin->notify(new StatusUpdateNotification(
                'Pengajuan Banding Baru',
                'User ' . (Auth::user()->mahasiswa?->nama ?? Auth::user()->email) . ' mengajukan banding atas pemblokiran akun.',
                route('admin.users.mahasiswa')
            ));
        }

        return back()->with('success', 'Pengajuan banding berhasil dikirim. Silakan tunggu respon dari tim Admin.');
    }
}
