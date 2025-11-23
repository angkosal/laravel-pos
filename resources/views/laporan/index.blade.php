@extends('layouts.admin')

@section('content')
<style>
  body{ background:#efefef; }
  .laporan-wrap{ max-width:1100px; }

  .toolbar{
    background:#e0e0e0; border-radius:12px; padding:10px 12px;
    display:flex; align-items:center; gap:10px; width:max-content;
  }
  .toolbar .iconbox{
    width:28px; height:28px; border-radius:8px; background:#bfbfbf;
    display:grid; place-items:center; color:#fff; font-size:14px;
  }
  .dropdown .pill-select{
    border:0; background:#bfbfbf; color:#fff; font-weight:700;
    border-radius:12px; padding:8px 14px; display:inline-flex; gap:8px; align-items:center;
  }
  .dropdown-toggle::after{ display:none; }
  .caret{
    display:inline-block; width:0; height:0;
    border-left:5px solid transparent; border-right:5px solid transparent;
    border-top:6px solid #ffffff; transform:translateY(1px);
  }

  .section-card{
    background:#ffffff; border:0; border-radius:16px; padding:0;
    box-shadow: 0 2px 10px rgba(0,0,0,.06);
  }
  .section-head{
    background:#e6e6e6; border-radius:16px 16px 0 0; padding:10px 16px;
    font-weight:700; color:#555; border-bottom:1px solid #dbdbdb;
  }
  .section-body{ padding:16px; }

  .search-line{ padding:6px 0 10px; }
  .form-search{ display:flex; align-items:center; gap:10px; font-weight:600; color:#60636a; }
  .search-input{
    flex:1; border-radius:12px; border:1px solid #d5d5d5; background:#fff;
    padding:12px 14px; outline:0; transition:.2s border-color;
  }
  .search-input:focus{ border-color:#1b4a3f; }

  table.table{ border-collapse:separate; border-spacing:0 18px; margin-top:4px; }
  thead th{
    font-weight:700; color:#7a7a7a; border:0 !important; padding:8px 14px;
  }
  tbody tr{ background:#fff; }
  tbody tr td{
    border-top:1px solid #ececec !important; border-bottom:1px solid #ececec !important;
    padding:16px 14px; vertical-align:middle;
  }
  tbody tr td:first-child{ border-left:1px solid #ececec; border-radius:10px 0 0 10px; width:60px; }
  tbody tr td:last-child{ border-right:1px solid #ececec; border-radius:0 10px 10px 0; }

  .btn-pill{ border-radius:12px; padding:8px 12px; border:0; font-weight:700; display:inline-flex; align-items:center; gap:8px; }
  .btn-ubah{ background:#f9c76a; color:#4e3a07; } .btn-ubah:hover{ background:#f3b94a; }
  .btn-hapus{ background:#f25f57; color:#fff; } .btn-hapus:hover{ background:#e5483e; }
  .btn-detail{ background:#e5e7ea; color:#59606a; } .btn-detail:hover{ background:#d9dbe0; }
  .btn-import{ background:#5cb85c; color:#fff; } .btn-import:hover{ background:#4cae4c; }
  .btn-export{ background:#28a745; color:#fff; } .btn-export:hover{ background:#218838; }

  .pagination .page-link{ border:0; border-radius:10px; padding:8px 12px; color:#555; }
  .pagination .page-item.active .page-link{ background:#1b4a3f; color:#fff; }
  .pagination .page-item.disabled .page-link{ color:#bcbcbc; }

  .text-money{ white-space:nowrap; }
</style>

@php
  $range = request('range', 'week');
  $label = [
    'today' => 'Hari Ini',
    'week'  => 'Minggu Ini',
    'month' => 'Bulan Ini',
    'year'  => 'Tahun Ini',
  ][$range] ?? 'Minggu Ini';
@endphp

<div class="container laporan-wrap mt-3">
  <h2 class="fw-bold mb-3" style="color:#143a31;">Dashboard</h2>

  <!-- FILTER BAR -->
  <div class="toolbar mb-3">
    <div class="iconbox"><i class="fas fa-calendar-alt"></i></div>
    <div class="dropdown">
      <button class="pill-select dropdown-toggle" type="button" data-bs-toggle="dropdown">
        {{ $label }} <span class="caret"></span>
      </button>
      <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="{{ route('laporan.index', request()->except('page') + ['range'=>'today']) }}">Hari Ini</a></li>
        <li><a class="dropdown-item" href="{{ route('laporan.index', request()->except('page') + ['range'=>'week']) }}">Minggu Ini</a></li>
        <li><a class="dropdown-item" href="{{ route('laporan.index', request()->except('page') + ['range'=>'month']) }}">Bulan Ini</a></li>
        <li><a class="dropdown-item" href="{{ route('laporan.index', request()->except('page') + ['range'=>'year']) }}">Tahun Ini</a></li>
      </ul>
    </div>
  </div>

  <div class="section-card">
    <div class="section-head d-flex justify-content-between align-items-center">
      <span>List Laporan</span>

      <div class="d-flex gap-2">

        <!-- 🎯 TOMBOL IMPORT CSV -->
        <button class="btn btn-sm btn-pill btn-import" data-bs-toggle="modal" data-bs-target="#modalImport">
          <i class="fas fa-file-import"></i> Import CSV
        </button>

        <!-- 🆕 🎯 TOMBOL GENERATE LAPORAN DARI TRANSAKSI -->
        <form action="{{ route('laporan.generate') }}" method="POST" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-sm btn-pill btn-warning text-white">
            <i class="fas fa-sync"></i> Generate dari Transaksi
          </button>
        </form>

        <!-- 🆕 🎯 TOMBOL EXPORT EXCEL (Tanpa Composer) -->
        <a href="{{ route('laporan.export.excel.manual') }}" class="btn btn-sm btn-pill btn-success text-white">
          <i class="fas fa-file-excel"></i> Export Excel
        </a>

      </div>
    </div>

    <div class="section-body">
      <!-- SEARCH FORM -->
      <div class="search-line">
        <form action="{{ route('laporan.index') }}" method="GET" class="form-search">
          <input type="hidden" name="range" value="{{ $range }}">
          <label for="search">Search:</label>
          <input type="text" id="search" name="search" class="search-input"
                 placeholder="Cari berdasarkan Kode/ID Laporan…"
                 value="{{ request('search') }}" oninput="this.form.submit()">
        </form>
      </div>

      <!-- TABLE -->
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
              <tr>
                <td>{{ ($laporans->currentPage() - 1) * $laporans->perPage() + $loop->iteration }}</td>
                <td>{{ $laporan->id_laporan }}</td>
                <td>{{ $laporan->tanggal_cetak ? \Carbon\Carbon::parse($laporan->tanggal_cetak)->format('d/m/Y') : '-' }}</td>
                <td>{{ $laporan->nama_pegawai ?? '-' }}</td>
                <td class="text-money">Rp{{ number_format($laporan->total_gaji ?? 0, 0, ',', '.') }}</td>

                <td class="text-center">
                  <a href="{{ route('laporan.edit', $laporan->id_laporan) }}" class="btn btn-sm btn-pill btn-ubah"><i class="fas fa-pen"></i> Ubah</a>
                  <form action="{{ route('laporan.destroy', $laporan->id_laporan) }}" method="POST" class="d-inline js-delete-laporan" data-kode="{{ $laporan->id_laporan }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-pill btn-hapus"><i class="fas fa-trash"></i> Hapus</button>
                  </form>
                  <a href="{{ route('laporan.show', $laporan->id_laporan) }}" class="btn btn-sm btn-pill btn-detail"><i class="fas fa-info-circle"></i> Detail</a>
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

      <!-- PAGINATION -->
      <div class="d-flex justify-content-between align-items-center mt-3 pb-1">
        <div class="text-muted">
          Menampilkan {{ $laporans->firstItem() ?? 0 }} - {{ $laporans->lastItem() ?? 0 }} dari total {{ $laporans->total() }} data
        </div>
        {{ $laporans->appends(request()->except('page'))->links() }}
      </div>
    </div>
  </div>
</div>

<!-- 📥 MODAL IMPORT CSV -->
<div class="modal fade" id="modalImport" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-sm">
      <div class="modal-body">
        <h6 class="mb-3">Import CSV</h6>
        <form action="{{ route('laporan.import.csv') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <input type="file" name="file" class="form-control mb-3" accept=".csv" required>
          <div class="text-end">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-warning text-white">Import</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
<script>
  document.addEventListener('click', function(e){
    const form = e.target.closest('.js-delete-laporan');
    if(!form) return;
    e.preventDefault();
    const kode = form.getAttribute('data-kode') || '';
    Swal.fire({
      title: 'Yakin menghapus data?',
      html: `Klik <b>Hapus</b> untuk menghapus data Laporan <b>"${kode}"</b>.`,
      icon: 'warning',
      showCancelButton: true,
      reverseButtons: true,
      confirmButtonText: 'Hapus',
      cancelButtonText: 'Batal',
      customClass: {
        confirmButton: 'btn swal2-confirm',
        cancelButton: 'btn swal2-cancel'
      },
      buttonsStyling: false
    }).then((result) => {
      if (result.isConfirmed) form.submit();
    });
  });
</script>
@endsection
