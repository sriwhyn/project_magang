<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Petugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PetugasController extends Controller
{
    
    private function generateEmail(string $nama): string
    {
        $cleanName = strtolower(trim($nama));
        $cleanName = preg_replace('/[^a-z0-9\s]/', '', $cleanName);
        $cleanName = preg_replace('/\s+/', '.', $cleanName);

        $domain = '@petugas.pnp.ac.id';
        $email = $cleanName . $domain;

        // duplikat
        $counter = 1;
        while (User::where('email', $email)->exists()) {
            $counter++;
            $email = $cleanName . '.' . $counter . $domain;
        }

        return $email;
    }

    public function index()
    {
        $petugasList = Petugas::with('user')->get();
        return view('admin.petugas.index', compact('petugasList'));
    }

    public function create()
    {
        return view('admin.petugas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:255',
            'jabatan'  => 'required|string|max:255',
            'password' => 'required|string',
        ]);

        // email
        $email = $this->generateEmail($request->nama);
        $rawPassword = $request->password;

        $user = User::create([
            'email'       => $email,
            'password'    => Hash::make($rawPassword),
            'role'        => 'petugas',
            'status_akun' => 'aktif',
        ]);

        Petugas::create([
            'id_user' => $user->id_user,
            'nama'    => $request->nama,
            'jabatan' => $request->jabatan,
        ]);

        return redirect()->route('admin.akun_petugas.index')
            ->with('success', 'Akun petugas berhasil dibuat.')
            ->with('credential_nama', $request->nama)
            ->with('credential_email', $email)
            ->with('credential_password', $rawPassword);
    }

    public function edit($id)
    {
        $petugas = Petugas::findOrFail($id);
        return view('admin.petugas.edit', compact('petugas'));
    }

    public function update(Request $request, $id)
    {
        $petugas = Petugas::findOrFail($id);

        $request->validate([
            'nama'    => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
        ]);

        // email
        $namaLama = $petugas->nama;
        $namaBaru = $request->nama;

        if (strtolower(trim($namaLama)) !== strtolower(trim($namaBaru))) {
            // ubah
            $emailBaru = $this->generateEmailExcluding($namaBaru, $petugas->id_user);
            $petugas->user->update(['email' => $emailBaru]);
        }

        if ($request->filled('password')) {
            $petugas->user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        $petugas->update([
            'nama'    => $request->nama,
            'jabatan' => $request->jabatan,
        ]);

        return redirect()->route('admin.akun_petugas.index')
            ->with('success', 'Akun petugas berhasil diperbarui.');
    }

   
    private function generateEmailExcluding(string $nama, int $excludeUserId): string
    {
        $cleanName = strtolower(trim($nama));
        $cleanName = preg_replace('/[^a-z0-9\s]/', '', $cleanName);
        $cleanName = preg_replace('/\s+/', '.', $cleanName);

        $domain = '@petugas.pnp.ac.id';
        $email = $cleanName . $domain;

        $counter = 1;
        while (User::where('email', $email)->where('id_user', '!=', $excludeUserId)->exists()) {
            $counter++;
            $email = $cleanName . '.' . $counter . $domain;
        }

        return $email;
    }

    public function destroy($id)
    {
        $petugas = Petugas::findOrFail($id);
        if ($petugas->user) {
            $petugas->user->delete();
        }
        $petugas->delete();

        return redirect()->route('admin.akun_petugas.index')
            ->with('success', 'Akun petugas berhasil dihapus.');
    }
}
