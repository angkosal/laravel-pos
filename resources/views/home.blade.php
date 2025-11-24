@extends('layouts.admin')
@section('content-header', 'Dashboard')

@section('content')
<style>
  body{ background:#e6e6e6; }

  .kpi-card{
    background:#d3d3d3; border:0; border-radius:18px;
    box-shadow:none;
  }
  .kpi-card .card-body{ padding:22px 18px; }
  .kpi-card p{ font-size:.72rem; color:#6a6a6a; margin-bottom:6px; letter-spacing:.2px; }
  .kpi-card h4{ font-weight:700; color:#222; }

  .btn-blue{ background:#0d6efd; color:#fff; border-radius:12px; font-weight:600; }

  .card-soft{ border:0; border-radius:20px; box-shadow:0 2px 10px rgba(0,0,0,.06); }
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<div class="container-fluid">

  {{-- KPI SECTION --}}
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

  {{-- BUTTON --}}
  <div class="d-flex justify-content-end mb-3">
    <a href="{{ route('laporan.index') }}" class="btn btn-blue">Kelola Laporan</a>
  </div>

  {{-- SALES ANALYTICS --}}
  <div class="card card-soft mb-4">
    <div class="card-body">
      <h5 class="mb-3 fw-semibold">Sales Analytics</h5>
      <canvas id="salesChart" height="120"></canvas>
    </div>
  </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  const labels = {!! json_encode(array_keys($chartData)) !!};
  const incomeData = {!! json_encode(array_values($chartData)) !!};

  const ctx = document.getElementById('salesChart').getContext('2d');

  new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels.length ? labels : ['Tidak ada data'],
      datasets: [
        {
          label:'Income',
          data: incomeData.length ? incomeData : [0],
          borderWidth:2,
          tension:0.4,
          borderColor:'#198754',
          backgroundColor:'rgba(25, 135, 84, 0.2)',
          fill:true
        }
      ]
    },
    options:{
      responsive:true,
      plugins:{ legend:{ position:'top' } },
      scales:{
        y:{ beginAtZero:true },
        x:{ grid:{ display:false } }
      }
    }
  });
</script>

@endpush
@endsection
