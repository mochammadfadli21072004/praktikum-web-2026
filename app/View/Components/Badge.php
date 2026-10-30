<?php

namespace App\View\Components;

use App\Enums\StockStatus;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Badge extends Component
{
    private StockStatus $status;

    /**
     * Pemakaian: <x-badge :stok="$product->stock" />
     */
    public function __construct(public int $stok = 0)
    {
        $this->status = StockStatus::fromQuantity($this->stok);
    }

    /** Teks status dari satu sumber aturan StockStatus */
    public function teks(): string
    {
        return $this->status->label();
    }

    /** Warna Bootstrap sesuai status */
    public function kelas(): string
    {
        return $this->status->badgeClass();
    }

    public function render(): View|Closure|string
    {
        return view('components.badge');
    }
}
