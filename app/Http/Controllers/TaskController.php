<?php

namespace App\Http\Controllers;

use App\Enums\TaskPriority;
use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Mini Project Tahap 1 - Task Manager.
 * Semua route berada di grup middleware 'auth' (lihat routes/web.php).
 */
class TaskController extends Controller
{
    public function index(Request $request)
    {
        // Hanya tugas milik user yang login; yang belum selesai tampil lebih dulu.
        $tasks = $request->user()->tasks()
            ->orderBy('is_completed')
            ->latest()
            ->get();

        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        return view('tasks.create', ['priorities' => TaskPriority::cases()]);
    }

    public function store(TaskRequest $request): RedirectResponse
    {
        // Lewat relasi: user_id terisi otomatis dari user login.
        $request->user()->tasks()->create($request->validated());

        return redirect()->route('tasks.index')->with('status', 'Tugas berhasil ditambahkan.');
    }

    public function edit(Task $task)
    {
        $this->authorize('update', $task);

        return view('tasks.edit', ['task' => $task, 'priorities' => TaskPriority::cases()]);
    }

    public function update(TaskRequest $request, Task $task): RedirectResponse
    {
        $task->update($request->validated());

        return redirect()->route('tasks.index')->with('status', 'Tugas berhasil diperbarui.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $this->authorize('delete', $task);

        $task->delete();

        return redirect()->route('tasks.index')->with('status', 'Tugas berhasil dihapus.');
    }

    /**
     * Tandai selesai / batal selesai (fitur Must: "Tandai tugas selesai").
     */
    public function toggleComplete(Task $task): RedirectResponse
    {
        $this->authorize('update', $task);

        $task->update(['is_completed' => ! $task->is_completed]);

        return redirect()->route('tasks.index')->with(
            'status',
            $task->is_completed ? 'Tugas ditandai selesai.' : 'Tugas dikembalikan ke belum selesai.'
        );
    }
}
