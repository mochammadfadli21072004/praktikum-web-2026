<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
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

