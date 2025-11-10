@extends('layouts.admin')

@section('content')
<style>
  /* ====== Look & Feel ala Figma ====== */
  body{ background:#f5f6f7; }
  .laporan-wrap{ max-width:1100px; }

  /* TOP FILTER BAR ala Figma (Minggu Ini + caret) */
  .top-filter{
    background:#e9eaec; border-radius:10px; padding:10px 12px;
    display:flex; align-items:center; justify-content:space-between; gap:12px;
  }
  .top-left{
    display:flex; align-items:center; gap:10px; font-weight:600; color:#555;
  }
  .top-left .dot{
    width:22px;height:22px;border-radius:8px;background:#dcdcdc;display:grid;place-items:center;
    font-size:12px;color:#666;
  }
  .range-select{
    appearance:none; -moz-appearance:none; -webkit-appearance:none;
    border:0; background:#fff; color:#333; font-weight:600;
    padding:8px 38px 8px 12px; border-radius:12px; outline:0; cursor:pointer;
    min-width:150px;
  }
  .select-wrap{
    position:relative; display:inline-block;
  }
  .select-wrap::after{
    content:"\f282"; /* bi-caret-down-fill */
    font-family: "bootstrap-icons";
    position:absolute; right:12px; top:50%; transform:translateY(-50%);
    font-size:14px; color:#6b6f76; pointer-events:none;
  }

  .section-card{
    background:#ffffff; border:0; border-radius:16px; padding:18px 18px 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,.04);
  }

  .search-line{ padding:10px 0 6px; }
  .form-search{
    width:100%; max-width: 620px; display:flex; align-items:center; gap:10px;
    font-weight:600; color:#60636a;
  }
  .form-search label{ margin:0; }
  .search-input{
    flex:1; border-radius:12px; border:1px solid #ddd; background:#fff;
    padding:10px 14px; outline:0; transition:.2s border-color;
  }
  .search-input:focus{ border-color:#1b4a3f; }

  table.table{ border-collapse:separate; border-spacing:0 10px; }
  thead th{
    font-weight:600; color:#7a7a7a; border:0 !important; padding:10px 14px;
  }
  tbody tr{ background:#fff; box-shadow: 0 1px 0 rgba(0,0,0,.06); }
  tbody tr td{
    border-top:1px solid #ececec !important; border-bottom:1px solid #ececec !important;
    padding:14px; vertical-align:middle;
  }
  tbody tr td:first-child{ border-left:1px solid #ececec; border-radius:10px 0 0 10px; width:60px; }
  tbody tr td:last-child{ border-right:1px solid #ececec; border-radius:0 10px 10px 0; }

  .btn-pill{ border-radius:12px; padding:8px 12px; border:0; font-weight:600; display:inline-flex; align-items:center; gap:6px; }
  .btn-ubah{ background:#f9b72d; color:#fff; }
  .btn-hapus{ background:#f93c3c; color:#fff; }
  .btn-detail{ background:#c4c4c4; color:#fff; }
  .btn-pill i{ font-size:14px; }

  .pagination .page-link{ border:0; border-radius:10px; padding:8px 12px; color:#555; }
  .pagination .page-item.active .page-link{ background:#1b4a3f; color:#fff; }
  .pagination .page-item.disabled .page-link{ color:#bcbcbc; }

  .text-money{ white-space:nowrap; }
</style>

@php
  $range = request('range', 'week'); // default "Minggu Ini"
@endphp

<div class="container laporan-wrap mt-3">
  <h2 class="fw-bold mb-3" style="color:#143a31;">Dashboard</h2>

  <!-- ====== TOP FILTER BAR (mengganti header 'List Laporan') ====== -->
  <div class="top-filter mb-3">
    <div class="top-left">
      <div class="dot"><i class="bi bi-list"></i></div>
      <div>Periode</div>
    </div>

    <form action="{{ route('laporan.index') }}" method="GET" id="rangeForm">
      {{-- Pertahankan keyword saat ganti range --}}
      @if(request('search'))
        <input type="hidden" name="search" value="{{ request('search') }}">
      @endif

      <div class="select-wrap">
        <select name="range" class="range-select" onchange="document.getElementById('rangeForm').submit()">
          <option value="today" {{ $range==='today'?'selected':'' }}>Hari Ini</option>
          <option value="week"  {{ $range==='week'?'selected':''  }}>Minggu Ini</option>
          <option value="month" {{ $range==='month'?'selected':'' }}>Bulan Ini</option>
          <option value="year"  {{ $range==='year'?'selected':''  }}>Tahun Ini</option>
        </select>
      </div>
    </form>
  </div>
  <!-- ====== END TOP FILTER BAR ====== -->

  <div class="section-card">
    <!-- Search Bar -->
    <div class="search-line">
      <form action="{{ route('laporan.index') }}" method="GET" class="form-search" role="search">
        <input type="hidden" name="range" value="{{ $range }}">
        <label for="search">Search:</label>
        <input
          type="text"
          id="search"
          name="search"
          class="search-input"
          placeholder="Cari berdasarkan Kode/ID Laporan…"
          value="{{ request('search') }}"
          oninput="this.form.submit()"
        >
      </form>
    </div>

    <!-- Tabel Laporan -->
    <div class="table-responsive mt-2">
      <table class="table align-middle mb-0">
        <thead>
          <tr class="text-muted">
            <th>No</th>
            <th>Kode</th>
            <th>Tanggal</th>
            <th>Pegawai</th>
            <th>Total Pendapatan</th>
            <th class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($laporans as $laporan)
            @php
              $kode = $laporan->kode ?? $laporan->id_laporan ?? $laporan->id_gaji ?? '-';
            @endphp
            <tr>
              <td>{{ ($laporans->currentPage() - 1) * $laporans->perPage() + $loop->iteration }}</td>
              <td>{{ $kode }}</td>
              <td>
                @if($laporan->tanggal_cetak)
                  {{ \Carbon\Carbon::parse($laporan->tanggal_cetak)->format('d/m/Y') }}
                @else
                  -
                @endif
              </td>
              <td>{{ $laporan->pegawai->nama ?? '-' }}</td>
              <td class="text-money">Rp{{ number_format($laporan->total_gaji ?? 0, 0, ',', '.') }}</td>
              <td class="text-center">
                <a href="{{ route('laporan.edit', $laporan->id_laporan) }}" class="btn btn-sm btn-pill btn-ubah">
                  <i class="bi bi-pencil-square"></i> Ubah
                </a>

                <form action="{{ route('laporan.destroy', $laporan->id_laporan) }}" method="POST" class="d-inline">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-pill btn-hapus"
                          onclick="return confirm('Yakin ingin menghapus laporan ini?')">
                    <i class="bi bi-trash"></i> Hapus
                  </button>
                </form>

                <a href="{{ route('laporan.show', $laporan->id_laporan) }}" class="btn btn-sm btn-pill btn-detail">
                  <i class="bi bi-eye"></i> Detail
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted py-4">Data laporan tidak ditemukan.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-3 pb-2">
      <div class="text-muted">
        Menampilkan {{ $laporans->firstItem() ?? 0 }} - {{ $laporans->lastItem() ?? 0 }} dari total {{ $laporans->total() }} data
      </div>

      <nav>
        <ul class="pagination mb-0">
          <li class="page-item {{ $laporans->onFirstPage() ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $laporans->previousPageUrl() ?? '#' }}">Previous</a>
          </li>

          @php
            $start = max($laporans->currentPage() - 1, 1);
            $end = min($laporans->currentPage() + 1, $laporans->lastPage());
          @endphp

          @for ($page = $start; $page <= $end; $page++)
            <li class="page-item {{ $page == $laporans->currentPage() ? 'active' : '' }}">
              <a class="page-link" href="{{ $laporans->url($page) }}">{{ $page }}</a>
            </li>
          @endfor

          <li class="page-item {{ !$laporans->hasMorePages() ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $laporans->nextPageUrl() ?? '#' }}">Next</a>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</div>
@endsection
