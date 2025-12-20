<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Refund;

echo "\n=== UPDATING REFUND #1 TO COMPLETED ===\n";

$refund = Refund::find(1);
echo "Before: Status = " . $refund->status . "\n";

// Update like the controller does
$refund->update([
    'status' => 'completed',
    'completed_at' => now(),
    'admin_notes' => 'Refund berhasil diselesaikan.'
]);

// Refresh from DB
$refund->refresh();
echo "After: Status = " . $refund->status . "\n";
echo "Completed at: " . $refund->completed_at . "\n";

echo "\nDone!\n";
