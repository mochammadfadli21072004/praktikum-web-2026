<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAndRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_login_tampil_untuk_tamu(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_login_berhasil_diarahkan_ke_dashboard(): void
    {
        $user = User::factory()->create(['email' => 'kasir@example.com']);

        $this->post('/login', ['email' => 'kasir@example.com', 'password' => 'password'])
            ->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_dengan_password_salah_ditolak(): void
    {
        User::factory()->create(['email' => 'kasir@example.com']);

        $this->post('/login', ['email' => 'kasir@example.com', 'password' => 'salah'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_dashboard_butuh_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_user_yang_login_bisa_membuka_dashboard(): void
    {
        $this->actingAs(User::factory()->create())->get('/dashboard')->assertOk()->assertSee('Dashboard');
    }

    public function test_kasir_ditolak_di_halaman_khusus_admin(): void
    {
        $this->actingAs(User::factory()->create())->get('/users')->assertForbidden();
    }

    public function test_admin_bisa_membuka_halaman_kelola_kasir(): void
    {
        $this->actingAs(User::factory()->admin()->create())->get('/users')->assertOk();
    }

    public function test_logout_mengakhiri_sesi(): void
    {
        $this->actingAs(User::factory()->create())->post('/logout')->assertRedirect('/login');

        $this->assertGuest();
    }
}
