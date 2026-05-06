<?php

use Illuminate\Console\Scheduling\Schedule;

define('LARAVEL_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';

// Use /tmp for writable directories on Vercel
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Set storage path to /tmp
$app->useStoragePath('/tmp/storage');

// Create necessary directories in /tmp
$directories = [
    '/tmp/storage/app',
    '/tmp/storage/app/public',
    '/tmp/storage/framework',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

$app->useStoragePath('/tmp/storage');

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
)->send();

$kernel->terminate($request, $response);