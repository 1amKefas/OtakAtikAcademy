<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// --- 🔥 FITUR PEMBERSIH CACHE OTOMATIS 🔥 ---
// Ini bakal ngehapus file konfigurasi lama yang bikin error 500
$cacheFiles = [
    __DIR__ . '/../bootstrap/cache/config.php',
    __DIR__ . '/../bootstrap/cache/packages.php',
    __DIR__ . '/../bootstrap/cache/services.php'
];

foreach ($cacheFiles as $file) {
    if (file_exists($file)) {
        @unlink($file); // Hapus paksa tanpa ampun
    }
}
// ---------------------------------------------

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// FIX VERCEL: Storage ke folder sementara
$app->useStoragePath('/tmp/storage');

$app->handleRequest(Request::capture());