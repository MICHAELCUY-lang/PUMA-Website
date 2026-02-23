<?php

use Illuminate\Support\Facades\DB;
use App\Models\Event;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking Events Table Schema...\n";
$info = DB::select("PRAGMA table_info(events)");
foreach ($info as $col) {
    if ($col->name === 'status') {
        echo "Status Column: Type={$col->type}, Dflt={$col->dflt_value}, Pk={$col->pk}\n";
    }
}

echo "\nAttempting to insert 'planned' event...\n";
try {
    $event = new Event();
    $event->title = "Test Planned";
    $event->description = "Test Description";
    $event->event_date = now();
    $event->status = "planned"; // This is the problematic value
    $event->save();
    echo "SUCCESS: Inserted event with ID {$event->id}\n";
    // Cleanup
    $event->delete();
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
