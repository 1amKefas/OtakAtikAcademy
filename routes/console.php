<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; // Pastikan ada ini

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// --- EXISTING SCHEDULES ---
Schedule::command('check:course-expiration')->hourly();

// --- [BARU] NFR-2: BACKUP DATABASE MINGGUAN ---
// Jalan setiap hari Minggu jam 00:00
Schedule::command('backup:clean')->weekly()->at('01:00'); // Hapus backup yg terlalu lama
Schedule::command('backup:run --only-db')->weekly()->at('01:30'); // Backup DB saja (biar hemat storage)