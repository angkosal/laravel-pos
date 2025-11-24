@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h2 class="fw-bold mb-4">Detail Laporan</h2>

    <div class="card p-4 shadow-sm border-0">
        <table class="table">
            <tr>
                <th>ID Laporan</th>
                <td>{{ $laporan->id_laporan }}</td>
            </tr>
            <tr>
                <th>Tanggal Cetak</th>
                <td>{{ $laporan->tanggal_cetak ? \Carbon\Carbon::parse($laporan->tanggal_cetak)->format('d/m/Y') : '-' }}</td>
            </tr>
            <tr>
                <th>Nama Pegawai</th>
                <td>{{ $laporan->nama_pegawai ?? '-' }}</td>
            </tr>
            <tr>
                <th>Total Gaji</th>
                <td>Rp {{ number_format($laporan->total_gaji ?? 0, 0, ',', '.') }}</td>
            </tr>
        </table>

        <a href="{{ route('laporan.index') }}" class="btn btn-secondary mt-3">Kembali</a>
    </div>
</div>
@endsection
