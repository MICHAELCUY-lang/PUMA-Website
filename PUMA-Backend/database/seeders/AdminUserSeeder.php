<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::updateOrCreate(
            ['email' => 'admin@puma.com'],
            [
                'name' => 'Admin PUMA',
                'email' => 'admin@puma.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        echo "Admin user created successfully!\n";
        echo "Email: admin@puma.com\n";
        echo "Password: admin123\n";
    }
}
