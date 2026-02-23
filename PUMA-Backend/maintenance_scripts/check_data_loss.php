<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Current Members Count ---\n";
$count = DB::table('members')->count();
echo "Count: $count\n";

echo "\n--- Listing All Tables ---\n";
$tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name LIKE '%member%'");
foreach ($tables as $t) {
    echo "Table: {$t->name}\n";
    $c = DB::table($t->name)->count();
    echo "  Rows: $c\n";
}

echo "\n--- SQLite Sequence (Auto Increment) ---\n";
$seq = DB::select("SELECT * FROM sqlite_sequence WHERE name='members'");
foreach ($seq as $s) {
    echo "Member Sequence: {$s->seq}\n";
}
