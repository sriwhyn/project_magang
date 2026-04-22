<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';
    protected $primaryKey = 'id_kategori';

    protected $fillable = [
        'nama_kategori',
        'tipe',
    ];

    /**
     * Scope: hanya kategori untuk laporan kehilangan.
     */
    public function scopeKehilangan($query)
    {
        return $query->whereIn('tipe', ['kehilangan', 'semua']);
    }

    /**
     * Scope: hanya kategori untuk laporan kerusakan.
     */
    public function scopeKerusakan($query)
    {
        return $query->whereIn('tipe', ['kerusakan', 'semua']);
    }

    public function barangTemuan()
    {
        return $this->hasMany(BarangTemuan::class, 'id_kategori', 'id_kategori');
    }

    public function laporanKehilangan()
    {
        return $this->hasMany(LaporanKehilangan::class, 'id_kategori', 'id_kategori');
    }

    public function laporanKerusakan()
    {
        return $this->hasMany(LaporanKerusakan::class, 'id_kategori', 'id_kategori');
    }

    public function klaimBarang()
    {
        return $this->hasMany(KlaimBarang::class, 'id_kategori', 'id_kategori');
    }
}