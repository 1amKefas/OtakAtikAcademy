<?php

use Illuminate\Http\Request;

// Pasang CCTV Error (Tangkap semua error fatal)
set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

try {
    define('LARAVEL_START', microtime(true));

    // 1. Cek apakah autoload ada
    if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
        die("CRITICAL: Folder 'vendor' tidak ditemukan! Build Vercel gagal install dependency.");
    }
    require __DIR__ . '/../vendor/autoload.php';

    // 2. Booting App
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // 3. Fix Storage Vercel
    $app->useStoragePath('/tmp/storage');

    // 4. Jalanin Request dengan Try-Catch
    $app->handleRequest(Request::capture());

} catch (\Throwable $e) {
    // INI DIA! Kita tampilin error aslinya ke layar browser
    http_response_code(500);
    echo "<div style='font-family: monospace; background: #1a202c; color: #e2e8f0; padding: 20px;'>";
    echo "<h1 style='color: #ef4444;'>🔥 TERTANGKAP ERROR ASLI:</h1>";
    echo "<h3>" . get_class($e) . ": " . $e->getMessage() . "</h3>";
    echo "<p><strong>File:</strong> " . $e->getFile() . " (Line: " . $e->getLine() . ")</p>";
    echo "<hr style='border-color: #475569;'>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre style='white-space: pre-wrap;'>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
    exit;
}