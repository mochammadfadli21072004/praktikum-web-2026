<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Produk (v1)
 *
 * CRUD produk. Kirim header "Accept: application/json".
 */
class ProductController extends Controller
{
    /**
     * Daftar produk.
     *
     * @queryParam page int Halaman ke berapa. Example: 1
     * @response 200 {"data": [{"id": 1, "name": "Kopi Susu", "price": 15000, "formatted_price": "Rp 15.000", "stock": 10, "category": {"id": 1, "name": "Minuman"}}], "links": {}, "meta": {}}
     */
    public function index()
    {
        return ProductResource::collection(
            Product::with('category')->latest()->paginate(10)
        );
    }

    /**
     * Buat produk baru.
     *
     * @bodyParam name string required Nama produk (maks 100). Example: Kopi Susu
     * @bodyParam description string Deskripsi produk. Example: Kopi susu gula aren
     * @bodyParam price number required Harga (>= 0). Example: 15000
     * @bodyParam stock integer Stok (>= 0). Example: 10
     * @bodyParam category_id integer required ID kategori yang ada. Example: 1
     * @response 201 {"data": {"id": 16, "name": "Kopi Susu", "price": 15000, "stock": 10}}
     * @response 422 {"message": "The name field is required.", "errors": {"name": ["The name field is required."]}}
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        $product = Product::create($data)->load('category');

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);   // 201
    }

    /**
     * Detail satu produk.
     *
     * @urlParam product integer required ID produk. Example: 1
     * @response 404 {"message": "No query results for model [App\\Models\\Product] 999"}
     */
    public function show(Product $product): ProductResource
    {
        return new ProductResource($product->load('category'));
    }

    /**
     * Ubah produk (PUT/PATCH, field boleh sebagian).
     *
     * @urlParam product integer required ID produk. Example: 1
     * @bodyParam name string Nama produk. Example: Kopi Susu Besar
     * @bodyParam price number Harga. Example: 18000
     */
    public function update(Request $request, Product $product): ProductResource
    {
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'description' => ['sometimes', 'nullable', 'string'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'stock' => ['sometimes', 'required', 'integer', 'min:0'],
            'category_id' => ['sometimes', 'required', 'exists:categories,id'],
        ]);

        $product->update($data);

        return new ProductResource($product->load('category'));
    }

    /**
     * Hapus produk.
     *
     * @urlParam product integer required ID produk. Example: 1
     * @response 204 scenario="berhasil" {}
     */
    public function destroy(Product $product): Response
    {
        $product->delete();

        return response()->noContent();   // 204
    }

    /**
     * 5 produk terbaru.
     */
    public function latest()
    {
        return ProductResource::collection(
            Product::with('category')->latest()->take(5)->get()
        );
    }
}
