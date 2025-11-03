@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Data Absensi</h2>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('absensi.create') }}" class="btn btn-primary">Tambah Absensi</a>

        <!-- Input pencarian tanpa tombol -->
        <form action="{{ route('absensi.index') }}" method="GET" class="d-flex" role="search">
            <input type="text" name="search" class="form-control"
                   placeholder="Cari berdasarkan ID Absensi, Pegawai ID, atau Status..."
                   value="{{ request('search') }}"
                   oninput="this.form.submit()"
                   style="max-width: 300px;">
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>ID Absensi</th>
                <th>Pegawai ID</th>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Jam Keluar</th>
                <th>Shift</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($absensis as $absensi)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $absensi->id_absensi }}</td>
                    <td>{{ $absensi->pegawai_id }}</td>
                    <td>{{ $absensi->tanggal }}</td>
                    <td>{{ $absensi->jam_masuk }}</td>
                    <td>{{ $absensi->jam_keluar }}</td>
                    <td>{{ $absensi->shift }}</td>
                    <td>{{ $absensi->status }}</td>
                    <td>
                        <a href="{{ route('absensi.edit', $absensi->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('absensi.destroy', $absensi->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-muted">Data tidak ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="mt-3">
        {{ $absensis->links() }}
    </div>
</div>
@endsection
