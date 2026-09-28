@extends('layouts.app')

@section('title', 'Kelola Akun Kasir')

@section('content')
    <h3 class="mb-3">Kelola Akun Kasir</h3>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">Tambah Kasir</h5>
            <form action="{{ url('/users') }}" method="POST" class="row g-2">
                @csrf
                <div class="col-md-3">
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Nama">
                </div>
                <div class="col-md-4">
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="Email">
                </div>
                <div class="col-md-3">
                    <input type="password" name="password" class="form-control" placeholder="Password (min. 8)">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <h5>Daftar Kasir</h5>
    <table class="table table-bordered bg-white">
        <thead class="table-light">
            <tr>
                <th style="width:60px">No</th>
                <th>Nama</th>
                <th>Email</th>
                <th style="width:120px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($kasirs as $kasir)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $kasir->name }}</td>
                    <td>{{ $kasir->email }}</td>
                    <td>
                        <form action="{{ url('/users/' . $kasir->id) }}" method="POST"
                              onsubmit="return confirm('Hapus akun ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted">Belum ada akun kasir.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
