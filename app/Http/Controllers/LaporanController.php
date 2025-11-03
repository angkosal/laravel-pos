<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Tampilkan daftar laporan.
     */
    public function index(Request $request)
    {
        // Ambil kata kunci pencarian dari input
        $search = $request->input('search');

        // Query dasar
        $query = Laporan::query();

        // Jika ada pencarian, hanya filter berdasarkan id_laporan
        if (!empty($search)) {
            $query->where('id_laporan', 'like', "%{$search}%");
        }

        // Urutkan terbaru dan paginasi
        $laporans = $query->latest()->paginate(10);

        // Kirim ke view
        return view('laporan.index', compact('laporans', 'search'));
    }

    /**
     * Tampilkan form tambah laporan.
     */
    public function create()
    {
        return view('laporan.create');
    }

    /**
     * Simpan laporan baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_laporan' => 'nullable|string|max:20',
            'id_gaji' => 'nullable|string|max:15',
            'periode' => 'required|string|max:20',
            'tanggal_cetak' => 'required|date',
            'total_gaji' => 'required|numeric',
        ]);

        // Generate id laporan otomatis jika kosong
        $idLaporan = $request->id_laporan;
        if (empty($idLaporan)) {
            $last = Laporan::latest('id_laporan')->first();
            $lastNumber = $last ? (int) filter_var($last->id_laporan, FILTER_SANITIZE_NUMBER_INT) : 0;
            $idLaporan = 'LAP' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        }

        Laporan::create([
            'id_laporan' => $idLaporan,
            'id_gaji' => $request->id_gaji,
            'periode' => $request->periode,
            'tanggal_cetak' => $request->tanggal_cetak,
            'total_gaji' => $request->total_gaji,
        ]);

        return redirect()->route('laporan.index')->with('success', 'Laporan berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit laporan.
     */
    public function edit($id)
    {
        $laporan = Laporan::findOrFail($id);
        return view('laporan.edit', compact('laporan'));
    }

    /**
     * Perbarui data laporan.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_gaji' => 'nullable|string|max:15',
            'periode' => 'required|string|max:20',
            'tanggal_cetak' => 'required|date',
            'total_gaji' => 'required|numeric',
        ]);

        $laporan = Laporan::findOrFail($id);
        $laporan->update([
            'id_gaji' => $request->id_gaji,
            'periode' => $request->periode,
            'tanggal_cetak' => $request->tanggal_cetak,
            'total_gaji' => $request->total_gaji,
        ]);

        return redirect()->route('laporan.index')->with('success', 'Laporan berhasil diperbarui.');
    }

    /**
     * Hapus laporan.
     */
    public function destroy($id)
    {
        $laporan = Laporan::findOrFail($id);
        $laporan->delete();

        return redirect()->route('laporan.index')->with('success', 'Laporan berhasil dihapus.');
    }
}
