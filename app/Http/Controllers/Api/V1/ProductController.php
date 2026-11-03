<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProductRequest;
use App\Http\Resources\V1\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
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
     *
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
     *
     * @response 201 {"data": {"id": 16, "name": "Kopi Susu", "price": 15000, "stock": 10}}
     * @response 422 {"message": "The name field is required.", "errors": {"name": ["The name field is required."]}}
     */
    public function store(ProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated())->load('category');

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);   // 201
    }

    /**
     * Detail satu produk.
     *
     * @urlParam product integer required ID produk. Example: 1
     *
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
     *
     * @bodyParam name string Nama produk. Example: Kopi Susu Besar
     * @bodyParam price number Harga. Example: 18000
     */
    public function update(ProductRequest $request, Product $product): ProductResource
    {
        $product->update($request->validated());

        return new ProductResource($product->load('category'));
    }

    /**
     * Hapus produk.
     *
     * @urlParam product integer required ID produk. Example: 1
     *
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
