<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Load Composer
require __DIR__ . '/../vendor/autoload.php';

// 2. Boot Laravel (Pake struktur baru bootstrap/app.php)
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 3. FIX VERCEL (Storage Read-Only)
// Wajib banget buat Laravel di serverless biar gak error permission
$app->useStoragePath('/tmp/storage');

// 4. Handle Request (Cara Modern Laravel 11/12)
$app->handleRequest(Request::capture());