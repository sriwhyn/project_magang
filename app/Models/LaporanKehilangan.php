<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LaporanKehilangan extends Model
{
    use HasFactory;

    protected $table = 'laporan_kehilangan';
    protected $primaryKey = 'id_laporan';

    protected $fillable = [
        'id_user',
        'id_kategori',
        'nama_barang',
        'warna',
        'deskripsi',
        'lokasi_hilang',
        'latitude',
        'longitude',
        'tanggal_hilang',
        'foto',
        'status',
        'merk',
        'tipe_model',
        'nomor_seri',
        'ukuran',
        'ciri_khusus',
    ];

    protected $casts = [
        'tanggal_hilang' => 'date',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'foto' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function barangTemuan()
    {
        return $this->hasMany(BarangTemuan::class, 'id_laporan_kehilangan', 'id_laporan');
    }
}
