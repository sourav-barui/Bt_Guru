<?php
// TEMPORARY CACHE CLEAR FILE - DELETE IMMEDIATELY AFTER USE
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<pre style='font-family:monospace;font-size:14px;padding:16px;'>";

try {
    require __DIR__ . '/../vendor/autoload.php';
    $app    = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

    $kernel->call('view:clear');
    echo "OK View cache cleared\n";

    $kernel->call('config:clear');
    echo "OK Config cache cleared\n";

    echo "\nDONE. DELETE THIS FILE (clear_cache.php) NOW!\n";
} catch (\Throwable $e) {
    echo "ERR " . $e->getMessage() . "\n";
}
echo "</pre>";
