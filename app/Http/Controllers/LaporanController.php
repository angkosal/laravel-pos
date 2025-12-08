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

        if ($request->has('search') && $request->search !== '') {
            $query->where(function ($q) use ($request) {
                $q->where('nama_pegawai', 'like', '%' . $request->search . '%')
                  ->orWhere('id_laporan', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('month')) {
            $query->whereMonth('tanggal_cetak', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('tanggal_cetak', $request->year);
        }

        $laporans = $query->orderBy('tanggal_cetak', 'desc')->paginate(10);
        $histories = ImportCSVHistory::orderBy('imported_at', 'desc')->limit(20)->get();

        return view('laporan.index', compact('laporans', 'histories'));
    }



    // =============================================================
    //   GENERATE LAPORAN BULANAN DARI TABEL TRANSAKSI
    // =============================================================
    public function generateFromTransaksi()
    {
        $periodes = Transaksi::selectRaw('YEAR(Tanggal_Transaksi) as tahun, MONTH(Tanggal_Transaksi) as bulan')
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get();

        foreach ($periodes as $periode) {
            $bulan = $periode->bulan;
            $tahun = $periode->tahun;

            // Hapus laporan duplikat untuk periode ini
            Laporan::whereMonth('tanggal_cetak', $bulan)
                ->whereYear('tanggal_cetak', $tahun)
                ->delete();

            // Ambil transaksi bulan tersebut
            $transaksis = Transaksi::whereMonth('Tanggal_Transaksi', $bulan)
                ->whereYear('Tanggal_Transaksi', $tahun)
                ->get();

            $grouped = $transaksis->groupBy('Nama_Pegawai');

            foreach ($grouped as $namaPegawai => $items) {

                // Hitung total bonus 10%
                $totalBonus = $items->sum(fn($t) => $t->Total_Harga * 0.10);

                // Ambil tanggal transaksi terakhir
                $tanggalTerakhir = $items->sortByDesc('Tanggal_Transaksi')->first()->Tanggal_Transaksi;

                Laporan::create([
                    'nama_pegawai'  => $namaPegawai,
                    'tanggal_cetak' => $tanggalTerakhir,
                    'total_gaji'    => $totalBonus,
                ]);
            }
        }

        return back()->with('success', 'Laporan berhasil digenerate dari data transaksi!');
    }



    // =============================================================
    //   IMPORT CSV (FORMAT KHUSUS SESUAI DATA KAMU)
    // =============================================================
    public function importCSV(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:4096',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();
        $count = 0;

        if (($handle = fopen($path, 'r')) !== false) {

            // Baca header CSV
            $header = fgets($handle);

            while (($line = fgets($handle)) !== false) {

                $line = trim($line);
                $line = trim($line, "\"");

                $data = explode(',', $line);

                // Skip jika data tidak lengkap
                if (count($data) < 7) continue;

                // Konversi tanggal
                try {
                    $tanggal = Carbon::createFromFormat('d/m/Y', trim($data[6]))->format('Y-m-d');
                } catch (\Exception $e) {
                    $tanggal = null;
                }

                // Simpan transaksi
                Transaksi::create([
                    'Nama_Pegawai'      => $data[2] ?? null,
                    'Nama_Produk'       => $data[3] ?? null,
                    'Total_Pesanan'     => (int)($data[4] ?? 0),
                    'Harga_Satuan'      => (int)($data[5] ?? 0),
                    'Tanggal_Transaksi' => $tanggal,
                    'Total_Harga'       => ((int)($data[4] ?? 0)) * ((int)($data[5] ?? 0)),
                ]);

                $count++;
            }

            fclose($handle);
        }

        ImportCSVHistory::create([
            'file_name'   => $file->getClientOriginalName(),
            'row_count'   => $count,
            'imported_at' => now(),
        ]);

        return back()->with('success', "Import berhasil! Total $count data tersimpan.");
    }



    // =============================================================
    //   EXPORT EXCEL MANUAL
    // =============================================================
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



    // =============================================================
    //   DELETE LAPORAN
    // =============================================================
    public function destroy($id)
    {
        $laporan = Laporan::findOrFail($id);
        $laporan->delete();

        return redirect()->route('laporan.index')->with('success', 'Laporan berhasil dihapus.');
    }



    // =============================================================
    //   SHOW DETAIL LAPORAN
    // =============================================================
    public function show($id)
    {
        $laporan = Laporan::findOrFail($id);
        return view('laporan.show', compact('laporan'));
    }



    // =============================================================
    //   EDIT LAPORAN
    // =============================================================
    public function edit($id)
    {
        $laporan = Laporan::findOrFail($id);
        return view('laporan.edit', compact('laporan'));
    }



    // =============================================================
    //   UPDATE LAPORAN
    // =============================================================
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_pegawai'  => 'nullable|string|max:255',
            'tanggal_cetak' => 'nullable|date',
            'total_gaji'    => 'nullable|numeric',
        ]);

        $laporan = Laporan::findOrFail($id);

        $laporan->update([
            'nama_pegawai'  => $request->nama_pegawai,
            'tanggal_cetak' => $request->tanggal_cetak,
            'total_gaji'    => $request->total_gaji,
        ]);

        return redirect()->route('laporan.index')->with('success', 'Laporan berhasil diperbarui!');
    }
}
