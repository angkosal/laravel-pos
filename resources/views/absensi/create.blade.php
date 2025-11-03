@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Tambah Data Absensi</h2>
    <form action="{{ route('absensi.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label>ID Absensi</label>
            <input type="text" name="id_absensi" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Pegawai ID</label>
            <input type="number" name="pegawai_id" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Tanggal</label>
            <input type="date" name="tanggal" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Jam Masuk</label>
            <input type="time" name="jam_masuk" class="form-control">
        </div>

        <div class="form-group">
            <label>Jam Keluar</label>
            <input type="time" name="jam_keluar" class="form-control">
        </div>

        <div class="form-group">
            <label>Shift</label>
            <select name="shift" class="form-control">
                <option value="">-- Pilih Shift --</option>
                <option value="Pagi">Pagi</option>
                <option value="Siang">Siang</option>
                <option value="Malam">Malam</option>
            </select>
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control" required>
                <option value="hadir">Hadir</option>
                <option value="izin">Izin</option>
                <option value="sakit">Sakit</option>
                <option value="alpha">Alpha</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success mt-3">Simpan</button>
        <a href="{{ route('absensi.index') }}" class="btn btn-secondary mt-3">Batal</a>
    </form>
</div>
@endsection
