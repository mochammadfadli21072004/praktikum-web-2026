<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'image', 'category_id', 'stock'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Accessor: dipanggil lewat $product->formatted_price
     * Contoh hasil: Rp 125.000
     */
    protected function formattedPrice(): Attribute
    {
        return Attribute::get(
            fn () => 'Rp ' . number_format((float) $this->price, 0, ',', '.')
        );
    }
}
