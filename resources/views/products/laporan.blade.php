@extends('layouts.app')

@section('title', 'Laporan Produk')

@section('content')
    <h3 class="mb-3">Laporan Produk per Kategori</h3>

    <table class="table table-striped table-bordered bg-white">
        <thead class="table-light">
            <tr>
                <th>Kategori</th>
                <th class="text-end">Jumlah Produk</th>
                <th class="text-end">Total Stok</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($laporan as $baris)
                <tr>
                    <td>{{ $baris->kategori }}</td>
                    <td class="text-end">{{ $baris->jumlah }}</td>
                    <td class="text-end">{{ $baris->total_stok ?? 0 }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center text-muted">Belum ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
