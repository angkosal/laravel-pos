<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Transaksi;
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

        // ambil history import
        $histories = ImportCSVHistory::orderBy('imported_at', 'desc')->limit(20)->get();

        return view('laporan.index', compact('laporans', 'transactions', 'histories'));
    }

    // Generate laporan per bulan (reset tiap bulan)
    public function generateFromTransaksi()
    {
        $periodeAktif = Transaksi::orderBy('Tanggal_Transaksi', 'desc')->first();

        if (!$periodeAktif) {
            return back()->with('error', 'Tidak ada data transaksi.');
        }

        $bulan = date('m', strtotime($periodeAktif->Tanggal_Transaksi));
        $tahun = date('Y', strtotime($periodeAktif->Tanggal_Transaksi));

        // Hapus laporan bulan ini terlebih dahulu
        Laporan::whereMonth('tanggal_cetak', $bulan)
            ->whereYear('tanggal_cetak', $tahun)
            ->delete();

        // Ambil transaksi khusus bulan ini
        $transaksis = Transaksi::whereMonth('Tanggal_Transaksi', $bulan)
            ->whereYear('Tanggal_Transaksi', $tahun)
            ->get();

        // Group by nama pegawai
        $grouped = $transaksis->groupBy('Nama_Pegawai');

        foreach ($grouped as $namaPegawai => $items) {
            $totalBonus = $items->sum(fn($t) => $t->Total_Harga * 0.10);
            $tanggalTerakhir = $items->sortByDesc('Tanggal_Transaksi')->first()->Tanggal_Transaksi;

            Laporan::create([
                'nama_pegawai'  => $namaPegawai,
                'tanggal_cetak' => $tanggalTerakhir,
                'total_gaji'    => $totalBonus,
            ]);
        }

        return back()->with('success', 'Laporan berhasil digenerate untuk bulan ' . date('F Y', strtotime($periodeAktif->Tanggal_Transaksi)) . '!');
    }

    // Export Excel Manual (Tanpa Composer)
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

    // Import CSV tanpa composer (final)
    public function importCSV(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        $count = 0;

        if (($handle = fopen($path, 'r')) !== false) {
            fgetcsv($handle, 1000, ','); // Skip header

            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                // Konversi format tanggal dari CSV ke MySQL
                $tanggal = null;
                if (!empty($data[4])) {
                    $tanggal = Carbon::createFromFormat('d/m/Y', $data[4])->format('Y-m-d');
                }

                Transaksi::create([
                    'Nama_Pegawai'      => $data[0] ?? null,
                    'Nama_Produk'       => $data[1] ?? null,
                    'Total_Pesanan'     => $data[2] ?? 0,
                    'Harga_Satuan'      => $data[3] ?? 0,
                    'Tanggal_Transaksi' => $tanggal,
                    'Total_Harga'       => ($data[2] ?? 0) * ($data[3] ?? 0),
                ]);

                $count++;
            }

            fclose($handle);
        }

        // simpan history import
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
