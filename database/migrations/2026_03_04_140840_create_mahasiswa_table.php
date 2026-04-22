<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->id('id_mahasiswa');
            $table->unsignedBigInteger('id_user');
            $table->foreign('id_user')
                ->references('id_user')
                ->on('users')
                ->onDelete('cascade');
            $table->string('nama');
            $table->string('nim')->unique();
            $table->boolean('nim_verified')->default(false);
            $table->string('foto_ktm')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};
