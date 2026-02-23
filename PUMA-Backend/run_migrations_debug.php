<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

try {
    echo "Starting migrations...\n";
    \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--force' => true]);
    echo "Migrations successful.\n";
    echo \Illuminate\Support\Facades\Artisan::output();
} catch (\Exception $e) {
    file_put_contents('error.log', $e->getMessage());
    echo "Error logged to error.log\n";
}
