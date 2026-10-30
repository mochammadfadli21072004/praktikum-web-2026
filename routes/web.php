<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Models\Category;
use App\Models\Product;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/about', [AboutController::class, 'index']);

Route::post('/submit', function () {
    return 'Form berhasil disubmit (POST)';
});

Route::get('/submit-test', function () {
    return view('submit-test');
});

Route::get('/user/{id}', function ($id) {
    return 'User dengan ID: ' . $id;
})->where('id', '[0-9]+');

Route::get('/hello/{name?}', function ($name = 'Tamu') {
    return 'Halo, ' . $name . '!';
});

Route::get('/akun-saya', function () {
    return 'Ini halaman Profile';
})->name('profile');

Route::get('/go-to-profile', function () {
    return redirect()->route('profile');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return 'Admin Dashboard';
    })->name('dashboard');

    Route::get('/users', function () {
        return 'Daftar User (Admin)';
    })->name('users');
});

Route::get('/cek-admin', function () {
    return redirect()->route('admin.dashboard');
});

Route::resource('photos', PhotoController::class);
Route::resource('photos-only', PhotoController::class)->only(['index', 'show']);

// ===== PRODUCTS =====
Route::get('/products/laporan', [ProductController::class, 'laporan']);

// Harus di ATAS index/show agar /products/create tidak tertangkap products/{product}
Route::middleware('auth')->group(function () {
    Route::resource('products', ProductController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy']);
});

Route::resource('products', ProductController::class)->only(['index', 'show']);

// ===== LOGIN, ROLE & MIDDLEWARE =====
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware(['guest', 'throttle:5,1']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

Route::get('/dashboard', function () {
    return view('dashboard', [
        'totalProduk' => Product::count(),
        'totalKategori' => Category::count(),
    ]);
})->middleware('auth');

Route::get('/riwayat-transaksi', function () {
    return 'Riwayat Transaksi Kasir (belum dibuat)';
})->name('pos.history')->middleware(['auth', 'role:kasir']);

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/categories', function () {
        return Category::withCount('products')->get();
    });

    Route::resource('users', UserController::class)->only(['index', 'store', 'destroy']);
});