<?php

namespace App\Enums;

/**
 * Prioritas tugas. Satu sumber kebenaran untuk nilai, label, dan warna badge
 * (menghindari magic string 'low'/'medium'/'high' tersebar di banyak file).
 */
enum TaskPriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';

    public function label(): string
    {
        return match ($this) {
            self::Low => 'Rendah',
            self::Medium => 'Sedang',
            self::High => 'Tinggi',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Low => 'bg-secondary',
            self::Medium => 'bg-warning text-dark',
            self::High => 'bg-danger',
        };
    }
}
