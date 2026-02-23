<?php

use Illuminate\Support\Facades\DB;
use App\Models\Event;
use App\Models\EventImage;
use App\Models\Member;
use App\Models\Cabinet;
use App\Models\Division;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Checking Event Images Table Schema ---\n";
$fks = DB::select("PRAGMA foreign_key_list(event_images)");
foreach ($fks as $fk) {
    echo "FK: {$fk->from} -> {$fk->table}({$fk->to}) ON UPDATE {$fk->on_update} ON DELETE {$fk->on_delete}\n";
}

echo "\n--- Testing Event Deletion ---\n";
try {
    $event = Event::create([
        'title' => 'Delete Test Event',
        'description' => 'To be deleted',
        'event_date' => now(),
        'status' => 'planned'
    ]);
    echo "Created Event ID: {$event->id}\n";
    
    $event->images()->create(['image_url' => 'http://test.com/img.jpg']);
    echo "Added Image to Event\n";
    
    echo "Attempting delete...\n";
    $event->delete(); // This triggers Controller logic manually if in code, but here we call model delete. 
    // Controller calls $event->images()->delete() first. Let's simulate that if needed, 
    // but standard model delete should work if Cascade DB or no relationship constraints blocking.
    // If Controller does manual delete, we should check if Model delete works alone (DB cascade) or if manual is needed.
    
    // In Controller:
    // $event->images()->delete();
    // $event->delete();
    
    // Let's try pure model delete first to see if DB complains
    if (Event::find($event->id)) {
        echo "Event still exists! Model delete failed (soft delete? no, not using SoftDeletes)\n";
    } else {
        echo "SUCCESS: Event deleted.\n";
    }
    
} catch (\Exception $e) {
    echo "ERROR Deleting Event: " . $e->getMessage() . "\n";
}

echo "\n--- Testing Member Creation (Backend Validation) ---\n";
try {
    // Valid data (simulating fixed frontend)
    $member = Member::create([
        'name' => 'Test Member Fix',
        'email' => 'fix@test.com',
        'position' => 'Dev',
        'batch' => '2024',
        'cabinet_id' => Cabinet::first()?->id,
        'division_id' => Division::first()?->id,
        'user_id' => null 
    ]);
    echo "SUCCESS: Created member with ID {$member->id}\n";
    $member->delete();
} catch (\Exception $e) {
    echo "ERROR Creating Member: " . $e->getMessage() . "\n";
}
