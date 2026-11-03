<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Pertemuan 11 - Materi 3: Validasi dan Error Exposure.
 */
class ValidationAndErrorExposureTest extends TestCase
{
    use RefreshDatabase;

    private function dataProduk(array $override = []): array
    {
        return array_merge([
            'name' => 'Teh Botol',
            'price' => 5000,
            'stock' => 20,
            'category_id' => Category::factory()->create()->id,
        ], $override);
    }

    // ---------- Validasi input ----------

    public function test_harga_melebihi_batas_kolom_ditolak(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/products', $this->dataProduk(['price' => 100000000]))
            ->assertSessionHasErrors('price');
    }

    public function test_stok_negatif_dan_deskripsi_terlalu_panjang_ditolak(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/products', $this->dataProduk([
            'stock' => -1,
            'description' => str_repeat('a', 2001),
        ]))->assertSessionHasErrors(['stock', 'description']);
    }

    public function test_file_bukan_gambar_ditolak(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/products', $this->dataProduk([
            'image' => UploadedFile::fake()->create('skrip.php', 10, 'application/x-php'),
        ]))->assertSessionHasErrors('image');
    }

    public function test_api_menolak_harga_melebihi_batas_dengan_422(): void
    {
        $this->postJson('/api/v1/products', $this->dataProduk(['price' => 100000000]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('price');
    }

    public function test_akun_kasir_email_duplikat_dan_password_pendek_ditolak(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['email' => 'sudah@ada.test']);

        $this->actingAs($admin)->post('/users', [
            'name' => 'Kasir',
            'email' => 'sudah@ada.test',
            'password' => '123',
        ])->assertSessionHasErrors(['email', 'password']);
    }

    // ---------- Error exposure ----------

    public function test_production_tidak_membocorkan_detail_error(): void
    {
        config(['app.debug' => false]);
        Route::get('/_uji-error', fn () => throw new \RuntimeException('rahasia-internal-xyz'));

        $this->get('/_uji-error')
            ->assertStatus(500)
            ->assertDontSee('rahasia-internal-xyz')
            ->assertDontSee('RuntimeException')
            ->assertDontSee(base_path());
    }

    public function test_mode_debug_membocorkan_detail_error_sebagai_pembanding(): void
    {
        config(['app.debug' => true]);
        Route::get('/_uji-error', fn () => throw new \RuntimeException('rahasia-internal-xyz'));

        $this->get('/_uji-error')->assertStatus(500)->assertSee('rahasia-internal-xyz');
    }

    public function test_contoh_env_production_mematikan_debug(): void
    {
        $isi = file_get_contents(base_path('.env.production.example'));

        $this->assertStringContainsString('APP_ENV=production', $isi);
        $this->assertStringContainsString('APP_DEBUG=false', $isi);
    }

    // ---------- Refactor closure -> controller tetap berperilaku sama ----------

    public function test_halaman_kategori_hanya_untuk_admin(): void
    {
        Category::factory()->create(['name' => 'Minuman']);

        $this->actingAs(User::factory()->create())->get('/categories')->assertForbidden();
        $this->actingAs(User::factory()->admin()->create())->get('/categories')
            ->assertOk()->assertJsonFragment(['name' => 'Minuman']);
    }
}
