@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Data Absensi</h2>
    <a href="{{ route('absensi.create') }}" class="btn btn-primary mb-3">Tambah Absensi</a>

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
            @foreach ($absensis as $absensi)
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
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="mt-3">
        {{ $absensis->links() }}
    </div>
</div>
@endsection
