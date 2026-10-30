<?php

namespace App\Providers;

use App\Models\User;
use App\Support\PriceFormatter;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
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
        // Data global: $appName tersedia di SEMUA view (Pertemuan 5)
        View::share('appName', config('app.name'));

        // Directive kustom: @rupiah($angka) => Rp 1.250.000
        Blade::directive('rupiah', function ($expression) {
            return "<?php echo \\" . PriceFormatter::class . "::rupiah($expression); ?>";
        });

        // ---- Gates (Pertemuan 6): aturan otorisasi sederhana ----
        Gate::define('kelola-user', fn (User $user) => $user->isAdmin());
        Gate::define('lihat-riwayat', fn (User $user) => $user->isKasir());

        // ProductPolicy otomatis ditemukan Laravel (App\Policies\ProductPolicy)
    }
}
