<?php

use Illuminate\Support\Facades\DB;
use App\Models\Member;
use App\Models\Cabinet;
use App\Models\Division;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking Members Table Schema...\n";
$info = DB::select("PRAGMA table_info(members)");
foreach ($info as $col) {
    echo "Column: {$col->name}, Type: {$col->type}, NotNull: {$col->notnull}, Default: {$col->dflt_value}\n";
}

echo "\nChecking Foreign Keys...\n";
$fks = DB::select("PRAGMA foreign_key_list(members)");
foreach ($fks as $fk) {
    echo "FK: {$fk->from} -> {$fk->table}({$fk->to}) ON UPDATE {$fk->on_update} ON DELETE {$fk->on_delete}\n";
}

echo "\nAttempting to create member with null user_id...\n";
try {
    // Ensure we have a cabinet and division
    $cabinet = Cabinet::first();
    $division = Division::first();
    
    if (!$cabinet || !$division) {
        echo "WARNING: No cabinet or division found to test with.\n";
    }

    $member = new Member();
    $member->name = "Test Member Schema";
    $member->email = "test.schema@example.com";
    $member->cabinet_id = $cabinet ? $cabinet->id : null;
    $member->division_id = $division ? $division->id : null;
    $member->position = "Tester";
    $member->user_id = null; // Testing nullability
    $member->save();
    
    echo "SUCCESS: Created member with ID {$member->id}\n";
    // Cleanup
    $member->delete();
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
