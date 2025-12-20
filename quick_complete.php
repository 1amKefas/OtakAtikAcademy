<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Refund;

$refund = Refund::find(1);
echo "Status before: " . $refund->status . "\n";

$refund->status = 'completed';
$refund->completed_at = \Illuminate\Support\Facades\DB::raw('NOW()');
$refund->save();

$refund->refresh();
echo "Status after: " . $refund->status . "\n";
echo "Done\n";
