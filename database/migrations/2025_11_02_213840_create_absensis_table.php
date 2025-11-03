<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsensisTable extends Migration
{
    /**
     * Jalankan migrasi untuk membuat tabel absensis.
     */
    public function up(): void
    {
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pegawai_id'); // jika ada relasi ke tabel pegawai
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_keluar')->nullable();
            $table->string('status'); // hadir, izin, sakit, alpha
            $table->timestamps();
        });
    }

    /**
     * Hapus tabel absensis jika rollback dilakukan.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
}
