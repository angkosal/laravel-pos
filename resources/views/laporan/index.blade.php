@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h2 class="fw-bold mb-4">List Laporan</h2>

    <!-- Search Bar -->
    <div class="card p-4 shadow-sm border-0">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <form action="{{ route('laporan.index') }}" method="GET" class="w-100 d-flex align-items-center" role="search">
                <label for="search" class="me-2 fw-semibold">Search:</label>
                <input type="text" 
                       id="search"
                       name="search" 
                       class="form-control"
                       placeholder="Cari berdasarkan ID Laporan..."
                       value="{{ request('search') }}"
                       oninput="this.form.submit()"
                       style="max-width: 300px;">
            </form>
        </div>

        <!-- Tabel Laporan -->
        <table class="table align-middle mb-0">
            <thead>
                <tr class="text-muted border-bottom">
                    <th>No</th>
                    <th>ID Gaji</th>
                    <th>Tanggal Cetak</th>
                    <th>Pegawai</th>
                    <th>Total Gaji</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($laporans as $laporan)
                    <tr class="border-bottom">
                        <td>{{ ($laporans->currentPage() - 1) * $laporans->perPage() + $loop->iteration }}</td>
                        <td>{{ $laporan->id_gaji }}</td>
                        <td>
                            @if($laporan->tanggal_cetak)
                                {{ \Carbon\Carbon::parse($laporan->tanggal_cetak)->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $laporan->pegawai->nama ?? '-' }}</td>
                        <td>Rp {{ number_format($laporan->total_gaji ?? 0, 0, ',', '.') }}</td>
                        <td>
                            <a href="{{ route('laporan.edit', $laporan->id_laporan) }}" 
                               class="btn btn-sm text-white" 
                               style="background-color: #f9b72d;">
                               <i class="bi bi-pencil-square"></i> Ubah
                            </a>

                            <form action="{{ route('laporan.destroy', $laporan->id_laporan) }}" 
                                  method="POST" 
                                  style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn btn-sm text-white" 
                                        style="background-color: #f93c3c;"
                                        onclick="return confirm('Yakin ingin menghapus laporan ini?')">
                                        <i class="bi bi-trash"></i> Hapus
                                </button>
                            </form>

                            <a href="{{ route('laporan.show', $laporan->id_laporan) }}" 
                               class="btn btn-sm text-white" 
                               style="background-color: #c4c4c4;">
                               <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            Data laporan tidak ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
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
