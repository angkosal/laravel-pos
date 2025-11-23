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
    Schema::table('transaksis', function (Blueprint $table) {
        if (!Schema::hasColumn('transaksis', 'harga_satuan')) {
            $table->decimal('harga_satuan', 12, 2)->nullable()->after('Total_Pesanan');
        }
    });
}

public function down(): void
{
    Schema::table('transaksis', function (Blueprint $table) {
        $table->dropColumn('harga_satuan');
    });
}

};
