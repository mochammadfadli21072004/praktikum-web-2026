<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

/**
 * @group Kategori (v1)
 */
class CategoryController extends Controller
{
    /**
     * Daftar kategori beserta jumlah produknya.
     */
    public function index()
    {
        return CategoryResource::collection(
            Category::withCount('products')->get()
        );
    }

    /**
     * Detail kategori beserta produk di dalamnya.
     *
     * @urlParam category integer required ID kategori. Example: 1
     */
    public function show(Category $category): CategoryResource
    {
        return new CategoryResource($category->load('products'));
    }
}
