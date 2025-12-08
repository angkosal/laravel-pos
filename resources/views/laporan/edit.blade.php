@extends('layouts.admin')

@section('content')
<h1>Edit Laporan</h1>

<form action="{{ route('laporan.update', $laporan->id_laporan) }}" method="POST">
    @csrf
    @method('PUT')

    <label>ID Gaji</label>
    <input type="text" name="id_gaji" value="{{ $laporan->id_gaji }}" class="form-control">

    <label>Periode</label>
    <input type="text" name="periode" value="{{ $laporan->periode }}" class="form-control" required>

    <label>Tanggal Cetak</label>
    <input type="date" name="tanggal_cetak" value="{{ $laporan->tanggal_cetak }}" class="form-control" required>

    <label>Total Gaji</label>
    <input type="number" step="0.01" name="total_gaji" value="{{ $laporan->total_gaji }}" class="form-control" required>

    <div class="mt-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary">Perbarui</button>
        <a href="{{ route('laporan.index') }}" class="btn btn-secondary">Batal</a>
    </div>
</form>
@endsection
