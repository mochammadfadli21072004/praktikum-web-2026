<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

/**
 * Refactor P11: logika dashboard dipindah dari closure di routes/web.php
 * ke controller supaya route tetap tipis (SRP) dan mudah diuji.
 */
class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('dashboard', [
            'totalProduk' => Product::count(),
            'totalKategori' => Category::count(),
        ]);
    }
}
