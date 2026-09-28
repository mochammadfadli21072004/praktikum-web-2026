@extends('layouts.app')

@section('title', 'Edit Produk')

@section('content')
    <a href="{{ url('/products') }}" class="text-decoration-none">&larr; Kembali ke daftar</a>

    <div class="card shadow-sm mt-3">
        <div class="card-body">
            <h4 class="card-title mb-3">Edit Produk</h4>

            <form action="{{ url('/products/' . $product->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Harga</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Stok</label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" class="form-select">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                @selected(old('category_id', $product->category_id) == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
@endsection
