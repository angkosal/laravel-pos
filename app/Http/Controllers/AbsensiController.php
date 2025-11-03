<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    /**
     * Tampilkan semua data absensi
     */
    public function index(Request $request)
    {
        // Ambil kata kunci pencarian
        $search = $request->input('search');

        // Query dasar
        $query = Absensi::query();

        // Jika ada pencarian, filter hanya berdasarkan id_absensi
        if (!empty($search)) {
            $query->where('id_absensi', 'like', "%{$search}%");
        }

        // Urutkan berdasarkan tanggal terbaru dan paginasi
        // Tambahkan withQueryString() agar pagination tetap membawa parameter pencarian
        $absensis = $query->orderBy('tanggal', 'desc')
                          ->paginate(10)
                          ->withQueryString();

        // Kirim data ke view
        return view('absensi.index', compact('absensis', 'search'));
    }

    /**
     * Form tambah data absensi
     */
    public function create()
    {
        return view('absensi.create');
    }

    /**
     * Simpan data absensi baru
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'id_absensi'  => 'required|string|unique:absensis,id_absensi',
            'pegawai_id'  => 'required|integer',
            'tanggal'     => 'required|date',
            'jam_masuk'   => 'nullable|date_format:H:i',
            'jam_keluar'  => 'nullable|date_format:H:i',
            'shift'       => 'nullable|string',
            'status'      => 'required|string',
        ]);

        // Simpan data yang tervalidasi
        Absensi::create($validated);

        return redirect()->route('absensi.index')
                         ->with('success', 'Data absensi berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail data absensi
     */
    public function show($id)
    {
        $absensi = Absensi::findOrFail($id);
        return view('absensi.show', compact('absensi'));
    }

    /**
     * Form ubah data absensi
     */
    public function edit($id)
    {
        $absensi = Absensi::findOrFail($id);
        return view('absensi.edit', compact('absensi'));
    }

    /**
     * Update data absensi
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'id_absensi'  => "required|string|unique:absensis,id_absensi,{$id}",
            'pegawai_id'  => 'required|integer',
            'tanggal'     => 'required|date',
            'jam_masuk'   => 'nullable|date_format:H:i',
            'jam_keluar'  => 'nullable|date_format:H:i',
            'shift'       => 'nullable|string',
            'status'      => 'required|string',
        ]);

        $absensi = Absensi::findOrFail($id);
        $absensi->update($validated);

        return redirect()->route('absensi.index')
                         ->with('success', 'Data absensi berhasil diperbarui.');
    }

    /**
     * Hapus data absensi
     */
    public function destroy($id)
    {
        Absensi::destroy($id);

        return redirect()->route('absensi.index')
                         ->with('success', 'Data absensi berhasil dihapus.');
    }
}
