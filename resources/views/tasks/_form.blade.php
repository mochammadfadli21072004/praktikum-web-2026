{{-- Partial form dipakai bersama create & edit (hindari duplikasi markup) --}}
<div class="mb-3">
    <label class="form-label">Judul</label>
    <input type="text" name="title" value="{{ old('title', $task->title ?? '') }}"
           class="form-control @error('title') is-invalid @enderror">
    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">Deskripsi (Opsional)</label>
    <textarea name="description" rows="3"
              class="form-control @error('description') is-invalid @enderror">{{ old('description', $task->description ?? '') }}</textarea>
    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">Prioritas</label>
    <select name="priority" class="form-select @error('priority') is-invalid @enderror">
        @foreach ($priorities as $priority)
            <option value="{{ $priority->value }}" @selected(old('priority', isset($task) ? $task->priority->value : 'medium') === $priority->value)>
                {{ $priority->label() }}
            </option>
        @endforeach
    </select>
    @error('priority') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
