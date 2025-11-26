<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Cek Autoload
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
} else {
    require __DIR__ . '/vendor/autoload.php';
}

// Bootstrap App
$app = require_once __DIR__ . '/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| VERCEL FIXES (Enhanced Error Handling)
|--------------------------------------------------------------------------
|
| Pindahkan storage dan bootstrap cache ke /tmp (writable),
| Copy file cache yang sudah ada biar config kebaca
|
*/

try {
    $app->useStoragePath('/tmp/storage');
    $app->useBootstrapPath('/tmp/bootstrap');

    // Buat struktur folder di /tmp
    $dirs = [
        '/tmp/storage/framework/views',
        '/tmp/storage/framework/cache/data',
        '/tmp/storage/framework/sessions',
        '/tmp/storage/logs',
        '/tmp/bootstrap/cache',
    ];

    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
    }

    // COPY file cache dari folder asli ke /tmp (PENTING!)
    $files = @glob(__DIR__ . '/../bootstrap/cache/*.php');
    if ($files) {
        foreach ($files as $file) {
            $dest = '/tmp/bootstrap/cache/' . basename($file);
            if (!file_exists($dest)) {
                @copy($file, $dest);
            }
        }
    }
} catch (Exception $e) {
    error_log("Vercel cache setup error: " . $e->getMessage());
}

// Jalankan Request dengan error handling
try {
    $app->handleRequest(Request::capture());
} catch (Exception $e) {
    error_log("Fatal error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal Server Error',
        'message' => 'An error occurred while processing your request'
    ]);
}