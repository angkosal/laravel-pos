<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Total pendapatan (Income)
        $income = DB::table('transaksis')->sum('Total_Harga');

        // Pengeluaran (Outcome) — masih tetap 0
        $outcome = 0;

        // Jumlah transaksi (total baris)
        $transactions = DB::table('transaksis')->count();

        // Data grafik income berdasarkan tanggal transaksi
        $chartData = DB::table('transaksis')
            ->select(DB::raw("DATE_FORMAT(Tanggal_Transaksi, '%d-%m-%Y') as tanggal"), DB::raw('SUM(Total_Harga) as total'))
            ->whereNotNull('Tanggal_Transaksi')
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->pluck('total', 'tanggal')
            ->toArray();

        unset($chartData[""]); // Buang key kosong jika ada

        // 🔹 Data grafik transaksi per pegawai (Bar Chart)
        $pegawaiTransaksi = DB::table('transaksis')
            ->select('Nama_Pegawai', DB::raw('COUNT(*) as total_transaksi'))
            ->groupBy('Nama_Pegawai')
            ->orderBy('total_transaksi', 'desc')
            ->pluck('total_transaksi', 'Nama_Pegawai')
            ->toArray();

        return view('home', compact(
            'income',
            'outcome',
            'transactions',
            'chartData',
            'pegawaiTransaksi'
        ));
    }
}
