<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

echo "Testing Event Creation API...\n";

$response = Http::post('http://localhost:8000/api/events', [
    'title' => 'API Test Event',
    'description' => 'Created via test script',
    'event_date' => date('Y-m-d H:i:s'),
    'status' => 'planned',
    'location' => 'Test Location',
    'cabinet_id' => null
]);

echo "Status: " . $response->status() . "\n";
echo "Body: " . $response->body() . "\n";

if ($response->successful()) {
    echo "SUCCESS: Event created.\n";
    // Clean up
    $data = $response->json();
    if (isset($data['data']['id'])) {
        \App\Models\Event::destroy($data['data']['id']);
        echo "Cleaned up event ID " . $data['data']['id'] . "\n";
    }
} else {
    echo "FAILED.\n";
}
