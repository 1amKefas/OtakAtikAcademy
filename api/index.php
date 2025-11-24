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
| VERCEL FIXES (Robust Version)
|--------------------------------------------------------------------------
|
| Kita pindahkan storage dan bootstrap cache ke /tmp (writable),
| TAPI kita juga harus copy file cache yang sudah ada biar config kebaca.
|
*/

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
        mkdir($dir, 0777, true);
    }
}

// COPY file cache dari folder asli ke /tmp (PENTING!)
// Ini mencegah error "Target class [view] does not exist" karena config tidak terbaca
$files = glob(__DIR__ . '/../bootstrap/cache/*.php');
foreach ($files as $file) {
    $dest = '/tmp/bootstrap/cache/' . basename($file);
    if (!file_exists($dest)) {
        copy($file, $dest);
    }
}

// Jalankan Request
$app->handleRequest(Request::capture());