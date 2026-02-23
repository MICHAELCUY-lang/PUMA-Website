<?php

use App\Models\User;
use App\Models\Member;
use App\Models\Cabinet;
use App\Models\Division;
use Illuminate\Support\Facades\Hash;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Counts:\n";
echo "Cabinets: " . Cabinet::count() . "\n";
echo "Divisions: " . Division::count() . "\n";
echo "Users: " . User::count() . "\n";
echo "Members: " . Member::count() . "\n";

// Check Admin
$adminEmail = 'admin@puma.com';
$admin = User::where('email', $adminEmail)->first();

if (!$admin) {
    echo "Admin user missing. Creating...\n";
    User::create([
        'name' => 'Admin PUMA',
        'email' => $adminEmail,
        'password' => Hash::make('password'),
        'role' => 'admin',
    ]);
    echo "Admin created.\n";
} else {
    echo "Admin exists.\n";
    // Ensure role is admin
    if ($admin->role !== 'admin') {
        $admin->role = 'admin';
        $admin->save();
        echo "Admin role fixed.\n";
    }
}
