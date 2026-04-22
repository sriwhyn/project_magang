<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('laporan_kerusakan', function (Blueprint $table) {
            $table->id('id_laporan');

            $table->unsignedBigInteger('id_user_pelapor');
            $table->foreign('id_user_pelapor')
                ->references('id_user')
                ->on('users');

            $table->unsignedBigInteger('id_kategori');
            $table->foreign('id_kategori')
                ->references('id_kategori')
                ->on('kategori');

            $table->string('judul_laporan');
            $table->text('deskripsi');

            $table->string('lokasi_kerusakan');
            $table->date('tanggal_lapor');
            $table->text('gambar')->nullable();

            $table->string('status_perbaikan')->default('dilaporkan');
            $table->string('foto_bukti_pengerjaan')->nullable();

            $table->unsignedBigInteger('id_petugas')->nullable();
            $table->foreign('id_petugas')
                ->references('id_petugas')
                ->on('petugas');

            $table->text('catatan_petugas')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_kerusakan');
    }
};
