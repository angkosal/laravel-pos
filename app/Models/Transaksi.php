<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksis';

    protected $fillable = [
        'Nama_Pegawai',
        'Nama_Produk',
        'Total_Pesanan',
        'Harga_Satuan',
        'Tanggal_Transaksi',
        'Total_Harga',
    ];

    public $timestamps = false;
}

