<?php

use App\Http\Controllers\Api\V1;
use App\Http\Controllers\Api\V2;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (Pertemuan 7)
|--------------------------------------------------------------------------
| File ini otomatis berawalan /api dan memakai grup middleware "api"
| (stateless, tanpa session & CSRF, ada throttle).
*/

// ---- Versi 1: /api/v1/... ----
Route::prefix('v1')->name('v1.')->group(function () {
    // Rute khusus HARUS di atas apiResource, kalau tidak "latest" dianggap {product}
    Route::get('/products/latest', [V1\ProductController::class, 'latest'])->name('products.latest');

    // 5 rute CRUD (tanpa create & edit)
    Route::apiResource('products', V1\ProductController::class);
    Route::apiResource('categories', V1\CategoryController::class)->only(['index', 'show']);
});

// ---- Versi 2: /api/v2/... (bentuk response berubah, v1 tetap jalan) ----
Route::prefix('v2')->name('v2.')->group(function () {
    Route::apiResource('products', V2\ProductController::class)->only(['index', 'show']);
});
