<?php
define('LARAVEL_START', microtime(true));

// Create writable directories in /tmp
$dirs = [
    '/tmp/storage/app/public',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];
foreach ($dirs as $dir) {
    is_dir($dir) || mkdir($dir, 0777, true);
}

// Copy bootstrap cache files to /tmp
$cacheDir = __DIR__ . '/../bootstrap/cache';
if (is_dir($cacheDir)) {
    foreach (glob($cacheDir . '/*.php') as $file) {
        $dest = '/tmp/bootstrap/cache/' . basename($file);
        if (!file_exists($dest)) copy($file, $dest);
    }
}

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->useStoragePath('/tmp/storage');

if (method_exists($app, 'useBootstrapPath')) {
    $app->useBootstrapPath('/tmp/bootstrap');
} else {
    $app->instance('path.bootstrap', '/tmp/bootstrap');
}

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture())->send();
$kernel->terminate($request, $response);