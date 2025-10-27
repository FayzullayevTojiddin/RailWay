<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test manager',
            'email' => 'manager@mtu.uz',
            'password' => 1,
            'role' => 'manager'
        ]);

        User::factory()->create([
            'name' => "Test Super",
            'email' => 'super@mtu.uz',
            'password' => 1,
            'role' => 'super'
        ]);

        User::factory()->create([
            'name' => "Test viewer",
            'email' => 'viewer@mtu.uz',
            'password' => 1,
            'role' => 'viewer'
        ]);
    }
}
