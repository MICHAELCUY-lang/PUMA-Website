<?php

use Illuminate\Support\Facades\DB;
use App\Models\Member;
use App\Models\Cabinet;
use App\Models\Division;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Unique Constraint with NULL user_id...\n";

try {
    $cabinet = Cabinet::first();
    $division = Division::first();
    
    if (!$cabinet || !$division) {
        die("Need cabinet and division data.\n");
    }
    
    echo "Cabinet ID: {$cabinet->id}, Division ID: {$division->id}\n";
    
    // Create First Member
    $m1 = Member::create([
        'name' => 'Member One',
        'email' => 'one@test.com',
        'position' => 'Pos 1',
        'batch' => '2024',
        'cabinet_id' => $cabinet->id,
        'division_id' => $division->id,
        'user_id' => null
    ]);
    echo "Created Member 1 ID: {$m1->id}\n";
    
    // Create Second Member (Same Cab/Div, NULL User)
    $m2 = Member::create([
        'name' => 'Member Two',
        'email' => 'two@test.com',
        'position' => 'Pos 2',
        'batch' => '2024',
        'cabinet_id' => $cabinet->id,
        'division_id' => $division->id,
        'user_id' => null
    ]);
    echo "Created Member 2 ID: {$m2->id}\n";
    
    // Cleanup
    $m1->delete();
    $m2->delete();
    echo "SUCCESS: Multiple NULL user_id allowed.\n";
    
} catch (\Exception $e) {
    file_put_contents('error_log.txt', $e->getMessage());
    echo "ERROR: " . $e->getMessage() . "\n";
    if (isset($m1)) $m1->delete();
}
