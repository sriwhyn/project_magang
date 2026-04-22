<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RiwayatPelanggaran extends Model
{
    use HasFactory;

    protected $table = 'riwayat_pelanggaran';
    protected $primaryKey = 'id_pelanggaran';

    protected $fillable = [
        'id_user',
        'jenis_pelanggaran',
        'tingkat',
        'poin',
        'deskripsi',
        'tanggal',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
