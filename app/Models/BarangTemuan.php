<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BarangTemuan extends Model
{
    use HasFactory;

    protected $table = 'barang_temuan';
    protected $primaryKey = 'id_barang';

    protected $fillable = [
        'id_user_pelapor',
        'id_petugas_penerima',
        'id_kategori',
        'id_laporan_kehilangan',
        'nama_barang',
        'warna',
        'deskripsi_singkat',
        'lokasi_nama',
        'latitude',
        'longitude',
        'tanggal_ditemukan',
        'foto',
        'status_verifikasi',
        'status_klaim',
        'status_penyerahan',
        'is_published',
        'id_admin_penerima',
        'deadline_penyerahan',
        'alasan_perpanjangan',
        'foto_kendala',
        'status_perpanjangan',
        'poin_reward',
        'merk',
        'tipe_model',
        'nomor_seri',
        'ukuran',
        'id_laporan_kehilangan',
        'notifikasi_deadline_terkirim',
        'notifikasi_reminder_terkirim',
    ];

    protected $casts = [
        'tanggal_ditemukan' => 'date',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'foto' => 'array',
        'is_published' => 'boolean',
        'deadline_penyerahan' => 'datetime',
        'notifikasi_deadline_terkirim' => 'boolean',
        'notifikasi_reminder_terkirim' => 'boolean',
    ];

    public function pelapor()
    {
        return $this->belongsTo(User::class, 'id_user_pelapor', 'id_user');
    }

    public function petugasPenerima()
    {
        return $this->belongsTo(Petugas::class, 'id_petugas_penerima', 'id_petugas');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function klaimBarang()
    {
        return $this->hasMany(KlaimBarang::class, 'id_barang', 'id_barang');
    }

    public function adminPenerima()
    {
        return $this->belongsTo(Admin::class, 'id_admin_penerima', 'id_admin');
    }

    public function laporanKehilangan()
    {
        return $this->belongsTo(LaporanKehilangan::class, 'id_laporan_kehilangan', 'id_laporan');
    }

    public function pengajuanBanding()
    {
        return $this->hasMany(PengajuanBanding::class, 'id_barang', 'id_barang');
    }
}
