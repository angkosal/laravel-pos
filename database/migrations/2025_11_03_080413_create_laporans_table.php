<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporans', function (Blueprint $table) {
            $table->id('id_laporan'); // Auto increment primary key
            $table->unsignedBigInteger('id_gaji')->nullable(); // Relasi ke tabel gaji, boleh kosong
            $table->string('periode', 20);
            $table->date('tanggal_cetak');
            $table->float('total_gaji');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporans');
    }
};
