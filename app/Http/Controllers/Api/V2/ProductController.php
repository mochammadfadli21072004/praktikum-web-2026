<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

/**
 * @group Produk (v2)
 *
 * Bentuk response baru + fitur filter/sort. Versi v1 tetap tersedia.
 */
class ProductController extends Controller
{
    /**
     * Daftar produk (dengan filter).
     *
     * @queryParam search string Cari berdasarkan nama. Example: kopi
     * @queryParam category_id integer Filter kategori. Example: 1
     * @queryParam sort string price_asc atau price_desc. Example: price_desc
     * @queryParam page int Halaman. Example: 1
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        match ($request->sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            default => $query->latest(),
        };

        return ProductResource::collection($query->paginate(10)->withQueryString());
    }

    /**
     * Detail satu produk.
     *
     * @urlParam product integer required ID produk. Example: 1
     */
    public function show(Product $product): ProductResource
    {
        return new ProductResource($product->load('category'));
    }
}
