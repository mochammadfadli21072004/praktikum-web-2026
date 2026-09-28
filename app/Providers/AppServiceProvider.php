<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Data global: $appName tersedia di SEMUA view (Materi 5)
        View::share('appName', config('app.name'));

        // Directive kustom: @rupiah($angka) => Rp 1.250.000
        Blade::directive('rupiah', function ($expression) {
            return "<?php echo 'Rp ' . number_format((float) ($expression), 0, ',', '.'); ?>";
        });
    }
}
