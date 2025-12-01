@extends('layouts.admin')
@section('content-header', 'Dashboard')

@section('content')
<style>
  body { background:#e6e6e6; }

  .kpi-card {
    background:#1E463A !important; /* Dark Green sesuai gambar */
    border:0;
    border-radius:18px;
    box-shadow:0 2px 10px rgba(0,0,0,.15);
    color:white !important;
  }
  .kpi-card .card-body { padding:22px 18px; }
  .kpi-card p,
  .kpi-card h4 {
    color:white !important;
    font-weight:600;
  }

  .btn-blue { background:#0d6efd; color:#fff; border-radius:12px; font-weight:600; }

  .card-soft { border:0; border-radius:20px; box-shadow:0 2px 10px rgba(0,0,0,.06); }
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<div class="container-fluid">

  {{-- KPI SECTION (Outcome Removed) --}}
  <div class="row g-3 mb-3">
    <div class="col-md-6">
      <div class="card kpi-card">
        <div class="card-body text-center">
          <p>PENDAPATAN (INCOME)</p>
          <h4>Rp. {{ number_format($income ?? 0, 0, ',', '.') }}</h4>
        </div>
      </div>
    </div>

    <div class="col-md-6">
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

  {{-- SALES & EMPLOYEE ANALYTICS (Side by side) --}}
  <div class="row mb-4">
    <div class="col-md-6">
      <div class="card card-soft h-100">
        <div class="card-body">
          <h5 class="mb-3 fw-semibold">Sales Analytics</h5>
          <canvas id="salesChart" height="120"></canvas>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card card-soft h-100">
        <div class="card-body">
          <h5 class="mb-3 fw-semibold">Employee Transaction Analytics</h5>
          <canvas id="pegawaiChart" height="120"></canvas>
        </div>
      </div>
    </div>
  </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  // --- Sales Analytics (Line Chart) ---
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

  // --- Employee Transaction Analytics (Bar Chart) ---
  const pegawaiLabels = {!! json_encode(array_keys($pegawaiTransaksi)) !!};
  const pegawaiData = {!! json_encode(array_values($pegawaiTransaksi)) !!};

  const ctx2 = document.getElementById('pegawaiChart').getContext('2d');

  new Chart(ctx2, {
    type: 'bar',
    data: {
      labels: pegawaiLabels.length ? pegawaiLabels : ['Tidak ada data'],
      datasets: [
        {
          label: 'Jumlah Transaksi',
          data: pegawaiData.length ? pegawaiData : [0],
          borderWidth: 1,
          backgroundColor: 'rgba(54, 162, 235, 0.5)',
          borderColor: 'rgba(54, 162, 235, 1)',
        }
      ]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: true },
      },
      scales: {
        y: { beginAtZero: true, ticks: { precision: 0 } },
        x: { grid: { display: false } }
      }
    }
  });
</script>

@endpush
@endsection
