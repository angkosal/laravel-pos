<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporans', function (Blueprint $table) {
            $table->id('id_laporan'); // primary key
            $table->string('id_gaji')->unique(); // ganti dari kode_laporan menjadi id_gaji
            $table->string('periode')->nullable()->change();
            $table->date('tanggal_cetak')->nullable();
            $table->decimal('total_gaji', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporans');
    }
};
