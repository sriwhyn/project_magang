<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Petugas extends Model
{
    use HasFactory;

    protected $table = 'petugas';
    protected $primaryKey = 'id_petugas';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'nik',
        'nama',
        'jabatan',
        'lokasi_tugas',
        'email',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function laporanKerusakan()
    {
        return $this->hasMany(LaporanKerusakan::class, 'id_petugas', 'id_petugas');
    }
}
