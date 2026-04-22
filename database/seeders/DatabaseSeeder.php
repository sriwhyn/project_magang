<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
use App\Models\Mahasiswa;
use App\Models\Petugas;
use App\Models\Kategori;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        /*
        |------------------------------------------------------------------
        | Akun Admin
        |------------------------------------------------------------------
        */
        $userAdmin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status_akun' => 'aktif',
            ]
        );
        Admin::firstOrCreate(
            ['id_user' => $userAdmin->id_user],
            [
                'nama' => 'Admin Pusat',
                'lokasi_tugas' => 'Gedung Pusat Administrasi',
            ]
        );

        /*
        |------------------------------------------------------------------
        | Akun Petugas (dibuat oleh admin, email ditentukan admin)
        |------------------------------------------------------------------
        */
        $userPetugas = User::firstOrCreate(
            ['email' => 'petugas@lostandfound.pnp.ac.id'],
            [
                'password' => Hash::make('password'),
                'role' => 'petugas',
                'status_akun' => 'aktif',
            ]
        );
        Petugas::updateOrCreate(
            ['id_user' => $userPetugas->id_user],
            [
                'nama' => 'Budi Santoso',
                'nik' => '1234567890123456',
                'jabatan' => 'Teknisi Listrik',
                'email' => 'petugas@lostandfound.pnp.ac.id',
            ]
        );

        $userPetugas2 = User::firstOrCreate(
            ['email' => 'petugas2@lostandfound.pnp.ac.id'],
            [
                'password' => Hash::make('password'),
                'role' => 'petugas',
                'status_akun' => 'aktif',
            ]
        );
        Petugas::updateOrCreate(
            ['id_user' => $userPetugas2->id_user],
            [
                'nama' => 'Andi Pratama',
                'nik' => '6543210987654321',
                'jabatan' => 'Teknisi AC & Pendingin',
                'email' => 'petugas2@lostandfound.pnp.ac.id',
            ]
        );

        /*
        |------------------------------------------------------------------
        | Akun Mahasiswa (contoh)
        |------------------------------------------------------------------
        */
        $userMhs = User::firstOrCreate(
            ['email' => 'mahasiswa@example.com'],
            [
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'status_akun' => 'aktif',
            ]
        );
        Mahasiswa::firstOrCreate(
            ['id_user' => $userMhs->id_user],
            [
                'nama' => 'Ahmad Fauzan',
                'nim' => '2211082032',
                'nim_verified' => true,
            ]
        );

        /*
        |------------------------------------------------------------------
        | Kategori Kehilangan (barang pribadi)
        |------------------------------------------------------------------
        */
        $kategoriKehilangan = [
            'Elektronik',
            'Kendaraan',
            'Dokumen & Surat',
            'Pakaian & Aksesoris',
            'Tas & Dompet',
            'Kunci',
            'Buku & Alat Tulis',
            'Peralatan Olahraga',
            'Perhiasan',
            'Lainnya',
        ];

        foreach ($kategoriKehilangan as $nama) {
            Kategori::firstOrCreate(
                ['nama_kategori' => $nama],
                ['tipe' => 'kehilangan']
            );
        }

        /*
        |------------------------------------------------------------------
        | Kategori Kerusakan (fasilitas kampus)
        |------------------------------------------------------------------
        */
        $kategoriKerusakan = [
            'Toilet & Kamar Mandi',
            'Lampu & Listrik',
            'AC & Pendingin',
            'Pintu & Jendela',
            'Atap & Plafon',
            'Lift & Eskalator',
            'Jaringan & Internet',
            'Meja & Kursi',
            'Papan Tulis & Proyektor',
            'Pipa & Saluran Air',
            'Lantai & Dinding',
            'Pagar & Gerbang',
            'Fasilitas Lainnya',
        ];

        foreach ($kategoriKerusakan as $nama) {
            Kategori::firstOrCreate(
                ['nama_kategori' => $nama],
                ['tipe' => 'kerusakan']
            );
        }

        $this->command->info('Data awal berhasil dibuat:');
        $this->command->info('- ' . count($kategoriKehilangan) . ' kategori kehilangan');
        $this->command->info('- ' . count($kategoriKerusakan) . ' kategori kerusakan');
        $this->command->info('');
        $this->command->info('Akun login:');
        $this->command->info('  Admin    : admin@example.com / password');
        $this->command->info('  Petugas  : petugas@lostandfound.pnp.ac.id / password');
        $this->command->info('  Mahasiswa: mahasiswa@example.com / password');
    }
}
