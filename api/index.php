<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Load Composer
require __DIR__ . '/../vendor/autoload.php';

// 2. Boot Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 3. FIX VERCEL: Redirect Storage ke Folder Sementara (/tmp)
$storagePath = '/tmp/storage';
$app->useStoragePath($storagePath);

// 4. FIX VERCEL: Bikin struktur folder manual biar gak error permission
if (!is_dir($storagePath . '/framework/views')) {
    mkdir($storagePath . '/framework/views', 0777, true);
}
if (!is_dir($storagePath . '/framework/cache')) {
    mkdir($storagePath . '/framework/cache', 0777, true);
}
if (!is_dir($storagePath . '/framework/sessions')) {
    mkdir($storagePath . '/framework/sessions', 0777, true);
}

// 5. Handle Request
$app->handleRequest(Request::capture());