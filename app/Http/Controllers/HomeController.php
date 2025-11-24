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
        // Total pendapatan (Income) dari transaksi
        $income = DB::table('transaksis')->sum('Total_Harga');

        // Pengeluaran (Outcome) — belum ada tabel, tetap 0
        $outcome = 0;

        // Jumlah transaksi (total baris)
        $transactions = DB::table('transaksis')->count();

        // Data grafik income per bulan
        $chartData = DB::table('transaksis')
            ->select(DB::raw("DATE_FORMAT(Tanggal_Transaksi, '%d-%m-%Y') as tanggal"), DB::raw('SUM(Total_Harga) as total'))
            ->whereNotNull('Tanggal_Transaksi')
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->pluck('total', 'tanggal')
            ->toArray();

        // Hapus key kosong "" agar tidak merusak grafik
        unset($chartData[""]);

        return view('home', compact(
            'income',
            'outcome',
            'transactions',
            'chartData'
        ));
    }
}
