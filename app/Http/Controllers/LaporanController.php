<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;

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

        return view('laporan.index', compact('laporans', 'transactions'));
    }

    // 🆕 Generate laporan berdasarkan transaksi (Akumulasi per pegawai)
    public function generateFromTransaksi()
    {
        $transaksis = Transaksi::all();

        foreach ($transaksis as $transaksi) {
            if (!$transaksi->Nama_Pegawai) continue;

            $laporan = Laporan::where('nama_pegawai', $transaksi->Nama_Pegawai)->first();

            if ($laporan) {
                $laporan->total_gaji += $transaksi->Total_Harga * 0.10;
                $laporan->save();
            } else {
                Laporan::create([
                    'nama_pegawai'  => $transaksi->Nama_Pegawai,
                    'tanggal_cetak' => $transaksi->Tanggal_Transaksi ?? now(),
                    'total_gaji'    => $transaksi->Total_Harga * 0.10,
                ]);
            }
        }

        return back()->with('success', 'Laporan berhasil digenerate dari transaksi!');
    }

    // 🆕 Export Excel Manual (Tanpa Composer)
    public function exportExcelManual()
    {
        $laporans = Laporan::all();

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=laporan.xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo "<table border='1'>
            <tr>
                <th>No</th>
                <th>Nama Pegawai</th>
                <th>Tanggal Cetak</th>
                <th>Total Gaji (Bonus 10%)</th>
            </tr>";

        $no = 1;
        foreach ($laporans as $laporan) {
            echo "
            <tr>
                <td>" . $no++ . "</td>
                <td>" . ($laporan->nama_pegawai ?? '-') . "</td>
                <td>" . ($laporan->tanggal_cetak ?? '-') . "</td>
                <td>Rp" . number_format($laporan->total_gaji ?? 0, 0, ',', '.') . "</td>
            </tr>";
        }

        echo "</table>";
        exit;
    }

    // 🆕 Import CSV (Tanpa Composer) – Sesuai format file kamu
    public function importCSV(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file')->getRealPath();

        if (($handle = fopen($file, 'r')) !== false) {

            fgetcsv($handle, 1000, ','); // Skip header

            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                Transaksi::create([
                    'Nama_Pegawai'      => $data[0] ?? null,
                    'Nama_Produk'       => $data[1] ?? null,
                    'Total_Pesanan'     => $data[2] ?? 0,
                    'Harga_Satuan'      => $data[3] ?? 0,
                    'Tanggal_Transaksi' => $data[4] ?? null,
                    'Total_Harga'       => ($data[2] ?? 0) * ($data[3] ?? 0),
                ]);
            }
            fclose($handle);
        }

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
