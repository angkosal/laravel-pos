@extends('layouts.admin')

@section('content')
<style>
    .edit-wrap {
        background:#ffffff;
        border-radius:18px;
        padding:24px;
        box-shadow:0 6px 20px rgba(0,0,0,0.08);
        max-width:700px;
        margin:auto;
    }
    .edit-title {
        font-size:28px;
        font-weight:900;
        color:#143a31;
        margin-bottom:18px;
    }
    .form-label {
        font-weight:700;
        color:#1b4a3f;
    }
    .form-control, select.form-control {
        border-radius:12px;
        padding:10px 12px;
        border:1px solid #dce2df;
    }
    .btn-submit {
        background:#1b4a3f;
        color:#fff;
        font-weight:700;
        border-radius:12px;
        padding:10px 18px;
    }
    .btn-submit:hover { background:#245747; }

    .btn-back {
        border-radius:12px;
        padding:10px 18px;
        font-weight:700;
    }
</style>

<div class="edit-wrap mt-4">

    <h2 class="edit-title">Edit Data Absensi</h2>

    <form action="{{ route('absensi.update', $absensi->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- ID Absensi --}}
        <div class="mb-3">
            <label class="form-label">ID Absensi</label>
            <input type="text" name="id_absensi" value="{{ $absensi->id_absensi }}" class="form-control" required>
        </div>

        {{-- Pegawai ID --}}
        <div class="mb-3">
            <label class="form-label">Pegawai ID</label>
            <input type="number" name="pegawai_id" value="{{ $absensi->pegawai_id }}" class="form-control" required>
        </div>

        {{-- Tanggal --}}
        <div class="mb-3">
            <label class="form-label">Tanggal</label>
            <input type="date" name="tanggal" value="{{ $absensi->tanggal }}" class="form-control" required>
        </div>

        {{-- Jam Masuk --}}
        <div class="mb-3">
            <label class="form-label">Jam Masuk</label>
            <input type="time" name="jam_masuk" value="{{ $absensi->jam_masuk }}" class="form-control">
        </div>

        {{-- Jam Keluar --}}
        <div class="mb-3">
            <label class="form-label">Jam Keluar</label>
            <input type="time" name="jam_keluar" value="{{ $absensi->jam_keluar }}" class="form-control">
        </div>

        {{-- Shift --}}
        <div class="mb-3">
            <label class="form-label">Shift</label>
            <select name="shift" class="form-control">
                <option value="">-- Pilih Shift --</option>
                <option value="Pagi" {{ $absensi->shift == 'Pagi' ? 'selected' : '' }}>Pagi</option>
                <option value="Siang" {{ $absensi->shift == 'Siang' ? 'selected' : '' }}>Siang</option>
                <option value="Malam" {{ $absensi->shift == 'Malam' ? 'selected' : '' }}>Malam</option>
            </select>
        </div>

        {{-- Status --}}
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-control" required>
                <option value="hadir" {{ $absensi->status == 'hadir' ? 'selected' : '' }}>Hadir</option>
                <option value="izin" {{ $absensi->status == 'izin' ? 'selected' : '' }}>Izin</option>
                <option value="sakit" {{ $absensi->status == 'sakit' ? 'selected' : '' }}>Sakit</option>
                <option value="alpha" {{ $absensi->status == 'alpha' ? 'selected' : '' }}>Alpha</option>
            </select>
        </div>

        {{-- Tombol --}}
        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn-submit">Update</button>
            <a href="{{ route('absensi.index') }}" class="btn btn-back btn-secondary">Batal</a>
        </div>

    </form>
</div>

@endsection
