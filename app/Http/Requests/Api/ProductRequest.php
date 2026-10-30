<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi API produk untuk POST (wajib lengkap) dan PUT/PATCH (boleh sebagian).
 * (Refactor P9: sebelumnya aturan store dan update ditulis dua kali di Controller.)
 */
class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Autentikasi API (Sanctum) belum dibahas — lihat Temuan A-05 di laporan.
        return true;
    }

    public function rules(): array
    {
        // POST: field wajib ada. PUT/PATCH: hanya divalidasi jika dikirim.
        $wajib = $this->isMethod('POST') ? ['required'] : ['sometimes', 'required'];

        return [
            'name'        => [...$wajib, 'string', 'max:100'],
            'description' => ['sometimes', 'nullable', 'string'],
            'price'       => [...$wajib, 'numeric', 'min:0'],
            // Tidak dikirim = default database (0). Dikirim null = ditolak 422 (sebelumnya jadi error 500).
            'stock'       => ['sometimes', 'integer', 'min:0'],
            'category_id' => [...$wajib, 'exists:categories,id'],
        ];
    }
}
