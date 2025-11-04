<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Gaji;

class LaporanController extends Controller
{
    /**
     * Menampilkan daftar laporan (default dan dengan pencarian).
     */
    public function index(Request $request)
    {
        $query = Laporan::query();

        // Fitur pencarian berdasarkan ID Laporan atau ID Gaji
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('id_laporan', 'like', '%' . $request->search . '%')
                  ->orWhere('id_gaji', 'like', '%' . $request->search . '%');
            });
        }

        // Urutkan dari yang terbaru
        $laporans = $query->orderBy('created_at', 'desc')->paginate(10);

        // Hitung total
        $income = $laporans->sum('total_pendapatan');
        $outcome = $laporans->sum('total_pengeluaran');
        $transactions = $laporans->count();

        return view('laporan.index', compact('laporans', 'income', 'outcome', 'transactions'));
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

    /**
     * Menampilkan laporan berdasarkan filter waktu.
     */
    public function filter($periode)
    {
        $today = Carbon::today();

        switch ($periode) {
            case 'today':
                $laporans = Laporan::whereDate('created_at', $today)->get();
                break;
            case 'week':
                $laporans = Laporan::whereBetween('created_at', [
                    $today->copy()->startOfWeek(),
                    $today->copy()->endOfWeek()
                ])->get();
                break;
            case 'month':
                $laporans = Laporan::whereMonth('created_at', $today->month)
                    ->whereYear('created_at', $today->year)
                    ->get();
                break;
            default:
                $laporans = Laporan::orderBy('created_at', 'desc')->get();
                break;
        }

        $income = $laporans->sum('total_pendapatan');
        $outcome = $laporans->sum('total_pengeluaran');
        $transactions = $laporans->count();

        return view('laporan.index', compact('laporans', 'income', 'outcome', 'transactions'));
    }

    /**
     * Simpan laporan baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_gaji' => 'required|exists:gajis,id_gaji',
        ]);

        $gaji = Gaji::find($validated['id_gaji']);

        $laporan = Laporan::create([
            'id_gaji' => $gaji->id_gaji,
            'tanggal_cetak' => now(),
            'total_gaji' => $gaji->total_gaji ?? 0,
            'periode' => $gaji->periode ?? now()->format('F Y'),
        ]);

        return redirect()->route('laporan.index')->with('success', 'Laporan berhasil ditambahkan!');
    }

    /**
     * Import data laporan dari file CSV.
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        $data = array_map('str_getcsv', file($path));
        $header = array_shift($data);

        foreach ($data as $row) {
            $rowData = array_combine($header, $row);

            Laporan::updateOrCreate(
                ['kode' => $rowData['kode']],
                [
                    'pegawai' => $rowData['pegawai'] ?? 'Unknown',
                    'total_pendapatan' => $rowData['total_pendapatan'] ?? 0,
                    'total_pengeluaran' => $rowData['total_pengeluaran'] ?? 0,
                ]
            );
        }

        return redirect()->back()->with('success', 'Data laporan berhasil diimport.');
    }

    public function edit($id)
    {
        $laporan = Laporan::findOrFail($id);
        return view('laporan.edit', compact('laporan'));
    }

    public function update(Request $request, $id)
    {
        $laporan = Laporan::findOrFail($id);
        $laporan->id_gaji = $request->id_gaji;
        $laporan->periode = $request->periode;
        $laporan->tanggal_cetak = $request->tanggal_cetak;
        $laporan->total_gaji = $request->total_gaji;
        $laporan->save();

        return redirect()->route('laporan.index')->with('success', 'Laporan berhasil diperbarui.');
    }
}
