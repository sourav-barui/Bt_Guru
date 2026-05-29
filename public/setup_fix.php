<?php
// TEMPORARY SETUP FIX FILE - DELETE IMMEDIATELY AFTER USE
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<pre style='font-family:monospace;font-size:14px;padding:16px;'>";

// Fix 1: Set APP_KEY in .env
$envPath = __DIR__ . '/../.env';
$appKey  = 'base64:3wwvp6k7ntwEmNJ13Zbaro3hUcBpck5P8BL3bU5pjg4=';

if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    if (strpos($envContent, 'APP_KEY=base64:') === false) {
        $envContent = preg_replace('/^APP_KEY=.*/m', 'APP_KEY=' . $appKey, $envContent);
        file_put_contents($envPath, $envContent);
        echo "OK APP_KEY set in .env\n";
    } else {
        echo "OK APP_KEY already set\n";
    }
} else {
    echo "ERR .env not found!\n";
}

// Fix 2: Delete stale bootstrap cache files
$cacheFiles = [
    __DIR__ . '/../bootstrap/cache/config.php',
    __DIR__ . '/../bootstrap/cache/routes.php',
    __DIR__ . '/../bootstrap/cache/routes-v7.php',
    __DIR__ . '/../bootstrap/cache/services.php',
    __DIR__ . '/../bootstrap/cache/packages.php',
    __DIR__ . '/../bootstrap/cache/events.php',
];

foreach ($cacheFiles as $file) {
    if (file_exists($file)) {
        unlink($file);
        echo "OK Deleted cache: " . basename($file) . "\n";
    }
}
echo "OK Bootstrap cache cleared\n";

// Fix 3: Boot Laravel and run migrations
echo "\n--- Artisan Commands ---\n";
try {
    require __DIR__ . '/../vendor/autoload.php';
    $app    = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

    $kernel->call('migrate', ['--force' => true]);
    echo "MIGRATE: " . $kernel->output() . "\n";

    $kernel->call('storage:link');
    echo "STORAGE: " . $kernel->output() . "\n";

    echo "\nOK Laravel environment: " . $app->environment() . "\n";
    echo "OK App URL: " . config('app.url') . "\n";
} catch (\Throwable $e) {
    echo "ERR " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\nDELETE THIS FILE (setup_fix.php) NOW!\n";
echo "</pre>";
