<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'mahasiswa';
    protected $primaryKey = 'id_mahasiswa';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'nama',
        'jurusan',
        'prodi',
        'nim',
        'nim_verified',
        'foto_ktm',
    ];

    protected $casts = [
        'nim_verified' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
