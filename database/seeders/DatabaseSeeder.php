<?php

namespace Database\Seeders;

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
        User::factory()->admin()->create([
            'name' => 'Admin Toko',
            'email' => 'admin@example.com',
        ]);

        User::factory()->create([
            'name' => 'Kasir Satu',
            'email' => 'kasir@example.com',
        ]);
    }
}