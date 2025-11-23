<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'transaksis';

    // Primary Key yang digunakan
    protected $primaryKey = 'ID_Transaksi';

    // Karena ID_Transaksi berformat custom (tidak auto increment)
    public $incrementing = false;

    // Karena tabel tidak punya created_at dan updated_at
    public $timestamps = false;

    // Kolom yang boleh diisi (mass assignment)
    protected $fillable = [
        'ID_Transaksi',
        'Tanggal_Transaksi',
        'Nama_Pegawai',
        'Nama_Produk',       // ⬅️ HARUS ADA agar produk tersimpan
        'Total_Pesanan',
        'Harga_Satuan',
        'Total_Harga',
    ];
}

