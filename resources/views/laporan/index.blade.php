@extends('layouts.admin')

@section('content')
@section('css')
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
  .btn-filter{ background:#007bff; color:#fff; } .btn-filter:hover{ background:#0062cc; }

  .pagination .page-link{ border:0; border-radius:10px; padding:8px 12px; color:#555; }
  .pagination .page-item.active .page-link{ background:#1b4a3f; color:#fff; }
  .pagination .page-item.disabled .page-link{ color:#bcbcbc; }

  .text-money{ white-space:nowrap; }

  /* SweetAlert custom buttons */
  .swal2-popup {
      border-radius: 18px !important;
  }
  .swal2-title {
      font-weight:800 !important;
      color: #143a31 !important;
  }
  .swal2-html-container {
      color:#4f5a56 !important;
  }
  .btn-swal-cancel {
      background:#d0d5d2 !important;
      color:#4b4f4d !important;
      padding:8px 14px !important;
      border-radius:10px !important;
      font-weight:700 !important;
  }
  .btn-swal-confirm {
      background:#1b4a3f !important;
      color:#fff !important;
      padding:8px 16px !important;
      border-radius:10px !important;
      font-weight:800 !important;
  }
</style>
@endsection

<div class="container laporan-wrap mt-3">
  <h2 class="fw-bold mb-3" style="color:#143a31;">Dashboard</h2>

  <!-- FILTER BULAN & TAHUN -->
  <form method="GET" action="{{ route('laporan.index') }}" class="d-flex gap-2 mb-3">
    <select name="month" class="form-control" style="max-width: 180px" onchange="this.form.submit()">
      <option value="">-- Pilih Bulan --</option>
      @for ($m = 1; $m <= 12; $m++)
        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
          {{ DateTime::createFromFormat('!m', $m)->format('F') }}
        </option>
      @endfor
    </select>

    <select name="year" class="form-control" style="max-width: 130px" onchange="this.form.submit()">
      <option value="">-- Pilih Tahun --</option>
      @foreach(range(date('Y'), date('Y') - 5) as $y)
        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
          {{ $y }}
        </option>
      @endforeach
    </select>

    <a href="{{ route('laporan.index') }}" class="btn btn-filter">Reset</a>
  </form>

  <div class="section-card">
    <div class="section-head d-flex justify-content-between align-items-center">
      <span>List Laporan</span>

      <div class="d-flex gap-2">

        <button class="btn btn-sm btn-pill btn-import" data-bs-toggle="modal" data-bs-target="#modalImport">
          <i class="fas fa-file-import"></i> Import CSV
        </button>

        <form action="{{ route('laporan.generate') }}" method="POST" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-sm btn-pill btn-warning text-white">
            <i class="fas fa-sync"></i> Generate dari Transaksi
          </button>
        </form>

        <a href="{{ route('laporan.export.excel.manual') }}" class="btn btn-sm btn-pill btn-success text-white">
          <i class="fas fa-file-excel"></i> Export Excel
        </a>
      </div>
    </div>

    <div class="section-body">

      <div class="search-line">
        <form action="{{ route('laporan.index') }}" method="GET" class="form-search">
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
                  <a href="{{ route('laporan.edit', $laporan->id_laporan) }}" class="btn btn-sm btn-pill btn-ubah">
                    <i class="fas fa-pen"></i> Ubah
                  </a>

                  <form action="{{ route('laporan.destroy', $laporan->id_laporan) }}" method="POST"
                        class="d-inline js-delete-laporan" data-kode="{{ $laporan->id_laporan }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-pill btn-hapus">
                      <i class="fas fa-trash"></i> Hapus
                    </button>
                  </form>

                  <a href="{{ route('laporan.show', $laporan->id_laporan) }}" class="btn btn-sm btn-pill btn-detail">
                    <i class="fas fa-info-circle"></i> Detail
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

      <div class="d-flex justify-content-between align-items-center mt-3 pb-1">
        <div class="text-muted">
          Menampilkan {{ $laporans->firstItem() ?? 0 }} - {{ $laporans->lastItem() ?? 0 }} dari total {{ $laporans->total() }} data
        </div>
        {{ $laporans->appends(request()->except('page'))->links() }}
      </div>
    </div>
  </div>
</div>

<!-- MODAL IMPORT CSV -->
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

<div class="offcanvas offcanvas-end" tabindex="-1" id="historyImport">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">History Import CSV</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>

  <div class="offcanvas-body">
    @if(isset($histories) && $histories->count() > 0)
      @foreach($histories as $h)
        <div class="border rounded p-2 mb-2">
          <div><b>{{ $h->file_name }}</b></div>
          <div>{{ $h->row_count }} baris</div>
          <div class="text-muted" style="font-size:12px">
            {{ \Carbon\Carbon::parse($h->imported_at)->format('d/m/Y H:i') }}
          </div>
        </div>
      @endforeach
    @else
      <div class="text-muted text-center">Belum ada history import.</div>
    @endif
  </div>
</div>

<!-- 🕘 🎯 TOMBOL HISTORY IMPORT -->
<button type="button" class="btn btn-sm btn-pill btn-secondary text-white"
        data-bs-toggle="offcanvas" data-bs-target="#historyImport">
    <i class="fas fa-history"></i> History Import
</button>

@endsection

@section('js')
<script>
  // SweetAlert delete confirmation for laporan
  document.addEventListener('click', function(e){
    const form = e.target.closest('.js-delete-laporan');
    if(!form) return;

    e.preventDefault();
    const kode = form.getAttribute('data-kode') || '';

    Swal.fire({
      icon: 'warning',
      title: 'Yakin menghapus data?',
      html: `Klik <b>Hapus</b> untuk menghapus Laporan <b>"${kode}"</b>.`,
      showCancelButton: true,
      confirmButtonText: 'Hapus',
      cancelButtonText: 'Batal',
      reverseButtons: true,
      buttonsStyling: false,
      customClass: {
        confirmButton: 'btn-swal-confirm',
        cancelButton: 'btn-swal-cancel'
      }
    }).then((result) => {
      if (result.isConfirmed) {
        form.submit();
      }
    });
  });
</script>
@endsection
