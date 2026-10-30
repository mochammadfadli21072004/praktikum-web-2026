<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductWebTest extends TestCase
{
    use RefreshDatabase;

    private function dataProdukValid(Category $category, array $override = []): array
    {
        return array_merge([
            'name' => 'Teh Botol',
            'price' => 5000,
            'stock' => 20,
            'category_id' => $category->id,
        ], $override);
    }

    // ---------- Membaca (GET) ----------

    public function test_tamu_bisa_melihat_daftar_produk(): void
    {
        $category = Category::factory()->create(['name' => 'Minuman']);
        Product::factory()->create(['name' => 'Kopi Susu', 'price' => 15000, 'category_id' => $category->id]);

        $this->get('/products')
            ->assertOk()
            ->assertSee('Kopi Susu')
            ->assertSee('Rp 15.000')
            ->assertSee('Minuman');
    }

    public function test_tamu_bisa_melihat_detail_produk(): void
    {
        $product = Product::factory()->create(['name' => 'Roti Tawar']);

        $this->get("/products/{$product->id}")->assertOk()->assertSee('Roti Tawar');
    }

    public function test_produk_yang_tidak_ada_mengembalikan_404(): void
    {
        $this->get('/products/999')->assertNotFound();
    }

    public function test_halaman_laporan_menampilkan_ringkasan_per_kategori(): void
    {
        $category = Category::factory()->create(['name' => 'Snack']);
        Product::factory()->count(2)->create(['category_id' => $category->id, 'stock' => 10]);

        $this->get('/products/laporan')->assertOk()->assertSee('Snack')->assertSee('20');
    }

    // ---------- Hak akses ----------

    public function test_tamu_diarahkan_ke_login_saat_membuka_form_tambah(): void
    {
        $this->get('/products/create')->assertRedirect('/login');
    }

    public function test_kasir_tidak_boleh_membuka_form_tambah(): void
    {
        $kasir = User::factory()->create();

        $this->actingAs($kasir)->get('/products/create')->assertForbidden();
    }

    public function test_admin_bisa_membuka_form_tambah(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/products/create')->assertOk()->assertSee('Tambah Produk');
    }

    // ---------- Membuat (POST) ----------

    public function test_admin_bisa_menambah_produk(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $this->actingAs($admin)
            ->post('/products', $this->dataProdukValid($category))
            ->assertRedirect(route('products.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('products', ['name' => 'Teh Botol', 'stock' => 20]);
    }

    public function test_admin_bisa_menambah_produk_dengan_gambar(): void
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped('Ekstensi GD dibutuhkan untuk UploadedFile::fake()->image().');
        }

        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $this->actingAs($admin)->post('/products', $this->dataProdukValid($category, [
            'image' => UploadedFile::fake()->image('teh.png'),
        ]))->assertRedirect(route('products.index'));

        Storage::disk('public')->assertExists(Product::first()->image);
    }

    public function test_data_tidak_valid_ditolak_dengan_pesan_error(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post('/products', [])
            ->assertSessionHasErrors(['name', 'price', 'stock', 'category_id']);

        $this->assertDatabaseCount('products', 0);
    }

    public function test_kasir_tidak_boleh_menambah_produk(): void
    {
        $kasir = User::factory()->create();
        $category = Category::factory()->create();

        $this->actingAs($kasir)
            ->post('/products', $this->dataProdukValid($category))
            ->assertForbidden();

        $this->assertDatabaseCount('products', 0);
    }

    // ---------- Mengubah (PUT) & Menghapus (DELETE) ----------

    public function test_admin_bisa_mengubah_produk(): void
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['name' => 'Nama Lama']);

        $this->actingAs($admin)
            ->put("/products/{$product->id}", $this->dataProdukValid($product->category, ['name' => 'Nama Baru']))
            ->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Nama Baru']);
        $this->assertDatabaseMissing('products', ['name' => 'Nama Lama']);
    }

    public function test_admin_bisa_menghapus_produk_beserta_gambarnya(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/lama.png', 'isi');
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['image' => 'products/lama.png']);

        $this->actingAs($admin)->delete("/products/{$product->id}")->assertRedirect(route('products.index'));

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        Storage::disk('public')->assertMissing('products/lama.png');
    }

    public function test_kasir_tidak_boleh_menghapus_produk(): void
    {
        $kasir = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($kasir)->delete("/products/{$product->id}")->assertForbidden();

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }
}
