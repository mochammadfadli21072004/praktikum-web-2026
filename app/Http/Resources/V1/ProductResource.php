<?php

namespace App\Http\Resources\V1;

use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => (float) $this->price,
            'formatted_price' => $this->formatted_price,   // accessor di model Product
            'stock' => $this->stock,
            // relasi hanya disertakan jika sudah di-eager load
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
