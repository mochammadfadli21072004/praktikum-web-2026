@extends('layouts.app')

@section('title', 'Daftar Produk')

@section('content')
    <h3 class="mb-3">Daftar Produk</h3>

    <table class="table table-striped table-bordered bg-white">
        <thead class="table-light">
            <tr>
                <th style="width:60px">No</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th class="text-end">Harga</th>
                <th>Stok</th>
                <th style="width:220px">Aksi</th>
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
                        <a href="{{ url('/products/' . $product->id) }}" class="btn btn-sm btn-outline-primary">Detail</a>

                        @can('update', $product)
                            <a href="{{ url('/products/' . $product->id . '/edit') }}" class="btn btn-sm btn-outline-warning">Edit</a>
                        @endcan

                        @can('delete', $product)
                            <form action="{{ url('/products/' . $product->id) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Hapus produk ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted">Belum ada produk.</td></tr>
            @endforelse
        </tbody>
    </table>

    <p class="text-muted small">Total: {{ $products->count() }} produk</p>
@endsection
