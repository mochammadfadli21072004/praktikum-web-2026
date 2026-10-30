<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $kasirs = User::where('role', User::ROLE_KASIR)->orderBy('name')->get();

        return view('users.index', compact('kasirs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8'],
        ]);

        // Role diisi manual menggunakan konstanta, bukan dari input form, agar tidak bisa dimanipulasi
        $user = new User($data);
        $user->role = User::ROLE_KASIR;
        $user->save();

        return redirect()->route('users.index')->with('status', 'Akun kasir berhasil dibuat.');
    }

    public function destroy(string $id)
    {
        User::where('role', User::ROLE_KASIR)->findOrFail($id)->delete();

        return redirect()->route('users.index')->with('status', 'Akun kasir dihapus.');
    }
}
