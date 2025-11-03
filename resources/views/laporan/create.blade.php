@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Tambah Laporan</h2>

    <form action="{{ route('laporan.store') }}" method="POST">
        @csrf

        <!-- ID Laporan -->
        <div class="mb-3">
            <label for="id_laporan" class="form-label">ID Laporan</label>
            <input type="text" name="id_laporan" id="id_laporan" class="form-control" placeholder="Masukkan ID Laporan">
        </div>

                <!-- ID Gaji -->
        <div class="mb-3">
            <label for="id_gaji" class="form-label">ID Gaji</label>
            <input type="text" name="id_gaji" id="id_gaji" class="form-control" placeholder="Masukkan ID gaji">
        </div>

        <!-- Periode -->
        <div class="mb-3">
            <label for="periode" class="form-label">Periode</label>
            <input type="text" name="periode" id="periode" class="form-control" placeholder="Masukkan periode">
        </div>

        <!-- Tanggal Cetak -->
        <div class="mb-3">
            <label for="tanggal_cetak" class="form-label">Tanggal Cetak</label>
            <input type="date" name="tanggal_cetak" id="tanggal_cetak" class="form-control">
        </div>

        <!-- Total Gaji -->
        <div class="mb-3">
            <label for="total_gaji" class="form-label">Total Gaji</label>
            <input type="number" name="total_gaji" id="total_gaji" class="form-control" placeholder="Masukkan total gaji">
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
    </form>
</div>
@endsection
