<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            // hanya muncul jika dipanggil withCount('products')
            'products_count' => $this->whenCounted('products'),
            // hanya muncul jika sudah di-eager load with('products')
            'products' => V1\ProductResource::collection($this->whenLoaded('products')),
        ];
    }
}
