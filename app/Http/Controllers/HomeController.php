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
        // Total pendapatan
        $income = DB::table('transaksis')->sum('Total_Harga');

        // Pengeluaran (manual)
        $outcome = 0;

        // Jumlah transaksi
        $transactions = DB::table('transaksis')->count();

        // Grafik pendapatan per tanggal
        $chartData = DB::table('transaksis')
            ->select(
                DB::raw("DATE_FORMAT(Tanggal_Transaksi, '%d-%m-%Y') as tanggal"),
                DB::raw('SUM(Total_Harga) as total')
            )
            ->whereNotNull('Tanggal_Transaksi')
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->pluck('total', 'tanggal')
            ->toArray();

        unset($chartData[""]); // buang key kosong


        // Grafik transaksi per pegawai
        $pegawaiTransaksi = DB::table('transaksis')
            ->select('Nama_Pegawai', DB::raw('COUNT(*) as total_transaksi'))
            ->groupBy('Nama_Pegawai')
            ->orderBy('total_transaksi', 'desc')
            ->pluck('total_transaksi', 'Nama_Pegawai')
            ->toArray();


        // 🔹 PRODUK TERAKHIR TERJUAL
        // Cari berdasarkan Tanggal_Transaksi terbaru
        $lastProduct = DB::table('transaksis')
            ->whereNotNull('Nama_Produk')
            ->orderBy('Tanggal_Transaksi', 'desc')
            ->value('Nama_Produk');


        // 🔹 TOP PRODUK PALING LARIS
        $topProducts = DB::table('transaksis')
            ->select('Nama_Produk', DB::raw('COUNT(*) as total'))
            ->whereNotNull('Nama_Produk')
            ->groupBy('Nama_Produk')
            ->orderByDesc('total')
            ->limit(5)
            ->get();


        return view('home', [
            'income' => $income,
            'outcome' => $outcome,
            'transactions' => $transactions,
            'chartData' => $chartData,
            'pegawaiTransaksi' => $pegawaiTransaksi,
            'lastProduct' => $lastProduct ?? '-',
            'topProducts' => $topProducts,
        ]);
    }
}
