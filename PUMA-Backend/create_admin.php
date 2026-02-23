<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = 'admin@puma.com';
$password = 'password';

$user = User::where('email', $email)->first();

if (!$user) {
    try {
        User::create([
            'name' => 'Admin PUMA',
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin', // Assuming you have a role column, if not remove or adjust
        ]);
        echo "Admin user created successfully.\n";
        echo "Email: $email\n";
        echo "Password: $password\n";
    } catch (\Exception $e) {
        echo "Error creating user: " . $e->getMessage() . "\n";
    }
} else {
    echo "Admin user already exists.\n";
}
