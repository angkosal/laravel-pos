<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use App\Models\ImportCSVHistory;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $query = Laporan::query();

        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('id_laporan', 'like', '%' . $request->search . '%')
                  ->orWhere('nama_pegawai', 'like', '%' . $request->search . '%');
            });
        }

        $laporans = $query->orderBy('created_at', 'desc')->paginate(10);
        $transactions = $laporans->count();

        // ✅ ambil history import
        $histories = ImportCSVHistory::orderBy('imported_at','desc')->limit(20)->get();

        return view('laporan.index', compact('laporans', 'transactions', 'histories'));
    }

    public function generate(Request $request)
    {
        $transaksi = Transaksi::all();

        if ($transaksi->count() == 0) {
            return redirect()->back()->with('error', 'Tidak ada data transaksi untuk digenerate');
        }

        $totalPendapatan = $transaksi->sum('Total_Harga');
        $totalTransaksi = $transaksi->count();

        // contoh logika gaji: 10% dari penjualan
        $totalGaji = $totalPendapatan * 0.10;

        Laporan::create([
            'nama_pegawai'      => 'Semua Pegawai',
            'total_transaksi'   => $totalTransaksi,
            'total_pendapatan'  => $totalPendapatan,
            'total_gaji'        => $totalGaji,
            'tanggal_cetak'     => now(),
        ]);

        return redirect()->back()->with('success', 'Laporan berhasil digenerate dari transaksi');
    }




    public function importCSV(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        $count = 0;

        if (($handle = fopen($path, 'r')) !== false) {

            fgetcsv($handle, 1000, ',');

            while (($data = fgetcsv($handle, 1000, ',')) !== false) {

                Transaksi::create([
                    'Nama_Pegawai'      => $data[0] ?? null,
                    'Nama_Produk'       => $data[1] ?? null,
                    'Total_Pesanan'     => $data[2] ?? 0,
                    'Harga_Satuan'      => $data[3] ?? 0,
                    'Tanggal_Transaksi' => $data[4] ?? null,
                    'Total_Harga'       => ($data[2] ?? 0) * ($data[3] ?? 0),
                ]);

                $count++;
            }

            fclose($handle);
        }

        // ✅ simpan history import
        ImportCSVHistory::create([
            'file_name'  => $file->getClientOriginalName(),
            'row_count'  => $count,
            'imported_at'=> now(),
        ]);

        return back()->with('success', 'Data transaksi berhasil diimport tanpa Composer!');
    }

    public function destroy($id)
    {
        $laporan = Laporan::findOrFail($id);
        $laporan->delete();

        return redirect()->route('laporan.index')->with('success', 'Laporan berhasil dihapus.');
    }

    public function show($id)
    {
        $laporan = Laporan::findOrFail($id);
        return view('laporan.show', compact('laporan'));
    }
}
