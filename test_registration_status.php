<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\CourseRegistration;

// Check registration status untuk refund yang completed
$registrations = CourseRegistration::whereHas('refund', function($q) {
    $q->where('status', 'completed');
})->with('refund', 'user', 'course')->get();

echo "\n=== REGISTRATIONS WITH COMPLETED REFUNDS ===\n";
if ($registrations->isEmpty()) {
    echo "No registrations with completed refunds found\n";
} else {
    foreach ($registrations as $reg) {
        echo "Registration ID: {$reg->id} | Status: {$reg->status} | User: {$reg->user->name} | Course: {$reg->course->title}\n";
        if ($reg->refund) {
            echo "  Refund Status: {$reg->refund->status}\n";
        }
    }
}
echo "\n";
