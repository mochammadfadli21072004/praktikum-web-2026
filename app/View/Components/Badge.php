<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Badge extends Component
{
    /**
     * Pemakaian: <x-badge :stok="$product->stock" />
     */
    public function __construct(public int $stok = 0)
    {
    }

    /** Teks status: Habis (0), Menipis (1-10), Aman (>10) */
    public function teks(): string
    {
        return match (true) {
            $this->stok <= 0  => 'Habis',
            $this->stok <= 10 => 'Menipis',
            default           => 'Aman',
        };
    }

    /** Warna Bootstrap sesuai status */
    public function kelas(): string
    {
        return match (true) {
            $this->stok <= 0  => 'text-bg-danger',
            $this->stok <= 10 => 'text-bg-warning',
            default           => 'text-bg-success',
        };
    }

    public function render(): View|Closure|string
    {
        return view('components.badge');
    }
}
