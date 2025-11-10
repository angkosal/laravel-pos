@extends('layouts.admin')

@section('title', 'Absensi Pegawai - Deeen Coffee')

@section('css')
<style>
  :root{
    --green-900:#143a31;
    --green-800:#1b4a3f;
    --green-600:#245747;
    --surface:#ffffff;
    --ring:#e9efec;
    --head:#e7e9ea;     /* bar "List Absensi Pegawai" */
    --thead:#e8efe9;    /* header tabel */
  }

  .wrap{
    background:var(--surface);
    border:1px solid var(--ring);
    border-radius:22px;
    padding:24px;
    box-shadow:0 8px 20px rgba(0,0,0,.06);
  }

  /* Title row */
  .page-head{ display:flex; align-items:center; gap:14px; margin-bottom:12px; }
  .page-head .icon-pill{
    width:44px; height:44px; border-radius:12px; background:var(--ring);
    display:flex; align-items:center; justify-content:center; color:var(--green-800);
  }
  .page-head h1{ font-size:32px; font-weight:900; color:var(--green-900); margin:0; letter-spacing:.2px; }

  /* Periode filter (ikon kalender + pill) */
  .filter-row{ display:flex; align-items:center; gap:10px; margin:6px 0 16px; }
  .filter-row .mini{
    width:32px; height:32px; border-radius:10px; background:var(--head);
    display:grid; place-items:center; color:#5e7069;
  }
  .filter-row .pill{
    background:#dfe3e2; color:#2e3a37; border:0; border-radius:12px;
    padding:8px 12px; font-weight:700;
  }

  /* Head bar "List Absensi Pegawai" (judul saja) */
  .headbar{
    background:var(--head);
    border-radius:12px;
    padding:10px 12px;
    display:flex; align-items:center; gap:12px; /* TIDAK space-between */
  }
  .headbar .title{ font-weight:800; color:#2c3b36; }

  /* GARIS AKSI: Tambah Pegawai KIRI, Search KANAN */
  .actionline{
    display:flex; align-items:center; justify-content:space-between;
    gap:12px; margin:10px 0 6px;
  }

  /* Button Tambah Pegawai (kiri) */
  .btn-add{
    background:var(--green-800); color:#fff; border:none; border-radius:14px;
    padding:10px 16px; display:inline-flex; align-items:center; gap:10px;
    font-weight:800;
  }
  .btn-add .dot{
    width:26px; height:26px; border-radius:999px; background:#fff; color:var(--green-800);
    display:inline-grid; place-items:center; font-weight:900; line-height:1;
  }
  .btn-add:hover{ background:var(--green-600); }

  /* Search (kanan) */
  .search{ position:relative; width:260px; }
  .search i{
    position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#98a8a2;
  }
  .search input{
    height:42px; border-radius:12px; border:1px solid var(--ring);
    padding-left:36px;
  }

  /* Table */
  .table-soft{ width:100%; margin-top:10px; border-collapse:separate; border-spacing:0; }
  .table-soft thead th{
    background:var(--thead); color:var(--green-900);
    border:none; text-transform:uppercase; font-size:13px; font-weight:800;
    letter-spacing:.35px; padding:12px 14px;
  }
  .table-soft tbody td{ padding:14px; border-bottom:1px solid #eef2f0; vertical-align:middle; }
  .table-soft tbody tr:hover{ background:#f7f9f8; }

  /* Status badge */
  .badge-soft{ border-radius:999px; padding:6px 12px; font-weight:800; font-size:12px; }
  .badge-hadir{ background:#e5f5ec; color:#2c7a4b; }
  .badge-absen{ background:#fff4e5; color:#b26b00; }

  /* Action buttons */
  .btn-aksi{ border:none; border-radius:10px; padding:8px 12px; font-weight:800; display:inline-flex; align-items:center; gap:8px; }
  .btn-edit{ background:#f9c76a; color:#4e3a07; }
  .btn-edit:hover{ background:#f3b94a; }
  .btn-del{ background:#f25f57; color:#fff; }
  .btn-del:hover{ background:#e5483e; }
  .btn-det{ background:#e5e7ea; color:#59606a; }
  .btn-det:hover{ background:#d9dbe0; }

  .pagination .page-link{ border-radius:10px; border:1px solid var(--ring); color:#2b2f2d; }
  .pagination .page-item.active .page-link{ background:var(--green-800); border-color:var(--green-800); color:#fff; }
</style>
@endsection

@section('content')
<div class="wrap">

  {{-- Title --}}
  <div class="page-head">
    <div class="icon-pill"><i class="fas fa-user"></i></div>
    <h1>Absensi</h1>
  </div>

  {{-- Periode --}}
  <div class="filter-row">
    <div class="mini"><i class="fas fa-calendar-day"></i></div>
    <div class="dropdown">
      <button class="pill dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        {{ ['today'=>'Hari Ini','week'=>'Minggu Ini','month'=>'Bulan Ini','year'=>'Tahun Ini'][request('range','today')] ?? 'Hari Ini' }}
      </button>
      <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="{{ route('absensi.index', request()->except('page') + ['range'=>'today']) }}"><i class="fas fa-sun me-2 text-muted"></i>Hari Ini</a></li>
        <li><a class="dropdown-item" href="{{ route('absensi.index', request()->except('page') + ['range'=>'week']) }}"><i class="fas fa-calendar-week me-2 text-muted"></i>Minggu Ini</a></li>
        <li><a class="dropdown-item" href="{{ route('absensi.index', request()->except('page') + ['range'=>'month']) }}"><i class="fas fa-calendar-alt me-2 text-muted"></i>Bulan Ini</a></li>
        <li><a class="dropdown-item" href="{{ route('absensi.index', request()->except('page') + ['range'=>'year']) }}"><i class="fas fa-calendar me-2 text-muted"></i>Tahun Ini</a></li>
      </ul>
    </div>
  </div>

  {{-- Headbar (judul saja) --}}
  <div class="headbar">
    <div class="title">List Absensi Pegawai</div>
  </div>

  {{-- GARIS AKSI: Tambah Pegawai KIRI, Search KANAN --}}
  <div class="actionline">
    <a href="{{ route('absensi.create') }}" class="btn-add">
      <span class="dot">+</span> Tambah Pegawai
    </a>

    <form action="{{ route('absensi.index') }}" method="GET" class="search" role="search">
      @if(request('range')) <input type="hidden" name="range" value="{{ request('range') }}"> @endif
      <i class="fas fa-search"></i>
      <input type="text" class="form-control" name="search" placeholder="Search..." value="{{ request('search') }}" oninput="this.form.submit()">
    </form>
  </div>

  {{-- Tabel --}}
  <div class="table-responsive">
    <table class="table-soft table">
      <thead>
        <tr>
          <th>NO</th>
          <th>NAMA</th>
          <th>STATUS</th>
          <th>NO. HP</th>
          <th class="text-center">AKSI</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($absensis as $absensi)
          @php
            $nama = $absensi->pegawai->nama ?? $absensi->pegawai_nama ?? ('ID '.$absensi->pegawai_id);
            $nohp = $absensi->pegawai->no_hp ?? $absensi->no_hp ?? '-';
          @endphp
          <tr>
            <td>{{ $loop->iteration + ($absensis->currentPage() - 1) * $absensis->perPage() }}</td>
            <td>{{ $nama }}</td>
            <td>
              @if(Str::lower($absensi->status) === 'hadir')
                <span class="badge-soft badge-hadir">Hadir</span>
              @else
                <span class="badge-soft badge-absen">{{ ucfirst($absensi->status) }}</span>
              @endif
            </td>
            <td>{{ $nohp }}</td>
            <td class="text-center">
              <a href="{{ route('absensi.edit', $absensi->id) }}" class="btn-aksi btn-edit"><i class="fas fa-pen"></i> Ubah</a>
              <form action="{{ route('absensi.destroy', $absensi->id) }}" method="POST" class="d-inline">
                @csrf @method('DELETE')
                <button type="submit" class="btn-aksi btn-del" onclick="return confirm('Yakin ingin menghapus data ini?')">
                  <i class="fas fa-trash"></i> Hapus
                </button>
              </form>
              <a href="{{ route('absensi.show', $absensi->id) }}" class="btn-aksi btn-det"><i class="fas fa-info-circle"></i> Detail</a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center text-muted py-4">Data tidak ditemukan.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  <div class="d-flex justify-content-between align-items-center mt-3">
    <div class="text-muted">
      Menampilkan {{ $absensis->firstItem() ?? 0 }} - {{ $absensis->lastItem() ?? 0 }} dari total {{ $absensis->total() }} data
    </div>
    {{ $absensis->links('pagination::bootstrap-5') }}
  </div>

</div>
@endsection
