@extends('layouts.app')

@section('title', 'Akses Ditolak')

@section('content')
    <div class="text-center mt-5">
        <h1 class="display-5">403 - Akses Ditolak</h1>
        <p>
            Akun Anda berperan <strong>{{ $role }}</strong>,
            sedangkan halaman ini khusus <strong>{{ $dibutuhkan }}</strong>.
        </p>
        <a href="{{ url('/dashboard') }}" class="btn btn-primary">Kembali ke Dashboard</a>
    </div>
@endsection
