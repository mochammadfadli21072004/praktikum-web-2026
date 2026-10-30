@extends('layouts.app')

@section('title', 'Daftar Produk')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Daftar Produk</h3>
        <div>
            <a href="{{ url('/products/laporan') }}" class="btn btn-sm btn-outline-secondary me-1">Laporan Kategori</a>
            @can('create', \App\Models\Product::class)
                <a href="{{ route('products.create') }}" class="btn btn-sm btn-primary">+ Tambah Produk</a>
            @endcan
        </div>
    </div>

    <table class="table table-striped table-bordered bg-white">
        <thead class="table-light">
            <tr>
                <th style="width:60px">No</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th class="text-end">Harga</th>
                <th>Stok</th>
                <th style="width:220px">Aksi</th>
                <th>Gambar</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                {{-- $loop->first: baris pertama diberi warna --}}
                <tr @if ($loop->first) class="table-warning" @endif>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category->name ?? '-' }}</td>
                    <td class="text-end">@rupiah($product->price)</td>
                    <td>{{ $product->stock }} <x-badge :stok="$product->stock" /></td>
                    <td>
                        <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-primary">Detail</a>

                        @can('update', $product)
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-warning">Edit</a>
                        @endcan

                        @can('delete', $product)
                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Hapus produk ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        @endcan
                    </td>
                    <td>
                        @if ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="height: 50px;">
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted">Belum ada produk.</td></tr>
            @endforelse
        </tbody>
    </table>

    <p class="text-muted small">Total: {{ $products->count() }} produk</p>
@endsection
