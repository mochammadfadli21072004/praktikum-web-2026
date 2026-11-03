@extends('layouts.app')

@section('title', 'Tambah Tugas')

@section('content')
<a href="{{ route('tasks.index') }}" class="d-inline-block mb-3">&larr; Kembali ke daftar</a>

<div class="card">
    <div class="card-body">
        <h3 class="mb-3">Tambah Tugas</h3>

        <form action="{{ route('tasks.store') }}" method="POST">
            @csrf
            @include('tasks._form')
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>
@endsection
