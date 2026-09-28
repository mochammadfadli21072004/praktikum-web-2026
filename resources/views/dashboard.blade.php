@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h3 class="mb-1">Dashboard</h3>
    <p class="text-muted">
        Halo, <strong>{{ auth()->user()->name }}</strong> (role: {{ auth()->user()->role }})
    </p>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <div class="display-6">{{ $totalProduk }}</div>
                    <div class="text-muted">Total Produk</div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <div class="display-6">{{ $totalKategori }}</div>
                    <div class="text-muted">Total Kategori</div>
                </div>
            </div>
        </div>
    </div>

    @if (auth()->user()->role === 'admin')
        <x-alert type="info">
            Anda login sebagai <strong>admin</strong>. Menu
            <a href="{{ url('/users') }}">Kelola Kasir</a> dan
            <a href="{{ url('/categories') }}">/categories</a> tersedia untuk Anda.
        </x-alert>
    @else
        <x-alert type="warning">
            Anda login sebagai <strong>{{ auth()->user()->role }}</strong>.
            Beberapa menu hanya untuk admin.
        </x-alert>
    @endif
@endsection
