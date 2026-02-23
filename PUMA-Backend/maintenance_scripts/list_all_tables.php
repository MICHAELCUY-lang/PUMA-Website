<?php

use Illuminate\Support\Facades\DB;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- All Tables in SQLite ---\n";
$tables = DB::select("SELECT name FROM sqlite_master WHERE type='table'");
foreach ($tables as $t) {
    echo "{$t->name}\n";
}
