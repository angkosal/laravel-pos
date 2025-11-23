@extends('layouts.admin')
@section('content-header', 'Dashboard')

@section('content')
<style>
  /* ====== Style Ringan ala Figma ====== */
  body{ background:#e6e6e6; }

  /* KPI Cards */
  .kpi-card{
    background:#d3d3d3; border:0; border-radius:18px;
    box-shadow:none;
  }
  .kpi-card .card-body{ padding:22px 18px; }
  .kpi-card p{ font-size:.72rem; color:#6a6a6a; margin-bottom:6px; letter-spacing:.2px; }
  .kpi-card h4{ font-weight:700; color:#222; }

  /* Filter Row (ikon + dropdown) */
  .filter-bar{ display:flex; align-items:center; gap:10px; }
  .mini-icon{
    width:28px; height:28px; border-radius:7px; background:#bfbfbf;
    display:grid; place-items:center; color:#fff; font-size:14px;
  }
  .pill{
    background:#bfbfbf; color:#fff; border:0; border-radius:12px;
    padding:8px 12px; font-weight:600; display:inline-flex; align-items:center; gap:8px;
  }

  /* Buttons */
  .btn-blue{ background:#0d6efd; color:#fff; border-radius:12px; font-weight:600; }
  .btn-gray{ background:#bfbfbf; color:#fff; border-radius:12px; font-weight:600; }

  .card-soft{ border:0; border-radius:20px; box-shadow:0 2px 10px rgba(0,0,0,.06); }
</style>

{{-- Tambah link Bootstrap Icons agar ikon kalender tampil --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

@php
  $periode = request('periode','week');
  $labelPeriode = [
    'today' => 'Hari Ini',
    'week'  => 'Minggu Ini',
    'month' => 'Bulan Ini',
    'year'  => 'Tahun Ini',
  ][$periode] ?? 'Minggu Ini';
@endphp

<div class="container-fluid">

  {{-- ======== KPI Section ======== --}}
  <div class="row g-3 mb-3">
    <div class="col-md-4">
      <div class="card kpi-card">
        <div class="card-body text-center">
          <p>PENGELUARAN (OUTCOME)</p>
          <h4>Rp. {{ number_format($outcome ?? 0, 0, ',', '.') }}</h4>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card kpi-card">
        <div class="card-body text-center">
          <p>PENDAPATAN (INCOME)</p>
          <h4>Rp. {{ number_format($income ?? 0, 0, ',', '.') }}</h4>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card kpi-card">
        <div class="card-body text-center">
          <p>JUMLAH TRANSAKSI</p>
          <h4>{{ $transactions ?? 0 }}</h4>
        </div>
      </div>
    </div>
  </div>

  {{-- ======== FILTER + BUTTON BAR ======== --}}
  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div class="filter-bar">
      <div class="mini-icon"><i class="bi bi-calendar-event"></i></div>

      <div class="dropdown">
        <button class="pill dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
          {{ $labelPeriode }}
        </button>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['periode'=>'today']) }}">Hari Ini</a></li>
          <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['periode'=>'week']) }}">Minggu Ini</a></li>
          <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['periode'=>'month']) }}">Bulan Ini</a></li>
          <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['periode'=>'year']) }}">Tahun Ini</a></li>
        </ul>
      </div>
    </div>

    {{-- Tombol lainnya tetap ada, KECUALI Import --}}
    <div class="d-flex gap-2">
      <button class="btn btn-blue" data-bs-toggle="modal" data-bs-target="#modalTambah">Tambah Laporan</button>
      <a href="{{ route('laporan.index') }}" class="btn btn-gray">Tampil Semua</a>
    </div>
  </div>

  {{-- ======== Chart Section ======== --}}
  <div class="card card-soft mb-4">
    <div class="card-body">
      <h5 class="mb-3 fw-semibold">Sales Analytics</h5>
      <canvas id="salesChart" height="120"></canvas>
    </div>
  </div>

  {{-- ======== List Laporan ======== --}}
  @if(isset($laporans) && count($laporans) > 0)
  <div class="card card-soft">
    <div class="card-body">
      <h5 class="fw-semibold mb-3">List Laporan</h5>
      <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search Kode...">

      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>No</th>
              <th>ID Gaji</th>
              <th>Tanggal</th>
              <th>Pegawai</th>
              <th>Total Pendapatan</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="laporanTable">
            @foreach($laporans as $i => $laporan)
              <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $laporan->id_gaji }}</td>
                <td>{{ $laporan->tanggal }}</td>
                <td>{{ $laporan->pegawai }}</td>
                <td>Rp. {{ number_format($laporan->total_pendapatan, 0, ',', '.') }}</td>
                <td>
                  <button class="btn btn-warning btn-sm text-white"><i class="fas fa-edit"></i> Ubah</button>
                  <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Hapus</button>
                  <button class="btn btn-secondary btn-sm" disabled><i class="fas fa-info-circle"></i> Detail</button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @else
  <div class="text-center text-muted mt-4">
    <p>Belum ada laporan yang tersedia.</p>
  </div>
  @endif
</div>

{{-- ======== MODAL Tambah Laporan tetap ada ======== --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-sm">
      <div class="modal-body">
        <h6 class="mb-3">Tambah Laporan</h6>
        <form action="{{ route('laporan.store') }}" method="POST">
          @csrf
          <div class="mb-3">
            <label for="id_gaji" class="form-label">Kode Laporan</label>
            <input type="text" name="id_gaji" id="id_gaji" class="form-control" placeholder="Masukkan Kode Laporan" required>
          </div>
          <div class="text-end">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- ======== SCRIPTS ======== --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const ctx = document.getElementById('salesChart').getContext('2d');
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['Tue','Wed','Thu','Fri','Sat','Sun','Mon'],
      datasets: [
        { label:'Expense', data:[120,90,150,180,200,220,190],
          borderColor:'#1f2937', backgroundColor:'rgba(31,41,55,.08)', tension:.4, fill:true },
        { label:'Income', data:[140,130,180,210,250,270,260],
          borderColor:'#2dd4bf', backgroundColor:'rgba(45,212,191,.12)', tension:.4, fill:true }
      ]
    },
    options:{
      responsive:true, plugins:{ legend:{ position:'top' } },
      scales:{ y:{ beginAtZero:true, grid:{ drawBorder:false } }, x:{ grid:{ display:false } } }
    }
  });

  // Search filter
  const s = document.getElementById('searchInput');
  if (s) {
    s.addEventListener('keyup', function(){
      const q = this.value.toLowerCase();
      document.querySelectorAll('#laporanTable tr').forEach(tr=>{
        const kode = tr.children[1]?.textContent.toLowerCase() || '';
        tr.style.display = kode.includes(q) ? '' : 'none';
      });
    });
  }
</script>
@endpush
@endsection
