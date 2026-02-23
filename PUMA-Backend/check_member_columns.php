<?php

use Illuminate\Support\Facades\Schema;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$columns = Schema::getColumnListing('members');
// echo "Columns in 'members' table:\n";
// print_r($columns);

$missing = [];
$required = ['name', 'email', 'cabinet_id', 'division_id', 'position', 'batch', 'photo_path'];
foreach ($required as $field) {
    if (!in_array($field, $columns)) {
        $missing[] = $field;
    }
}

if (count($missing) > 0) {
    echo "MISSING_COLUMNS: " . implode(', ', $missing) . "\n";
} else {
    echo "ALL_COLUMNS_EXIST\n";
}
