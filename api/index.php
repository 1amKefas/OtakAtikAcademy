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

    // 4. Jalanin Request
    $app->handleRequest(Request::capture());

} catch (\Throwable $e) {
    // INI DIA! Kita tampilin error aslinya ke layar browser
    http_response_code(500);
    echo "<div style='font-family: monospace; background: #1a202c; color: #e2e8f0; padding: 20px;'>";
    
    echo "<h1 style='color: #ef4444;'>💥 ERROR UTAMA (ROOT CAUSE):</h1>";
    
    // Loop buat nyari biang kerok aslinya (Previous Exception)
    $current = $e;
    $count = 1;
    do {
        echo "<div style='border: 1px solid #4a5568; padding: 10px; margin-bottom: 10px;'>";
        echo "<h3 style='margin:0; color: #fbbf24;'>#$count: " . get_class($current) . "</h3>";
        echo "<p style='margin:5px 0;'><strong>Message:</strong> " . $current->getMessage() . "</p>";
        echo "<p style='margin:5px 0; color: #94a3b8;'><strong>File:</strong> " . $current->getFile() . ":" . $current->getLine() . "</p>";
        echo "</div>";
        $current = $current->getPrevious();
        $count++;
    } while ($current);

    echo "<hr style='border-color: #475569;'>";
    
    echo "<h3>🔍 DEBUG INVESTIGATION:</h3>";
    // Cek apakah folder Yajra (Oracle) masih ada di server?
    $yajraExists = is_dir(__DIR__ . '/../vendor/yajra') ? '<span style="color:red">YES (MASIH ADA!)</span>' : '<span style="color:green">NO (AMAN)</span>';
    echo "<p><strong>Folder 'vendor/yajra' Exists?</strong> $yajraExists</p>";
    
    // Cek Config Cache
    $cacheExists = file_exists(__DIR__ . '/../bootstrap/cache/config.php') ? '<span style="color:red">YES (BAHAYA)</span>' : '<span style="color:green">NO (AMAN)</span>';
    echo "<p><strong>Config Cache Exists?</strong> $cacheExists</p>";

    echo "</div>";
    exit;
}