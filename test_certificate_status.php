<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check for 100% complete registrations
$registrations = \App\Models\CourseRegistration::where('progress_percentage', 100)
    ->with('user', 'course')
    ->limit(5)
    ->get();

echo "=== Course Registrations at 100% Completion ===\n";
if ($registrations->isEmpty()) {
    echo "No registrations at 100% completion found.\n";
    // Check for any registrations
    $any = \App\Models\CourseRegistration::with('user', 'course')->limit(3)->get();
    echo "\n=== Sample Registrations ===\n";
    foreach ($any as $reg) {
        echo "User: {$reg->user->name} | Course: {$reg->course->title} | Progress: {$reg->progress_percentage}%\n";
    }
} else {
    foreach ($registrations as $reg) {
        echo "✓ User: {$reg->user->name} | Course: {$reg->course->title} | Progress: {$reg->progress_percentage}%\n";
        
        // Check if certificate exists
        $cert = \App\Models\Certificate::where('user_id', $reg->user_id)
            ->where('course_id', $reg->course_id)
            ->first();
        
        if ($cert) {
            echo "  → Certificate exists: {$cert->certificate_number}\n";
            echo "  → Verification Code: {$cert->verification_code}\n";
        } else {
            echo "  → No certificate yet (will be auto-generated on download)\n";
        }
    }
}

echo "\n=== Template Check ===\n";
$templatePath = 'resources/views/certificates/template.blade.php';
$template = file_get_contents($templatePath);

// Check for CEO text
if (strpos($template, 'CEO') !== false) {
    echo "⚠️  Found 'CEO' text in template\n";
} else {
    echo "✓ No 'CEO' text in template\n";
}

// Check for gradient background
if (strpos($template, 'linear-gradient') !== false) {
    echo "✓ Gradient background found in template\n";
} else {
    echo "⚠️  No gradient background in template\n";
}

// Check for pattern overlay
if (strpos($template, 'radial-gradient') !== false) {
    echo "✓ Pattern overlay (radial-gradient) found in template\n";
} else {
    echo "⚠️  No pattern overlay in template\n";
}
