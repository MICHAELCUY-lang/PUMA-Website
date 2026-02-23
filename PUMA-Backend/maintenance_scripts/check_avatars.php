<?php
// Check member avatar paths

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Member;
use App\Models\Cabinet;

$output = "";

// Get Kaustav cabinet ID
$kaustav = Cabinet::where('name', 'Kaustav')->first();
$output .= "Kaustav Cabinet ID: " . ($kaustav ? $kaustav->id : 'NOT FOUND') . "\n\n";

// Get members from Kaustav
$members = Member::where('cabinet_id', $kaustav->id ?? 1)
    ->with(['user'])
    ->limit(10)
    ->get();

$output .= "=== KAUSTAV MEMBERS AVATAR DATA ===\n";
foreach ($members as $m) {
    $output .= "ID: {$m->id}\n";
    $output .= "  Name (member): " . ($m->name ?? 'NULL') . "\n";
    $output .= "  Name (user): " . ($m->user->name ?? 'NO USER') . "\n";
    $output .= "  Photo Path: " . ($m->photo_path ?? 'NULL') . "\n";
    $output .= "  User Avatar: " . ($m->user->avatar ?? 'NULL') . "\n";
    $output .= "---\n";
}

file_put_contents(__DIR__ . '/avatar_debug.txt', $output);
echo "Output saved to avatar_debug.txt\n";
