<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('category')->get();

        // Cara 1: compact()
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with('category')->findOrFail($id);

        // Cara 2: ->with()
        return view('products.show')->with('product', $product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::findOrFail($id);
        $this->authorize('update', $product);   // 403 jika bukan admin

        $categories = Category::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);
        $this->authorize('update', $product);

        $data = $request->validate([
            'name'  => ['required', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        $product->update($data);

        return redirect('/products')->with('status', 'Produk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $this->authorize('delete', $product);

        $product->delete();

        return redirect('/products')->with('status', 'Produk dihapus.');
    }

    public function laporan()
{
    return [
        'total_produk' => Product::count(),
        'harga_tertinggi' => Product::max('price'),
        'produk_mahal' => Product::where('price', '>', 50000)->orderBy('price', 'desc')->limit(5)->get(),
        'produk_per_kategori' => DB::table('products')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->select('categories.name as kategori', DB::raw('count(*) as jumlah'))
            ->groupBy('categories.name')
            ->get(),
    ];
}
}

