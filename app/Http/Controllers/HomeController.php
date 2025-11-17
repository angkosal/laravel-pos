<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     */
    public function index()
    {
        // Hitung jumlah pegawai
        $pegawai_count = DB::table('pegawais')->count();

        // Total transaksi (jumlah baris)
        $total_transaksi = DB::table('transaksis')->count();

        // Total pendapatan dari transaksi
        $total_pendapatan = DB::table('transaksis')->sum('total_harga');

        // Total transaksi hari ini
        $pendapatan_hari_ini = DB::table('transaksis')
            ->whereDate('tanggal_transaksi', date('Y-m-d'))
            ->sum('total_harga');

        // Total gaji belum dibayar
        $gaji_pending = DB::table('gajis')->where('status_pembayaran', 'pending')->count();


        return view('home', [
            'pegawai_count' => $pegawai_count,
            'total_transaksi' => $total_transaksi,
            'total_pendapatan' => $total_pendapatan,
            'pendapatan_hari_ini' => $pendapatan_hari_ini,
            'gaji_pending' => $gaji_pending,
        ]);
    }
}
