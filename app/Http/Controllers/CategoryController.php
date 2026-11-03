<?php

namespace App\Http\Controllers;

use App\Models\Category;

/**
 * Refactor P11: closure /categories dipindah ke controller (khusus admin, lihat routes/web.php).
 */
class CategoryController extends Controller
{
    public function index()
    {
        return Category::withCount('products')->get();
    }
}
