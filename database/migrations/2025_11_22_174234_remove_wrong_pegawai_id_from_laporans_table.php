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
    Schema::table('laporans', function (Blueprint $table) {
        if (Schema::hasColumn('laporans', 'pegawai_id')) {
            $table->dropColumn('pegawai_id');
        }
    });
}

public function down(): void
{
    Schema::table('laporans', function (Blueprint $table) {
        if (!Schema::hasColumn('laporans', 'pegawai_id')) {
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawais')->nullOnDelete();
        }
    });
}

};
