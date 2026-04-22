<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('klaim_barang', function (Blueprint $table) {
            $table->id('id_klaim');

            $table->unsignedBigInteger('id_kategori');
            $table->foreign('id_kategori')
                ->references('id_kategori')
                ->on('kategori');

            $table->unsignedBigInteger('id_barang');
            $table->foreign('id_barang')
                ->references('id_barang')
                ->on('barang_temuan');

            $table->unsignedBigInteger('id_user_pengaju');
            $table->foreign('id_user_pengaju')
                ->references('id_user')
                ->on('users');

            $table->string('nama_pemilik');
            $table->string('merk')->nullable();
            $table->string('tipe_model')->nullable();
            $table->string('nomor_seri')->nullable();
            $table->string('warna')->nullable();

            $table->text('isi_barang')->nullable();
            $table->text('deskripsi_ciri_khusus')->nullable();

            $table->date('tanggal_hilang');
            $table->string('lokasi_hilang');
            $table->string('foto_bukti')->nullable();

            $table->enum('status_klaim', ['menunggu', 'disetujui', 'ditolak'])
                ->default('menunggu');

            // Verification
            $table->integer('skor_kecocokan')->nullable();
            $table->text('catatan_admin')->nullable();

            $table->timestamp('tanggal_pengajuan');
            $table->timestamp('tanggal_verifikasi')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('klaim_barang');
    }
};
