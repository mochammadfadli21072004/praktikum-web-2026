<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PhotoController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth', only: ['destroy']),
        ];
    }

    public function index()
    {
        return 'INDEX - Menampilkan daftar semua foto';
    }

    public function create()
    {
        return 'CREATE - Form tambah foto';
    }

    public function store(Request $request)
    {
        return 'Data diterima: ' . $request->input('judul', 'tanpa judul');
    }

    public function show($id)
    {
        return 'SHOW - Menampilkan foto ID: ' . $id;
    }

    public function edit($id)
    {
        return 'EDIT - Form edit foto ID: ' . $id;
    }

    public function update(Request $request, $id)
    {
        return 'UPDATE - Foto ID ' . $id . ' diperbarui';
    }

    public function destroy($id)
    {
        return 'DESTROY - Foto ID ' . $id . ' dihapus';
    }
}