<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = \Illuminate\Http\Request::capture()
);

use App\Models\User;

// Ambil user dengan ID 3 (atau sesuaikan)
$user = User::find(3);

if ($user) {
    echo "User ID: " . $user->id . "\n";
    echo "Name: " . $user->name . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Profile Picture (DB): " . ($user->profile_picture ?? 'NULL') . "\n";
    echo "Profile Picture Exists: " . (\Illuminate\Support\Facades\Storage::exists($user->profile_picture) ? 'YES' : 'NO') . "\n";
    if ($user->profile_picture) {
        echo "Storage URL: " . \Illuminate\Support\Facades\Storage::url($user->profile_picture) . "\n";
    }
} else {
    echo "User not found\n";
}
?>
