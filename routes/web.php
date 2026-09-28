<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ProductController;

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

// Parameter wajib + regex constraint
Route::get('/user/{id}', function ($id) {
    return 'User dengan ID: ' . $id;
})->where('id', '[0-9]+');

// Parameter opsional
Route::get('/hello/{name?}', function ($name = 'Tamu') {
    return 'Halo, ' . $name . '!';
});

Route::get('/akun-saya', function () {
    return 'Ini halaman Profile';
})->name('profile');

// Contoh redirect pakai nama rute
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

// Resource lengkap (7 rute CRUD)
Route::resource('photos', PhotoController::class);

// Resource sebagian - hanya index & show (Materi 2, Pertemuan 3)
Route::resource('photos-only', PhotoController::class)->only(['index', 'show']);

Route::get('/products/laporan', [ProductController::class, 'laporan']);
Route::resource('products', ProductController::class)->only(['index', 'show']);