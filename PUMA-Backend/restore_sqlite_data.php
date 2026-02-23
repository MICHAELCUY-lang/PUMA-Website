<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = ['cabinets', 'divisions', 'users', 'members'];
$sqlitePath = database_path('database.sqlite');

if (!file_exists($sqlitePath)) {
    die("SQLite database not found at $sqlitePath\n");
}

try {
    $sqlite = new PDO('sqlite:' . $sqlitePath);
    $sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed to SQLite: " . $e->getMessage() . "\n");
}

echo "Connected to SQLite.\n";
echo "Targeting MySQL Database: " . env('DB_DATABASE') . "\n";

DB::statement('SET FOREIGN_KEY_CHECKS=0;');

foreach ($tables as $table) {
    echo "Migrating table: $table\n";
    
    // 1. Truncate MySQL table
    DB::table($table)->truncate();
    
    // 2. Get data from SQLite
    try {
        $stmt = $sqlite->query("SELECT * FROM $table");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        echo "  - Error reading from SQLite table $table: " . $e->getMessage() . " (Skipping)\n";
        continue;
    }
    
    if (empty($rows)) {
        echo "  - SQLite table $table is empty.\n";
        continue;
    }
    
    // 3. Get MySQL columns to filter invalid fields
    $mysqlColumns = Schema::getColumnListing($table);
    
    $count = 0;
    foreach ($rows as $row) {
        $insertData = [];
        foreach ($mysqlColumns as $col) {
            if (array_key_exists($col, $row)) {
                $insertData[$col] = $row[$col];
            }
        }
        
        try {
            DB::table($table)->insert($insertData);
            $count++;
        } catch (Exception $e) {
            echo "  - Error inserting row ID " . ($row['id'] ?? '?') . ": " . $e->getMessage() . "\n";
        }
    }
    
    echo "  - Migrated $count rows.\n";
}

DB::statement('SET FOREIGN_KEY_CHECKS=1;');
echo "Data restoration complete.\n";
