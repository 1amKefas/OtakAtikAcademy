<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$response = $kernel->handle(
    $request = \Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// Set password baru di sini
$newPassword = 'password123'; // GANTI DENGAN PASSWORD YANG DIINGINKAN
$email = 'kefaskefas10@gmail.com';

try {
    $hashedPassword = Hash::make($newPassword);
    
    $updated = DB::table('users')
        ->where('email', $email)
        ->update(['password' => $hashedPassword]);
    
    if ($updated) {
        echo "✓ Password berhasil di-reset untuk: $email\n";
        echo "✓ Password baru di-hash dengan bcrypt\n";
        echo "✓ Silakan login dengan password baru\n";
    } else {
        echo "✗ User tidak ditemukan: $email\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
?>
