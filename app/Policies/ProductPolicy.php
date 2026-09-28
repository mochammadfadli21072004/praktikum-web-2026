<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /** Semua orang boleh melihat daftar & detail produk. */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Product $product): bool
    {
        return true;
    }

    /** Hanya admin yang boleh mengubah produk. */
    public function update(User $user, Product $product): bool
    {
        return $user->role === 'admin';
    }

    /** Hanya admin yang boleh menghapus produk. */
    public function delete(User $user, Product $product): bool
    {
        return $user->role === 'admin';
    }
}
