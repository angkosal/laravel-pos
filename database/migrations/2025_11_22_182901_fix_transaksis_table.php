<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            if (!Schema::hasColumn('transaksis', 'Nama_Produk')) {
                $table->string('Nama_Produk')->nullable()->after('Tanggal_Transaksi');
            }

            if (!Schema::hasColumn('transaksis', 'Harga_Satuan')) {
                $table->decimal('Harga_Satuan', 12, 2)->nullable()->after('Nama_Produk');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            if (Schema::hasColumn('transaksis', 'Nama_Produk')) {
                $table->dropColumn('Nama_Produk');
            }
            if (Schema::hasColumn('transaksis', 'Harga_Satuan')) {
                $table->dropColumn('Harga_Satuan');
            }
        });
    }
};
