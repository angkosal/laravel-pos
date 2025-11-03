@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4 fw-semibold">Daftar Laporan Absensi</h1>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <!-- Tombol Tambah -->
        <a href="{{ route('laporan.create') }}" class="btn btn-primary">Tambah Laporan</a>

        <!-- Input Pencarian (otomatis jalan tanpa tombol) -->
        <form action="{{ route('laporan.index') }}" method="GET">
            <input 
                type="text" 
                name="search" 
                class="form-control" 
                placeholder="Cari berdasarkan ID Laporan..." 
                value="{{ request('search') }}"
                oninput="this.form.submit()"
                style="width: 250px;"
            >
        </form>
    </div>

    <!-- Tabel Data -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID Laporan</th>
                    <th>ID Gaji</th>
                    <th>Periode</th>
                    <th>Tanggal Cetak</th>
                    <th>Total Gaji</th>
                    <th class="text-center" style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($laporans as $laporan)
                <tr>
                    <td>{{ $laporan->id_laporan }}</td>
                    <td>{{ $laporan->id_gaji }}</td>
                    <td>{{ $laporan->periode }}</td>
                    <td>{{ $laporan->tanggal_cetak }}</td>
                    <td>{{ number_format($laporan->total_gaji, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <a href="{{ route('laporan.edit', $laporan->id_laporan) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('laporan.destroy', $laporan->id_laporan) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button 
                                type="submit" 
                                class="btn btn-danger btn-sm" 
                                onclick="return confirm('Yakin ingin menghapus laporan ini?')">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Tidak ada data laporan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-3">
        {{ $laporans->links() }}
    </div>
</div>
@endsection
