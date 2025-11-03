@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Detail Absensi</h2>
    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <td>{{ $absensi->id }}</td>
        </tr>
        <tr>
            <th>Pegawai ID</th>
            <td>{{ $absensi->pegawai_id }}</td>
        </tr>
        <tr>
            <th>Tanggal</th>
            <td>{{ $absensi->tanggal }}</td>
        </tr>
        <tr>
            <th>Jam Masuk</th>
            <td>{{ $absensi->jam_masuk }}</td>
        </tr>
        <tr>
            <th>Jam Keluar</th>
            <td>{{ $absensi->jam_keluar }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>{{ ucfirst($absensi->status) }}</td>
        </tr>
    </table>
    <a href="{{ route('absensi.index') }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection
