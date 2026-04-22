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
        Schema::create('riwayat_pelanggaran', function (Blueprint $table) {
            $table->id('id_pelanggaran');

            $table->unsignedBigInteger('id_user');
            $table->foreign('id_user')
                ->references('id_user')
                ->on('users');

            $table->string('jenis_pelanggaran');

            $table->enum('tingkat', ['ringan', 'sedang', 'berat']);
            $table->integer('poin');

            $table->text('deskripsi')->nullable();
            $table->date('tanggal');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_pelanggaran');
    }
};
