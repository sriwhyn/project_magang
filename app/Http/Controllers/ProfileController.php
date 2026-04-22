<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    // halaman pengaturan akun
    public function settings()
    {
        return view('profile.settings');
    }

    // update password user
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed'],
        ], [
            'current_password.current_password' => 'Password saat ini yang Anda masukkan salah.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.min' => 'Password baru minimal harus 8 karakter.',
        ]);

        $user = Auth::user();
        
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password Anda berhasil diperbarui.');
    }
}
