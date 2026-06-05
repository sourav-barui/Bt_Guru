<?php
// Emergency cache clear - place in public/ and visit via browser
// DELETE AFTER USE!

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;

echo "<pre>Clearing caches...\n\n";

try {
    Artisan::call('view:clear');
    echo "✅ Views cleared\n";
    
    Artisan::call('config:clear');
    echo "✅ Config cleared\n";
    
    Artisan::call('route:clear');
    echo "✅ Routes cleared\n";
    
    Artisan::call('cache:clear');
    echo "✅ Cache cleared\n";
    
    echo "\n✅ All caches cleared!";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

echo "</pre>";
