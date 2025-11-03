<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_laporan';
    protected $fillable = [
        'id_gaji',
        'periode',
        'tanggal_cetak',
        'total_gaji',
    ];
}
