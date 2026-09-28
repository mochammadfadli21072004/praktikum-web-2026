<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Alert extends Component
{
    /**
     * Properti public otomatis menjadi atribut Blade: <x-alert type="error">
     */
    public function __construct(public string $type = 'info')
    {
    }

    /**
     * Ubah tipe (success/error/warning/info) jadi kelas warna Bootstrap.
     */
    public function kelas(): string
    {
        return match ($this->type) {
            'success' => 'alert-success',
            'error'   => 'alert-danger',
            'warning' => 'alert-warning',
            default   => 'alert-info',
        };
    }

    public function render(): View|Closure|string
    {
        return view('components.alert');
    }
}
