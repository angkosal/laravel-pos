@extends('layouts.admin')
@section('content-header', 'Dashboard')

@section('content')
<div class="container-fluid">

  {{-- ======== Top Summary Cards ======== --}}
  <div class="row mb-4">
    <div class="col-md-4">
      <div class="card shadow-sm rounded-3 border-0">
        <div class="card-body text-center">
          <p class="text-muted mb-1">PENGELUARAN (OUTCOME)</p>
          <h4 class="fw-bold">Rp. {{ number_format($outcome ?? 0, 0, ',', '.') }}</h4>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm rounded-3 border-0">
        <div class="card-body text-center">
          <p class="text-muted mb-1">PENDAPATAN (INCOME)</p>
          <h4 class="fw-bold">Rp. {{ number_format($income ?? 0, 0, ',', '.') }}</h4>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm rounded-3 border-0">
        <div class="card-body text-center">
          <p class="text-muted mb-1">JUMLAH TRANSAKSI</p>
          <h4 class="fw-bold">{{ $transactions ?? 0 }}</h4>
        </div>
      </div>
    </div>
  </div>

  {{-- ======== Filter + Buttons ======== --}}
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex align-items-center gap-2">
      <i class="fas fa-calendar-alt text-secondary"></i>
      <select class="form-select form-select-sm" id="filterPeriode" style="width:auto;">
        <option value="today" {{ request('periode') == 'today' ? 'selected' : '' }}>Hari Ini</option>
        <option value="week" {{ request('periode') == 'week' ? 'selected' : '' }}>Minggu Ini</option>
        <option value="month" {{ request('periode') == 'month' ? 'selected' : '' }}>Bulan Ini</option>
        <option value="year" {{ request('periode') == 'year' ? 'selected' : '' }}>Tahun Ini</option>
      </select>
    </div>

    <div class="d-flex gap-2">
      <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">Tambah Laporan</button>
      <button class="btn btn-warning btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalImport">Import Data CSV</button>
      <a href="{{ route('laporan.index') }}" class="btn btn-secondary btn-sm">Tampil Semua</a>
    </div>
  </div>

  {{-- ======== Success Message ======== --}}
  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  {{-- ======== Chart Section ======== --}}
  <div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body">
      <h5 class="mb-3 fw-semibold">Sales Analytics</h5>
      <canvas id="salesChart" height="120"></canvas>
    </div>
  </div>

  {{-- ======== List Laporan ======== --}}
  @if(isset($laporans) && count($laporans) > 0)
  <div class="card border-0 shadow-sm rounded-3">
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

{{-- ======== Modal Tambah Laporan ======== --}}
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

{{-- ======== Modal Import CSV ======== --}}
<div class="modal fade" id="modalImport" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-sm">
      <div class="modal-body">
        <h6 class="mb-3">Import CSV</h6>
        <form action="{{ route('laporan.import') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <input type="file" name="csv_file" class="form-control mb-3" accept=".csv" required>
          <div class="text-end">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-warning text-white">Import</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- ======== Script Section ======== --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.getElementById('filterPeriode').addEventListener('change', function() {
  const periode = this.value;
  window.location.href = `{{ url('admin') }}?periode=${periode}`;
});

const ctx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: ['Tue','Wed','Thu','Fri','Sat','Sun','Mon'],
    datasets: [
      {
        label: 'Expense',
        data: [120, 90, 150, 180, 200, 220, 190],
        borderColor: '#f87171',
        backgroundColor: 'rgba(248,113,113,0.1)',
        tension: 0.4,
        fill: true
      },
      {
        label: 'Income',
        data: [140, 130, 180, 210, 250, 270, 260],
        borderColor: '#34d399',
        backgroundColor: 'rgba(52,211,153,0.1)',
        tension: 0.4,
        fill: true
      }
    ]
  },
  options: {
    responsive: true,
    plugins: { legend: { position: 'top' } },
    scales: {
      y: { beginAtZero: true, grid: { drawBorder: false } },
      x: { grid: { display: false } }
    }
  }
});

// Search filter
const searchInput = document.getElementById('searchInput');
if (searchInput) {
  searchInput.addEventListener('keyup', function() {
    const filter = this.value.toLowerCase();
    document.querySelectorAll('#laporanTable tr').forEach(row => {
      const kode = row.children[1].textContent.toLowerCase();
      row.style.display = kode.includes(filter) ? '' : 'none';
    });
  });
}
</script>
@endpush
@endsection
