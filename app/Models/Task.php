<?php

namespace App\Models;

use App\Enums\TaskPriority;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    // user_id SENGAJA tidak ada di $fillable: pemilik selalu diambil dari user yang login
    // (lewat $user->tasks()->create()), bukan dari input form -> cegah mass assignment.
    protected $fillable = ['title', 'description', 'priority', 'is_completed'];

    protected function casts(): array
    {
        return [
            'priority' => TaskPriority::class,
            'is_completed' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
