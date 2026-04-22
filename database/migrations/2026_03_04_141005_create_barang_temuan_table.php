<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_temuan', function (Blueprint $table) {
            $table->id('id_barang');

            $table->unsignedBigInteger('id_user_pelapor');
            $table->foreign('id_user_pelapor')
                ->references('id_user')
                ->on('users');

            $table->unsignedBigInteger('id_admin_penerima')->nullable();
            $table->foreign('id_admin_penerima')
                  ->references('id_admin')
                  ->on('admins')
                  ->onDelete('set null');

            $table->unsignedBigInteger('id_laporan_kehilangan')->nullable();
            $table->foreign('id_laporan_kehilangan')
                ->references('id_laporan')
                ->on('laporan_kehilangan')
                ->onDelete('set null');

            $table->unsignedBigInteger('id_kategori');
            $table->foreign('id_kategori')
                ->references('id_kategori')
                ->on('kategori');

            $table->string('nama_barang');
            $table->string('warna')->nullable();
            $table->string('merk')->nullable();
            $table->string('tipe_model')->nullable();
            $table->string('nomor_seri')->nullable();
            $table->string('ukuran')->nullable();
            $table->text('deskripsi_singkat')->nullable();
            $table->string('lokasi_nama');

            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            $table->date('tanggal_ditemukan');
            $table->text('foto')->nullable();

            $table->enum('status_verifikasi', ['belum', 'terverifikasi'])
                ->default('belum');
            $table->enum('status_penyerahan', ['menunggu_diserahkan', 'sudah_diterima'])
                  ->default('menunggu_diserahkan');
            $table->boolean('is_published')->default(false);

            // Deadline & perpanjangan
            $table->datetime('deadline_penyerahan')->nullable();
            $table->text('alasan_perpanjangan')->nullable();
            $table->text('foto_kendala')->nullable();
            $table->string('status_perpanjangan')->nullable();
            $table->integer('poin_reward')->default(0);

            // Status klaim
            $table->enum('status_klaim', ['belum', 'proses', 'selesai'])
                ->default('belum');

            // Flag notifikasi
            $table->boolean('notifikasi_deadline_terkirim')->default(false);
            $table->boolean('notifikasi_reminder_terkirim')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_temuan');
    }
};
