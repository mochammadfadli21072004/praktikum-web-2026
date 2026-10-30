<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_v1_index_mengembalikan_daftar_produk(): void
    {
        Product::factory(3)->create();

        $this->getJson('/api/v1/products')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['id', 'name', 'price', 'formatted_price', 'stock', 'category']]]);
    }

    public function test_v1_store_valid_mengembalikan_201(): void
    {
        $category = Category::factory()->create();

        $this->postJson('/api/v1/products', [
            'name' => 'Kopi Susu',
            'price' => 15000,
            'stock' => 10,
            'category_id' => $category->id,
        ])->assertCreated()->assertJsonPath('data.name', 'Kopi Susu');

        $this->assertDatabaseHas('products', ['name' => 'Kopi Susu']);
    }

    public function test_v1_store_tidak_valid_mengembalikan_422(): void
    {
        $this->postJson('/api/v1/products', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'price', 'category_id']);
    }

    public function test_v1_store_stock_null_ditolak_422_bukan_500(): void
    {
        $category = Category::factory()->create();

        $this->postJson('/api/v1/products', [
            'name' => 'Teh Manis',
            'price' => 5000,
            'stock' => null,
            'category_id' => $category->id,
        ])->assertStatus(422)->assertJsonValidationErrors(['stock']);
    }

    public function test_v1_show_tidak_ada_mengembalikan_404_json(): void
    {
        $this->getJson('/api/v1/products/999')->assertNotFound();
    }

    public function test_v1_update_dan_destroy(): void
    {
        $product = Product::factory()->create();

        $this->putJson("/api/v1/products/{$product->id}", ['name' => 'Nama Baru'])
            ->assertOk()->assertJsonPath('data.name', 'Nama Baru');

        $this->deleteJson("/api/v1/products/{$product->id}")->assertNoContent();
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_rute_latest_tidak_bentrok_dengan_show(): void
    {
        Product::factory(7)->create();

        $this->getJson('/api/v1/products/latest')->assertOk()->assertJsonCount(5, 'data');
    }

    public function test_v2_punya_bentuk_response_berbeda(): void
    {
        $product = Product::factory()->create(['stock' => 0]);

        $this->getJson("/api/v2/products/{$product->id}")
            ->assertOk()
            ->assertJsonPath('data.price.currency', 'IDR')
            ->assertJsonPath('data.stock.status', 'habis');
    }
}
