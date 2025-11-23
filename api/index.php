<?php

use Illuminate\Http\Request;

// 1. Definisikan waktu mulai (Penting buat Laravel)
define('LARAVEL_START', microtime(true));

// 2. Cek Autoload
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
} else {
    // Fallback kalau vendor ada di level atas (kadang struktur vercel beda)
    require __DIR__ . '/vendor/autoload.php';
}

// 3. Booting App
$app = require_once __DIR__ . '/../bootstrap/app.php';

// --- 🔥 VERCEL FIXES (JANGAN DIHAPUS) 🔥 ---

// A. Pindahin STORAGE ke /tmp (Biar bisa upload/log)
$app->useStoragePath('/tmp/storage');

// B. Pindahin BOOTSTRAP CACHE ke /tmp (INI SOLUSI ERROR 500 LU!)
$app->useBootstrapPath('/tmp/bootstrap');

// C. Bikin struktur folder manual di /tmp karena server Vercel itu kosong
$dirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache', // <--- Folder sakti penyelamat error
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}
// ---------------------------------------------

// 4. Jalanin Request
$app->handleRequest(Request::capture());