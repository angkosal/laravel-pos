<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    /**
     * Nama tabel jika berbeda dari nama model (opsional)
     */
    // protected $table = 'absensi';

    /**
     * Atribut yang boleh diisi massal
     */
    protected $fillable = [
        'id_absensi',   // ID absensi unik
        'pegawai_id',   // ID pegawai
        'tanggal',      // Tanggal absensi
        'jam_masuk',    // Jam masuk
        'jam_keluar',   // Jam keluar
        'shift',        // Shift: pagi/siang/malam
        'status',       // Status: hadir, izin, sakit, dsb
    ];

    /**
     * Tipe data atribut (opsional tapi direkomendasikan)
     */
    protected $casts = [
        'tanggal' => 'date',
        'jam_masuk' => 'datetime:H:i',
        'jam_keluar' => 'datetime:H:i',
    ];

    /**
     * Relasi ke pegawai (opsional, jika ada tabel pegawai)
     */
    // public function pegawai()
    // {
    //     return $this->belongsTo(User::class, 'pegawai_id');
    // }
}
