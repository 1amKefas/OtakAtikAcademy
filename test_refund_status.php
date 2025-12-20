<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Refund;

$refunds = Refund::with('user', 'registration.course')->get();

echo "\n=== REFUND STATUS CHECK ===\n";
foreach ($refunds as $refund) {
    echo "ID: {$refund->id} | Status: {$refund->status} | User: {$refund->user->name} | Course: {$refund->registration->course->title}\n";
}
echo "\n";
