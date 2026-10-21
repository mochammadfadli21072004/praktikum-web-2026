<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * V2: perubahan besar pada bentuk response (price jadi objek, kategori diratakan,
 * ada status stok & link). Klien lama tetap aman karena masih memakai V1.
 */
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => [
                'amount' => (float) $this->price,
                'currency' => 'IDR',
                'formatted' => $this->formatted_price,
            ],
            'stock' => [
                'quantity' => $this->stock,
                'status' => match (true) {
                    $this->stock === 0 => 'habis',
                    $this->stock <= 5 => 'menipis',
                    default => 'tersedia',
                },
            ],
            'category' => $this->whenLoaded('category', fn () => $this->category?->name),
            'links' => [
                'self' => url("/api/v2/products/{$this->id}"),
            ],
        ];
    }
}
