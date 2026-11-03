@extends('layouts.app')

@section('title', 'Tugas Saya')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Tugas Saya</h3>
        <a href="{{ route('tasks.create') }}" class="btn btn-sm btn-primary">+ Tambah Tugas</a>
    </div>

    <table class="table table-striped table-bordered bg-white">
        <thead class="table-light">
            <tr>
                <th style="width:60px">No</th>
                <th>Judul</th>
                <th>Prioritas</th>
                <th>Status</th>
                <th style="width:260px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tasks as $task)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td @class(['text-decoration-line-through text-muted' => $task->is_completed])>
                        {{ $task->title }}
                        @if ($task->description)
                            <div class="small text-muted">{{ $task->description }}</div>
                        @endif
                    </td>
                    <td><span class="badge {{ $task->priority->badgeClass() }}">{{ $task->priority->label() }}</span></td>
                    <td>{{ $task->is_completed ? 'Selesai' : 'Belum selesai' }}</td>
                    <td>
                        <form action="{{ route('tasks.complete', $task) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-outline-success">
                                {{ $task->is_completed ? 'Batal Selesai' : 'Tandai Selesai' }}
                            </button>
                        </form>

                        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-outline-warning">Edit</a>

                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Hapus tugas ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted">Belum ada tugas.</td></tr>
            @endforelse
        </tbody>
    </table>

    <p class="text-muted small">Total: {{ $tasks->count() }} tugas</p>
@endsection
