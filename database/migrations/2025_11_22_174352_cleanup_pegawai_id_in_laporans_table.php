<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporans', function (Blueprint $table) {
            // Hanya hapus foreign key jika benar-benar ada
            if (Schema::hasColumn('laporans', 'pegawai_id')) {
                try {
                    $table->dropForeign(['pegawai_id']);
                } catch (\Exception $e) {
                    // Abaikan error jika foreign key sudah tidak ada
                }
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
