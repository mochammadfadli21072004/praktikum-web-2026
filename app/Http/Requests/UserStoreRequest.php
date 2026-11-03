<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi pembuatan akun kasir oleh admin.
 * (Refactor P11: sebelumnya validasi ditulis inline di UserController::store.)
 *
 * Catatan keamanan: field "role" sengaja TIDAK ada di rules(), jadi tidak akan
 * pernah muncul di validated() walaupun penyerang mengirimnya.
 */
class UserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('kelola-user') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            // max:72 -> bcrypt hanya memakai 72 byte pertama, sekaligus membatasi payload berlebihan
            'password' => ['required', 'string', 'min:8', 'max:72'],
        ];
    }
}
