<?php

namespace Tests\Feature;

use App\Enums\TaskPriority;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Mini Project Tahap 1 - Task Manager: fitur inti (Must) + keamanan kepemilikan data.
 */
class TaskTest extends TestCase
{
    use RefreshDatabase;

    private function dataTugas(array $override = []): array
    {
        return array_merge([
            'title' => 'Mengerjakan laporan praktikum',
            'description' => 'Bab security',
            'priority' => 'high',
        ], $override);
    }

    // ---------- Akses (harus login) ----------

    public function test_tamu_diarahkan_ke_login_untuk_semua_halaman_tugas(): void
    {
        $task = Task::factory()->create();

        $this->get('/tasks')->assertRedirect('/login');
        $this->get('/tasks/create')->assertRedirect('/login');
        $this->post('/tasks', $this->dataTugas())->assertRedirect('/login');
        $this->put("/tasks/{$task->id}", $this->dataTugas())->assertRedirect('/login');
        $this->delete("/tasks/{$task->id}")->assertRedirect('/login');
        $this->patch("/tasks/{$task->id}/complete")->assertRedirect('/login');
    }

    // ---------- Lihat daftar tugas milik sendiri ----------

    public function test_user_hanya_melihat_tugas_miliknya(): void
    {
        $saya = User::factory()->create();
        Task::factory()->for($saya)->create(['title' => 'Tugas Saya Sendiri']);
        Task::factory()->create(['title' => 'Tugas Orang Lain']);

        $this->actingAs($saya)->get('/tasks')
            ->assertOk()
            ->assertSee('Tugas Saya Sendiri')
            ->assertDontSee('Tugas Orang Lain');
    }

    public function test_daftar_kosong_menampilkan_pesan(): void
    {
        $this->actingAs(User::factory()->create())->get('/tasks')->assertOk()->assertSee('Belum ada tugas.');
    }

    // ---------- Tambah tugas ----------

    public function test_user_bisa_menambah_tugas_dan_menjadi_pemiliknya(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/tasks', $this->dataTugas())
            ->assertRedirect(route('tasks.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('tasks', [
            'title' => 'Mengerjakan laporan praktikum',
            'priority' => 'high',
            'is_completed' => false,
            'user_id' => $user->id,
        ]);
    }

    public function test_data_tidak_valid_ditolak(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/tasks', ['priority' => 'urgent'])
            ->assertSessionHasErrors(['title', 'priority']);

        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_user_id_dari_input_diabaikan_mass_assignment(): void
    {
        $saya = User::factory()->create();
        $korban = User::factory()->create();

        $this->actingAs($saya)->post('/tasks', $this->dataTugas([
            'user_id' => $korban->id,
            'is_completed' => 1,
        ]));

        $task = Task::firstOrFail();
        $this->assertSame($saya->id, $task->user_id);
        $this->assertFalse($task->is_completed);
    }

    // ---------- Edit & hapus ----------

    public function test_user_bisa_mengubah_tugas_sendiri(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create(['title' => 'Judul Lama']);

        $this->actingAs($user)->put("/tasks/{$task->id}", $this->dataTugas(['title' => 'Judul Baru']))
            ->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'Judul Baru']);
    }

    public function test_user_tidak_bisa_mengubah_atau_membuka_edit_tugas_orang_lain(): void
    {
        $task = Task::factory()->create(['title' => 'Milik Orang Lain']);
        $penyusup = User::factory()->create();

        $this->actingAs($penyusup)->get("/tasks/{$task->id}/edit")->assertForbidden();
        $this->actingAs($penyusup)->put("/tasks/{$task->id}", $this->dataTugas())->assertForbidden();

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'Milik Orang Lain']);
    }

    public function test_user_bisa_menghapus_tugas_sendiri(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create();

        $this->actingAs($user)->delete("/tasks/{$task->id}")->assertRedirect(route('tasks.index'));

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_user_tidak_bisa_menghapus_tugas_orang_lain(): void
    {
        $task = Task::factory()->create();

        $this->actingAs(User::factory()->create())->delete("/tasks/{$task->id}")->assertForbidden();

        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    // ---------- Tandai selesai ----------

    public function test_user_bisa_menandai_selesai_dan_membatalkannya(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create();

        $this->actingAs($user)->patch("/tasks/{$task->id}/complete")->assertRedirect(route('tasks.index'));
        $this->assertTrue($task->fresh()->is_completed);

        $this->actingAs($user)->patch("/tasks/{$task->id}/complete");
        $this->assertFalse($task->fresh()->is_completed);
    }

    public function test_user_tidak_bisa_menandai_selesai_tugas_orang_lain(): void
    {
        $task = Task::factory()->create();

        $this->actingAs(User::factory()->create())->patch("/tasks/{$task->id}/complete")->assertForbidden();

        $this->assertFalse($task->fresh()->is_completed);
    }

    // ---------- Struktur data ----------

    public function test_tugas_ikut_terhapus_saat_akun_pemilik_dihapus(): void
    {
        $user = User::factory()->create();
        Task::factory()->for($user)->count(2)->create();

        $user->delete();

        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_prioritas_default_adalah_medium(): void
    {
        $user = User::factory()->create();

        // Insert tanpa kolom priority -> nilai default dari migration yang dipakai
        DB::table('tasks')->insert([
            'title' => 'Tanpa prioritas eksplisit',
            'user_id' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertSame(TaskPriority::Medium, Task::firstOrFail()->priority);
    }

    // ---------- Form ----------

    public function test_form_tambah_dan_edit_tampil_untuk_pemilik(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create(['title' => 'Judul Edit', 'priority' => 'low']);

        $this->actingAs($user)->get('/tasks/create')->assertOk()->assertSee('Tambah Tugas');
        $this->actingAs($user)->get("/tasks/{$task->id}/edit")
            ->assertOk()
            ->assertSee('Judul Edit')
            ->assertSee('value="low" selected', false);
    }
}
