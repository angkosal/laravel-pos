@extends('layouts.admin')

@section('title', 'Detail Absensi - Deeen Coffee')

@section('css')
<style>
    :root{ --green:#1f3d34; --ring:#e9efec; }
    .card-detail{background:#fff;border:1px solid var(--ring);border-radius:16px;padding:22px;box-shadow:0 8px 20px rgba(0,0,0,.06)}
    .head{display:flex;align-items:center;gap:12px;margin-bottom:16px}
    .head .icon{width:44px;height:44px;border-radius:12px;background:#e9efec;display:flex;align-items:center;justify-content:center}
    .head h2{margin:0;font-weight:800;color:var(--green)}
    .item{display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f0f2f1}
    .label{color:#5c6762;font-weight:600}
    .value{color:#1f2724}
</style>
@endsection

@section('content')
<div class="card-detail">
    <div class="head">
        <div class="icon"><i class="fas fa-info-circle"></i></div>
        <h2>Detail Absensi</h2>
    </div>

    @php
        $tgl = \Carbon\Carbon::parse($absensi->tanggal)->format('Y-m-d');
        $jm  = \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i');
        $jk  = \Carbon\Carbon::parse($absensi->jam_keluar)->format('H:i');
    @endphp

    <div class="item"><span class="label">ID Absensi</span><span class="value">{{ $absensi->id_absensi }}</span></div>
    <div class="item"><span class="label">Pegawai ID</span><span class="value">{{ $absensi->pegawai_id }}</span></div>
    <div class="item"><span class="label">Tanggal</span><span class="value">{{ $tgl }}</span></div>
    <div class="item"><span class="label">Jam Masuk</span><span class="value">{{ $jm }}</span></div>
    <div class="item"><span class="label">Jam Keluar</span><span class="value">{{ $jk }}</span></div>
    <div class="item"><span class="label">Shift</span><span class="value">{{ $absensi->shift }}</span></div>
    <div class="item"><span class="label">Status</span><span class="value">{{ ucfirst($absensi->status) }}</span></div>

    <div class="mt-3 d-flex gap-2">
        <a href="{{ route('absensi.edit', $absensi->id) }}" class="btn btn-warning fw-bold" style="border-radius:10px">
            <i class="fas fa-pen"></i> Ubah
        </a>
        <a href="{{ route('absensi.index') }}" class="btn btn-secondary" style="border-radius:10px">
            Kembali
        </a>
    </div>
</div>
@endsection
