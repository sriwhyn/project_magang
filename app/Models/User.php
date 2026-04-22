<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'id_user';

    protected $fillable = [
        'email',
        'password',
        'role',
        'status_akun',
        'poin',
        'no_hp',
        'foto_profil',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'last_login' => 'datetime',
    ];

    public function mahasiswa()
    {
        return $this->hasOne(Mahasiswa::class, 'id_user', 'id_user');
    }

    public function dosen()
    {
        return $this->hasOne(Dosen::class, 'id_user', 'id_user');
    }

    public function petugas()
    {
        return $this->hasOne(Petugas::class, 'id_user', 'id_user');
    }

    public function admin()
    {
        return $this->hasOne(Admin::class, 'id_user', 'id_user');
    }

    public function riwayatPelanggaran()
    {
        return $this->hasMany(RiwayatPelanggaran::class, 'id_user', 'id_user');
    }

    public function barangTemuan()
    {
        return $this->hasMany(BarangTemuan::class, 'id_user_pelapor', 'id_user');
    }

    public function laporanKehilangan()
    {
        return $this->hasMany(LaporanKehilangan::class, 'id_user', 'id_user');
    }

    public function laporanKerusakan()
    {
        return $this->hasMany(LaporanKerusakan::class, 'id_user_pelapor', 'id_user');
    }

    public function klaimBarang()
    {
        return $this->hasMany(KlaimBarang::class, 'id_user_pengaju', 'id_user');
    }

    public function pengajuanBanding()
    {
        return $this->hasMany(PengajuanBanding::class, 'id_user', 'id_user');
    }
}
