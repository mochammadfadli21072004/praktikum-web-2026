@extends('layouts.app')

@section('title', 'Edit Tugas')

@section('content')
<a href="{{ route('tasks.index') }}" class="d-inline-block mb-3">&larr; Kembali ke daftar</a>

<div class="card">
    <div class="card-body">
        <h3 class="mb-3">Edit Tugas</h3>

        <form action="{{ route('tasks.update', $task) }}" method="POST">
            @csrf
            @method('PUT')
            @include('tasks._form')
            <button type="submit" class="btn btn-primary">Perbarui</button>
        </form>
    </div>
</div>
@endsection
