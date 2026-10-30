<?php

namespace Tests\Unit;

use App\Models\Product;
use PHPUnit\Framework\TestCase;

class ProductFormattedPriceTest extends TestCase
{
    public function test_accessor_formatted_price(): void
    {
        $product = new Product(['price' => 125000]);

        $this->assertEquals('Rp 125.000', $product->formatted_price);
    }
}
