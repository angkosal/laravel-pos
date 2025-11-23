<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawais';
    protected $primaryKey = 'ID_Pegawai';
    public $timestamps = false;

    protected $fillable = [
        'Nama_Pegawai',
        'Username',
        'Password',
        'Status',
    ];
}
