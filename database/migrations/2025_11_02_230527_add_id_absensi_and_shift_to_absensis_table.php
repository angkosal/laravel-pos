<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdAbsensiAndShiftToAbsensisTable extends Migration
{
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->string('id_absensi')->unique()->after('id'); // ID Absensi unik
            $table->string('shift')->nullable()->after('jam_keluar'); // Shift bisa null
        });
    }

    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn(['id_absensi', 'shift']);
        });
    }
}
