<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pertemuan 11 - Materi 2: Mass assignment.
 * Dua lapis pertahanan: $fillable di Model + validated() di Form Request.
 */
class MassAssignmentTest extends TestCase
{
    use RefreshDatabase;

    // ---------- Lapis 1: $fillable pada Model ----------

    public function test_model_produk_mengabaikan_kolom_di_luar_fillable(): void
    {
        $product = Product::create([
            'name' => 'Teh Botol',
            'price' => 5000,
            'category_id' => Category::factory()->create()->id,
            'id' => 999,
            'created_at' => '2000-01-01 00:00:00',
        ]);

        $this->assertNotSame(999, $product->id);
        $this->assertNotSame(2000, $product->created_at->year);
    }

    public function test_role_user_tidak_bisa_diisi_secara_massal(): void
    {
        $user = User::create([
            'name' => 'Penyusup',
            'email' => 'penyusup@example.com',
            'password' => 'rahasia123',
            'role' => User::ROLE_ADMIN,
        ]);

        $this->assertFalse($user->fresh()->isAdmin());
        $this->assertSame(User::ROLE_KASIR, $user->fresh()->role);
    }

    // ---------- Lapis 2: validated() pada Form Request ----------

    public function test_admin_tidak_bisa_membuat_akun_admin_lewat_form_kasir(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/users', [
            'name' => 'Kasir Palsu',
            'email' => 'palsu@example.com',
            'password' => 'rahasia123',
            'role' => User::ROLE_ADMIN,
        ])->assertRedirect(route('users.index'));

        $this->assertSame(User::ROLE_KASIR, User::where('email', 'palsu@example.com')->value('role'));
    }

    public function test_field_tambahan_pada_form_produk_diabaikan(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $this->actingAs($admin)->post('/products', [
            'name' => 'Teh Botol',
            'price' => 5000,
            'stock' => 20,
            'category_id' => $category->id,
            'id' => 777,
            'is_admin' => 1,
        ])->assertRedirect(route('products.index'));

        $this->assertDatabaseMissing('products', ['id' => 777]);
        $this->assertDatabaseHas('products', ['name' => 'Teh Botol']);
    }

    public function test_field_tambahan_pada_api_produk_diabaikan(): void
    {
        $category = Category::factory()->create();

        $this->postJson('/api/v1/products', [
            'name' => 'Kopi Susu',
            'price' => 15000,
            'category_id' => $category->id,
            'id' => 555,
        ])->assertCreated();

        $this->assertDatabaseMissing('products', ['id' => 555]);
    }
}
