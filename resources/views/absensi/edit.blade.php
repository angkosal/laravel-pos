@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Edit Data Absensi</h2>
    <form action="{{ route('absensi.update', $absensi->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>ID Absensi</label>
            <input type="text" name="id_absensi" value="{{ $absensi->id_absensi }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Pegawai ID</label>
            <input type="number" name="pegawai_id" value="{{ $absensi->pegawai_id }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Tanggal</label>
            <input type="date" name="tanggal" value="{{ $absensi->tanggal }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Jam Masuk</label>
            <input type="time" name="jam_masuk" value="{{ $absensi->jam_masuk }}" class="form-control">
        </div>

        <div class="form-group">
            <label>Jam Keluar</label>
            <input type="time" name="jam_keluar" value="{{ $absensi->jam_keluar }}" class="form-control">
        </div>

        <div class="form-group">
            <label>Shift</label>
            <select name="shift" class="form-control">
                <option value="">-- Pilih Shift --</option>
                <option value="Pagi" {{ $absensi->shift == 'Pagi' ? 'selected' : '' }}>Pagi</option>
                <option value="Siang" {{ $absensi->shift == 'Siang' ? 'selected' : '' }}>Siang</option>
                <option value="Malam" {{ $absensi->shift == 'Malam' ? 'selected' : '' }}>Malam</option>
            </select>
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control" required>
                <option value="hadir" {{ $absensi->status == 'hadir' ? 'selected' : '' }}>Hadir</option>
                <option value="izin" {{ $absensi->status == 'izin' ? 'selected' : '' }}>Izin</option>
                <option value="sakit" {{ $absensi->status == 'sakit' ? 'selected' : '' }}>Sakit</option>
                <option value="alpha" {{ $absensi->status == 'alpha' ? 'selected' : '' }}>Alpha</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Update</button>
        <a href="{{ route('absensi.index') }}" class="btn btn-secondary mt-3">Batal</a>
    </form>
</div>
@endsection
