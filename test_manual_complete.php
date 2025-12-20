<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Refund;

$refund = Refund::with('registration')->find(1);
echo "Before update:\n";
echo "  Refund Status: " . $refund->status . "\n";
echo "  Registration Status: " . $refund->registration->status . "\n";

// Update like controller does
$refund->update(['status' => 'completed', 'processed_at' => now()]);
if ($refund->registration) {
    $refund->registration->update(['status' => 'cancelled']);
}

// Refresh from DB
$refund->refresh();
$refund->registration->refresh();

echo "\nAfter update:\n";
echo "  Refund Status: " . $refund->status . "\n";
echo "  Registration Status: " . $refund->registration->status . "\n";
