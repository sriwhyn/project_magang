<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KlaimBarang extends Model
{
    use HasFactory;

    protected $table = 'klaim_barang';
    protected $primaryKey = 'id_klaim';

    protected $fillable = [
        'id_kategori',
        'id_barang',
        'id_user_pengaju',
        'nama_pemilik',
        'merk',
        'tipe_model',
        'nomor_seri',
        'warna',
        'isi_barang',
        'deskripsi_ciri_khusus',
        'tanggal_hilang',
        'lokasi_hilang',
        'foto_bukti',
        'status_klaim',
        'tanggal_pengajuan',
        'tanggal_verifikasi',
        'skor_kecocokan',
        'catatan_admin',
    ];

    protected $casts = [
        'tanggal_hilang' => 'date',
        'tanggal_pengajuan' => 'datetime',
        'tanggal_verifikasi' => 'datetime',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function barangTemuan()
    {
        return $this->belongsTo(BarangTemuan::class, 'id_barang', 'id_barang');
    }

    public function pengaju()
    {
        return $this->belongsTo(User::class, 'id_user_pengaju', 'id_user');
    }
}
