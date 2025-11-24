<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {

            if (!Schema::hasColumn('transaksis', 'id_transaksi')) {
                $table->string('id_transaksi')->nullable()->after('id');
            }

            if (!Schema::hasColumn('transaksis', 'id_menu')) {
                $table->string('id_menu')->nullable()->after('id_transaksi');
            }

            if (!Schema::hasColumn('transaksis', 'Nama_Pegawai')) {
                $table->string('Nama_Pegawai')->nullable()->after('id_menu');
            }

            if (!Schema::hasColumn('transaksis', 'Nama_Produk')) {
                $table->string('Nama_Produk')->nullable()->after('Nama_Pegawai');
            }

            if (!Schema::hasColumn('transaksis', 'Total_Pesanan')) {
                $table->integer('Total_Pesanan')->nullable()->after('Nama_Produk');
            }

            if (!Schema::hasColumn('transaksis', 'Harga_Satuan')) {
                $table->decimal('Harga_Satuan', 12, 2)->nullable()->after('Total_Pesanan');
            }

            if (!Schema::hasColumn('transaksis', 'Tanggal_Transaksi')) {
                $table->date('Tanggal_Transaksi')->nullable()->after('Harga_Satuan');
            }

            if (!Schema::hasColumn('transaksis', 'Total_Harga')) {
                $table->decimal('Total_Harga', 12, 2)->nullable()->after('Tanggal_Transaksi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropColumn([
                'id_transaksi',
                'id_menu',
                'Nama_Pegawai',
                'Nama_Produk',
                'Total_Pesanan',
                'Harga_Satuan',
                'Tanggal_Transaksi',
                'Total_Harga',
            ]);
        });
    }
};
