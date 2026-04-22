<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\Petugas;
use App\Notifications\StatusUpdateNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private function isApiRequest(Request $request): bool
    {
        return $request->wantsJson() || $request->is('api/*');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function showAdminLogin()
    {
        return view('auth.admin_login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        $isApi = $this->isApiRequest($request);

        if (!$user || !Hash::check($request->password, $user->password)) {
            if ($isApi) {
                throw ValidationException::withMessages(['email' => ['Email atau password salah.']]);
            }
            return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
        }

        $user->update(['last_login' => now()]);

        if ($user->role === 'admin') {
            return back()
                ->withErrors(['email' => 'Anda terdaftar sebagai Admin. Silakan gunakan portal khusus Admin untuk login.'])
                ->onlyInput('email');
        }

        if ($user->status_akun === 'nonaktif') {
            return back()
                ->withErrors(['email' => "Mohon ma'af akun anda belum diverifikasi oleh admin. Notifikasi verifikasi akan dikirmkan ke email Anda setelah akun disetujui."])
                ->onlyInput('email');
        }

        if ($isApi) {
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json(['message' => 'Login berhasil', 'data' => $user, 'token' => $token]);
        }

        Auth::login($user);
        $request->session()->regenerate();
        
        return redirect()->route('home');
    }

    public function adminLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
        }

        if ($user->role !== 'admin') {
            return back()->withErrors(['email' => 'Hanya Admin yang dapat menggunakan portal ini.'])->onlyInput('email');
        }

        $user->update(['last_login' => now()]);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }


    public function register(Request $request)
    {
        $isApi = $this->isApiRequest($request);

        $rules = [
            'email' => 'required|email|unique:users,email',
            'role'    => 'required|in:mahasiswa,dosen',
            'no_hp'   => 'nullable|string|regex:/^[0-9]+$/',
            'jurusan' => 'required|string',
            'prodi'   => 'required|string',
        ];

        if ($request->role === 'mahasiswa') {
            $rules['nama']     = 'required|string|max:255';
            $rules['nim']      = 'required|string|digits:10|regex:/^[0-9]+$/|unique:mahasiswa,nim';
            $rules['foto_ktm'] = 'required|image|mimes:jpeg,png,jpg|max:2048';
        } elseif ($request->role === 'dosen') {
            $rules['nama']    = 'required|string|max:255';
            $rules['nip']     = 'required|string|digits:18|regex:/^[0-9]+$/|unique:dosen,nip';
            $rules['foto_id'] = 'required|image|mimes:jpeg,png,jpg|max:2048';
        }

        $rules['password'] = $isApi ? 'required' : 'required|confirmed';

        $request->validate($rules, [
            'nim.digits'  => 'NIM harus terdiri dari 10 digit angka.',
            'nim.regex'   => 'NIM hanya boleh berisi angka.',
            'nim.unique'  => 'NIM sudah terdaftar di sistem.',
            'nip.digits'  => 'NIP harus terdiri dari 18 digit angka.',
            'nip.regex'   => 'NIP hanya boleh berisi angka.',
            'nip.unique'  => 'NIP sudah terdaftar di sistem.',
            'no_hp.regex' => 'Nomor WhatsApp hanya boleh berisi angka (tidak valid jika menggunakan huruf).',
        ]);

        try {
            return \Illuminate\Support\Facades\DB::transaction(function () use ($request, $isApi) {
                $user = User::create([
                    'email'       => $request->email,
                    'password'    => Hash::make($request->password),
                    'role'        => $request->role,
                    'status_akun' => 'nonaktif',
                    'no_hp'       => $request->no_hp,
                ]);

                if ($request->role === 'mahasiswa') {
                    $fotoKtm = null;
                    if ($request->hasFile('foto_ktm')) {
                        $fotoKtm = $request->file('foto_ktm')->store('mahasiswa/ktm', 'public');
                    }

                    Mahasiswa::create([
                        'id_user'      => $user->id_user,
                        'nama'         => $request->nama,
                        'jurusan'      => $request->jurusan,
                        'prodi'        => $request->prodi,
                        'nim'          => $request->nim,
                        'nim_verified' => false,
                        'foto_ktm'     => $fotoKtm,
                    ]);
                }

                if ($request->role === 'dosen') {
                    $fotoId = null;
                    if ($request->hasFile('foto_id')) {
                        $fotoId = $request->file('foto_id')->store('dosen/id_cards', 'public');
                    }

                    Dosen::create([
                        'id_user'      => $user->id_user,
                        'nama'         => $request->nama,
                        'jurusan'      => $request->jurusan,
                        'prodi'        => $request->prodi,
                        'nip'          => $request->nip,
                        'foto_id'      => $fotoId,
                        'nip_verified' => false,
                    ]);
                }

                // Notifikasi ke User (Konfirmasi Pendaftaran)
                try {
                    $user->notify(new StatusUpdateNotification(
                        'Pendaftaran Berhasil',
                        'Akun Anda telah berhasil didaftarkan dan sedang menunggu verifikasi admin.'
                    ));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning("Email register failed for user {$user->email}: " . $e->getMessage());
                }

                // Notifikasi ke Admin
                $this->kirimNotifikasiKeAdmin(
                    'Pendaftaran Akun Baru',
                    'User baru dengan email ' . $user->email . ' (' . ucfirst($user->role) . ') telah mendaftar ke sistem.',
                    route('admin.users.mahasiswa')
                );

                if ($isApi) {
                    return response()->json([
                        'status'  => true,
                        'message' => 'Registrasi berhasil. Akun Anda sedang menunggu verifikasi admin.',
                        'data'    => $user,
                    ]);
                }

                return redirect()->route('login')->with('success', 'Terima kasih, akun Anda telah berhasil didaftarkan. Saat ini akun Anda sedang dalam proses verifikasi oleh admin. Anda belum dapat login sampai proses verifikasi selesai. Silakan tunggu, kami akan mengirimkan notifikasi ke email Anda setelah akun disetujui.');
            });
        } catch (\Exception $e) {
            if ($isApi) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Terjadi kesalahan saat pendaftaran: ' . $e->getMessage(),
                ], 500);
            }
            return back()->withInput()->withErrors(['email' => 'Terjadi kesalahan sistem saat pendaftaran. Silakan coba beberapa saat lagi. (Error: ' . $e->getMessage() . ')']);
        }
    }

    public function logout(Request $request)
    {
        if ($this->isApiRequest($request)) {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logout berhasil']);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function me(Request $request)
    {
        $user = $request->user();
        $user->load(['mahasiswa']);
        return response()->json(['data' => $user]);
    }

    private function kirimNotifikasiKeAdmin($judul, $pesan, $url = '#')
    {
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            try {
                $admin->notify(new StatusUpdateNotification($judul, $pesan, $url));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Admin email failed in Auth: " . $e->getMessage());
            }
        }
    }
}
