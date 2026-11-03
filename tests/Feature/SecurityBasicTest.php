<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Pertemuan 11 - Materi 1: Security dasar Laravel
 * (CSRF, XSS/escaping Blade, hashing password, SQL injection, validasi parameter route).
 */
class SecurityBasicTest extends TestCase
{
    use RefreshDatabase;

    // ---------- CSRF ----------

    public function test_form_login_menyertakan_token_csrf(): void
    {
        $this->get('/login')->assertOk()->assertSee('name="_token"', false);
    }

    public function test_form_admin_menyertakan_token_csrf(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/products/create')->assertSee('name="_token"', false);
        $this->actingAs($admin)->get('/users')->assertSee('name="_token"', false);
    }

    public function test_logout_tidak_bisa_dipanggil_lewat_get(): void
    {
        $this->actingAs(User::factory()->create())->get('/logout')->assertStatus(405);
    }

    // ---------- XSS ----------

    public function test_nama_produk_di_escape_oleh_blade(): void
    {
        Product::factory()->create(['name' => "<script>alert('xss')</script>"]);

        $this->get('/products')
            ->assertOk()
            ->assertDontSee("<script>alert('xss')</script>", false)
            ->assertSee('&lt;script&gt;', false);
    }

    public function test_input_foto_di_escape_sebelum_dikirim_balik(): void
    {
        $this->post('/photos', ['judul' => '<script>alert(1)</script>'])
            ->assertOk()
            ->assertDontSee('<script>', false)
            ->assertSee('&lt;script&gt;', false);
    }

    public function test_parameter_foto_yang_bukan_angka_ditolak_404(): void
    {
        $this->get('/photos/<script>alert(1)</script>')->assertNotFound();
        $this->get('/photos/abc')->assertNotFound();
        $this->get('/photos/5')->assertOk()->assertSee('5');
    }

    // ---------- Hashing password ----------

    public function test_password_kasir_baru_disimpan_dalam_bentuk_hash(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/users', [
            'name' => 'Kasir Baru',
            'email' => 'baru@example.com',
            'password' => 'rahasia123',
        ])->assertRedirect(route('users.index'));

        $tersimpan = User::where('email', 'baru@example.com')->firstOrFail()->password;

        $this->assertNotSame('rahasia123', $tersimpan);
        $this->assertTrue(Hash::check('rahasia123', $tersimpan));
    }

    // ---------- SQL Injection ----------

    public function test_login_dengan_payload_sql_injection_ditolak(): void
    {
        User::factory()->create(['email' => 'kasir@example.com']);

        $this->post('/login', ['email' => "' OR '1'='1", 'password' => "' OR '1'='1"])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_query_eloquent_memakai_parameter_binding(): void
    {
        User::factory()->create(['email' => 'kasir@example.com']);
        Product::factory()->count(3)->create();

        // Jika input mentah masuk ke SQL, kondisi OR 1=1 akan mengembalikan semua baris.
        $this->assertNull(User::where('email', "x@example.com' OR '1'='1")->first());
        $this->assertSame(0, Product::where('name', "' OR 1=1 --")->count());
    }
}
