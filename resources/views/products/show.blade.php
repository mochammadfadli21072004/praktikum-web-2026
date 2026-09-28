@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <a href="{{ url('/products') }}" class="text-decoration-none">&larr; Kembali ke daftar</a>

    <div class="card shadow-sm mt-3">
        <div class="card-body">
            <h4 class="card-title">{{ $product->name }}</h4>
            <p class="text-muted mb-2">Kategori: {{ $product->category->name ?? '-' }}</p>
            <p>{{ $product->description ?? 'Tidak ada deskripsi.' }}</p>
            <h5 class="text-success">@rupiah($product->price)</h5>
            <p class="mb-0">Stok: {{ $product->stock }} <x-badge :stok="$product->stock" /></p>
        </div>
    </div>
@endsection
