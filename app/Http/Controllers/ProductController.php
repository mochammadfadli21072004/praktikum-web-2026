<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductImageService;
use Illuminate\Database\Eloquent\Collection;

class ProductController extends Controller
{
    public function __construct(
        private ProductImageService $images
    ) {}

    public function index()
    {
        $products = Product::with('category')->latest()->get();

        return view('products.index', compact('products'));
    }

    public function create()
    {
        $this->authorize('create', Product::class);
        $categories = $this->categoryOptions();

        return view('products.create', compact('categories'));
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $this->images->store($request->file('image'));
        }

        Product::create($data);

        return redirect()->route('products.index')
            ->with('status', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product)
    {
        $product->load('category');

        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);
        $categories = $this->categoryOptions();

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $this->images->replace($product->image, $request->file('image'));
        } else {
            unset($data['image']);
        }

        $product->update($data);

        return redirect()->route('products.index')
            ->with('status', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        $this->images->delete($product->image);
        $product->delete();

        return redirect()->route('products.index')
            ->with('status', 'Produk berhasil dihapus.');
    }

    public function laporan()
    {
        $laporan = Category::query()
            ->select('categories.id', 'categories.name as kategori')
            ->withCount('products as jumlah')
            ->withSum('products as total_stok', 'stock')
            ->orderBy('name')
            ->get();

        return view('products.laporan', compact('laporan'));
    }

    /**
     * Extract Method: pilihan kategori terurut dipakai bersama oleh create() & edit().
     */
    private function categoryOptions(): Collection
    {
        return Category::orderBy('name')->get();
    }
}
