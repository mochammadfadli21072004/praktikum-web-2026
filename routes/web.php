<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhotoController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/about', function () {
    return '
        <h2>Profil Toko</h2>
        <p><strong>Nama Toko:</strong> Fadli Store</p>
        <p><strong>Deskripsi:</strong> Menjual berbagai kebutuhan elektronik dan aksesoris dengan harga terjangkau.</p>
        <p><strong>Berdiri sejak:</strong> 2024</p>
    ';
});

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

Route::resource('photos', PhotoController::class);