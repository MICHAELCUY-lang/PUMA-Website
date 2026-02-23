<?php

use Illuminate\Support\Facades\DB;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$output = "";
echo "--- Table Definition for 'members' ---\n";
$output .= "--- Table Definition for 'members' ---\n";
$sql = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name='members'");
if (!empty($sql)) {
    echo $sql[0]->sql . "\n";
    $output .= $sql[0]->sql . "\n";
}

echo "\n--- Indexes on 'members' ---\n";
$output .= "\n--- Indexes on 'members' ---\n";
$indexes = DB::select("SELECT name, sql FROM sqlite_master WHERE type='index' AND tbl_name='members'");
foreach ($indexes as $idx) {
    echo "Index: {$idx->name}\nSQL: {$idx->sql}\n----------------\n";
    $output .= "Index: {$idx->name}\nSQL: {$idx->sql}\n----------------\n";
}

file_put_contents('schema_dump.txt', $output);
