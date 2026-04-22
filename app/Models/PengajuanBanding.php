<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanBanding extends Model
{
    protected $table = 'pengajuan_bandings';
    protected $primaryKey = 'id_pengajuan';
    
    protected $fillable = [
        'id_user',
        'id_barang',
        'jenis_banding',
        'alasan',
        'foto',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function barangTemuan()
    {
        return $this->belongsTo(BarangTemuan::class, 'id_barang', 'id_barang');
    }
}
