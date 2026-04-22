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
        Schema::table('pengajuan_bandings', function (Blueprint $table) {
            $table->unsignedBigInteger('id_barang')->nullable()->after('id_user');
            $table->foreign('id_barang')->references('id_barang')->on('barang_temuan')->onDelete('cascade');
            $table->string('jenis_banding')->default('perpanjangan_deadline')->after('id_barang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_bandings', function (Blueprint $table) {
            $table->dropForeign(['id_barang']);
            $table->dropColumn(['id_barang', 'jenis_banding']);
        });
    }
};
