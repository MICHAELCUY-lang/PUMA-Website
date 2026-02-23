<?php

use Illuminate\Support\Facades\DB;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Checking Indexes on members table ---\n";
$indexes = DB::select("PRAGMA index_list(members)");
foreach ($indexes as $idx) {
    echo "Index: {$idx->name}, Unique: {$idx->unique}\n";
    $cols = DB::select("PRAGMA index_info({$idx->name})");
    foreach ($cols as $col) {
        echo "  - Column: {$col->name}\n";
    }
}
