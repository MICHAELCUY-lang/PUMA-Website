<?php

use Illuminate\Support\Facades\DB;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Checking event_images Foreign Keys ---\n";
$fks = DB::select("PRAGMA foreign_key_list(event_images)");
foreach ($fks as $fk) {
    echo "FK: {$fk->from} -> {$fk->table}({$fk->to}) ON UPDATE {$fk->on_update} ON DELETE {$fk->on_delete}\n";
}

echo "\n--- Checking events Table Info ---\n";
$tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name LIKE 'event%'");
foreach ($tables as $t) {
    echo "Table found: {$t->name}\n";
}
