<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporans', function (Blueprint $table) {
            $table->id('id_laporan'); // primary key, jangan diubah
            $table->string('kode_laporan')->unique(); // kode yang bisa diedit
            $table->string('id_gaji');
            $table->string('periode');
            $table->date('tanggal_cetak')->nullable();
            $table->decimal('total_gaji', 15, 2)->default(0);
            $table->timestamps();
        });

    }

    public function up()
{
    Schema::table('laporans', function (Blueprint $table) {
        $table->string('kode_laporan')->unique()->after('id_laporan');
    });
}

public function down()
{
    Schema::table('laporans', function (Blueprint $table) {
        $table->dropColumn('kode_laporan');
    });
}

