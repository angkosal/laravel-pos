@extends('layouts.admin')

@section('title', 'Absensi Pegawai - Deeen Coffee')

@section('css')
<style>
    :root{
        --green:#1f3d34; --green-600:#294f3d; --muted:#8e9a95; --ring:#e9efec;
    }
    .container-absensi{background:#fff;border:1px solid var(--ring);border-radius:16px;padding:22px;box-shadow:0 8px 20px rgba(0,0,0,.06)}
    .page-head{display:flex;align-items:center;gap:14px;margin-bottom:12px}
    .page-head .icon-pill{width:44px;height:44px;border-radius:12px;background:#e9efec;display:flex;align-items:center;justify-content:center}
    .page-head h2{margin:0;font-weight:800;color:var(--green)}
    .btn-add{background:var(--green);border:none;border-radius:12px;padding:10px 14px;color:#fff;font-weight:700;display:inline-flex;align-items:center;gap:10px}
    .btn-add:hover{background:var(--green-600)}
    .btn-add .plus-circle{width:22px;height:22px;border-radius:50%;background:#fff;color:var(--green);display:inline-flex;align-items:center;justify-content:center;font-weight:800;font-size:14px}
    .search-wrap{position:relative;max-width:300px;width:100%}
    .search-wrap .fa-search{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#93a19b}
    .search-input{padding-left:36px;border-radius:12px;background:#fff;border:1px solid var(--ring);height:42px}
    .table-soft{width:100%;background:#fff;border-collapse:separate;border-spacing:0;margin-top:8px}
    .table-soft thead th{background:#e8efe9;color:var(--green);border:none;text-transform:uppercase;font-size:13px;font-weight:700;letter-spacing:.4px}
    .table-soft th,.table-soft td{padding:12px 14px;border-bottom:1px solid #edf1ef;vertical-align:middle}
    .table-soft tbody tr:hover{background:#f7f9f7}
    .badge-soft{border-radius:999px;padding:6px 12px;font-weight:700;font-size:12px}
    .badge-hadir{background:#e7f5ec;color:#2c7a4b}
    .badge-absen{background:#fff4e5;color:#b26b00}

    /* Aksi buttons */
    .btn-aksi{border:none;border-radius:10px;padding:6px 12px;font-weight:700;display:inline-flex;align-items:center;gap:6px}
    .btn-edit{background:#f9c76a;color:#4e3a07}
    .btn-edit:hover{background:#f3b94a}
    .btn-del{background:#f25f57;color:#fff}
    .btn-del:hover{background:#e5483e}
    .btn-det{background:#e5e7ea;color:#59606a}
    .btn-det:hover{background:#d9dbe0}

    .pagination .page-link{border-radius:10px;border:1px solid var(--ring);color:#2b2f2d}
    .pagination .page-item.active .page-link{background:var(--green);border-color:var(--green);color:#fff}
</style>
@endsection

@section('content')
<div class="container-absensi">

    {{-- Header: ikon + judul Absensi --}}
    <div class="page-head">
        <div class="icon-pill"><i class="fas fa-user"></i></div>
        <h2>Absensi</h2>
    </div>

    {{-- Action bar: tombol tambah + search by ID Absensi saja --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <a href="{{ route('absensi.create') }}" class="btn btn-add">
            <span class="plus-circle">+</span>
            Tambah Absensi
        </a>

        <form action="{{ route('absensi.index') }}" method="GET" class="search-wrap" role="search">
            <i class="fas fa-search"></i>
            <input type="text"
                   name="search"                    {{-- tetap 'search' agar tidak ubah controller --}}
                   class="form-control search-input"
                   placeholder="Cari ID Absensi…"
                   inputmode="numeric" pattern="[0-9]*"      {{-- batasi input angka --}}
                   value="{{ request('search') }}"
                   oninput="this.form.submit()">
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table-soft table">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>ID ABSENSI</th>
                    <th>PEGAWAI ID</th>
                    <th>TANGGAL</th>
                    <th>JAM MASUK</th>
                    <th>JAM KELUAR</th>
                    <th>SHIFT</th>
                    <th>STATUS</th>
                    <th class="text-center">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($absensis as $absensi)
                    @php
                        $tgl = \Carbon\Carbon::parse($absensi->tanggal)->format('Y-m-d');
                        $jm  = \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i');
                        $jk  = \Carbon\Carbon::parse($absensi->jam_keluar)->format('H:i');
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration + ($absensis->currentPage() - 1) * $absensis->perPage() }}</td>
                        <td>{{ $absensi->id_absensi }}</td>
                        <td>{{ $absensi->pegawai_id }}</td>
                        <td>{{ $tgl }}</td>
                        <td>{{ $jm }}</td>
                        <td>{{ $jk }}</td>
                        <td>{{ $absensi->shift }}</td>
                        <td>
                            @if(Str::lower($absensi->status) === 'hadir')
                                <span class="badge-soft badge-hadir">Hadir</span>
                            @else
                                <span class="badge-soft badge-absen">{{ ucfirst($absensi->status) }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('absensi.edit', $absensi->id) }}" class="btn-aksi btn-edit">
                                <i class="fas fa-pen"></i> Ubah
                            </a>
                            <form action="{{ route('absensi.destroy', $absensi->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-aksi btn-del"
                                        onclick="return confirm('Yakin ingin menghapus data ini?')">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                            <a href="{{ route('absensi.show', $absensi->id) }}" class="btn-aksi btn-det">
                                <i class="fas fa-info-circle"></i> Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">Data tidak ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination (Bootstrap 5) --}}
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted">
            Menampilkan {{ $absensis->firstItem() ?? 0 }} - {{ $absensis->lastItem() ?? 0 }} dari total {{ $absensis->total() }} data
        </div>
        {{ $absensis->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
