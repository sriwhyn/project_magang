<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_kehilangan', function (Blueprint $table) {
            $table->id('id_laporan');

            $table->unsignedBigInteger('id_user');
            $table->foreign('id_user')
                ->references('id_user')
                ->on('users');

            $table->unsignedBigInteger('id_kategori');
            $table->foreign('id_kategori')
                ->references('id_kategori')
                ->on('kategori');

            $table->string('nama_barang');
            $table->string('warna')->nullable();
            $table->string('merk')->nullable();
            $table->string('tipe_model')->nullable();
            $table->string('ukuran')->nullable();
            $table->string('nomor_seri')->nullable();
            $table->text('ciri_khusus')->nullable();
            $table->text('deskripsi')->nullable();

            $table->string('lokasi_hilang')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            $table->date('tanggal_hilang');
            $table->text('foto')->nullable();

            $table->string('status')
                ->default('menunggu');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_kehilangan');
    }
};
