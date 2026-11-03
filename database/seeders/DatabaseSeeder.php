<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ProductSeeder::class,
        ]);

        // BARU: dua akun, password keduanya "password"
        $admin = User::factory()->admin()->create([
            'name' => 'Admin Toko',
            'email' => 'admin@example.com',
        ]);

        $kasir = User::factory()->create([
            'name' => 'Kasir Satu',
            'email' => 'kasir@example.com',
        ]);

        // Data contoh Mini Project Task Manager
        Task::factory()->count(3)->for($admin)->create();
        Task::factory()->count(2)->for($kasir)->create();
        Task::factory()->completed()->for($kasir)->create();
    }
}
