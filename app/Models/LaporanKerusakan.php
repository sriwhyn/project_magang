<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LaporanKerusakan extends Model
{
    use HasFactory;

    protected $table = 'laporan_kerusakan';
    protected $primaryKey = 'id_laporan';

    protected $fillable = [
        'id_user_pelapor',
        'id_kategori',
        'judul_laporan',
        'deskripsi',
        'lokasi_kerusakan',
        'tanggal_lapor',
        'gambar',
        'status_perbaikan',
        'id_petugas',
        'catatan_petugas',
        'foto_bukti_pengerjaan',
    ];

    protected $casts = [
        'tanggal_lapor' => 'date',
        'gambar' => 'array',
        'foto_bukti_pengerjaan' => 'array',
    ];

    public function pelapor()
    {
        return $this->belongsTo(User::class, 'id_user_pelapor', 'id_user');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function petugas()
    {
        return $this->belongsTo(Petugas::class, 'id_petugas', 'id_petugas');
    }
}
