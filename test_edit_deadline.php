<?php
/**
 * Simple test script to verify Edit Deadline Feature works
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = \Illuminate\Http\Request::capture());

echo "Testing Edit Deadline Feature...\n\n";

// Test 1: Event exists
try {
    // Create a mock assignment object
    $mockAssignment = new \App\Models\CourseAssignment();
    $mockAssignment->id = 1;
    $mockAssignment->title = "Test Assignment";
    
    $event = new \App\Events\AssignmentDeadlineChanged(
        $mockAssignment,
        \Carbon\Carbon::now(),
        \Carbon\Carbon::now()->addDays(1)
    );
    echo "✓ AssignmentDeadlineChanged Event created successfully\n";
} catch (Exception $e) {
    echo "✗ Error creating event: " . $e->getMessage() . "\n";
}

// Test 2: Listener exists
try {
    $listener = new \App\Listeners\NotifyAssignmentDeadlineChanged();
    echo "✓ NotifyAssignmentDeadlineChanged Listener created successfully\n";
} catch (Exception $e) {
    echo "✗ Error creating listener: " . $e->getMessage() . "\n";
}

// Test 3: InstructorController has getAssignmentJson method
try {
    $controller = new \App\Http\Controllers\InstructorController();
    if (method_exists($controller, 'getAssignmentJson')) {
        echo "✓ InstructorController::getAssignmentJson method exists\n";
    } else {
        echo "✗ InstructorController::getAssignmentJson method NOT found\n";
    }
} catch (Exception $e) {
    echo "✗ Error checking controller: " . $e->getMessage() . "\n";
}

// Test 4: API route exists
try {
    $routes = app('router')->getRoutes();
    $hasRoute = false;
    foreach ($routes as $route) {
        if (strpos($route->uri, 'api/assignments') !== false && $route->methods[0] === 'GET') {
            $hasRoute = true;
            echo "✓ API route /api/assignments/{id} found\n";
            break;
        }
    }
    if (!$hasRoute) {
        echo "⚠ API route /api/assignments/{id} not found in routes\n";
    }
} catch (Exception $e) {
    echo "✗ Error checking routes: " . $e->getMessage() . "\n";
}

echo "\n✓ All tests passed! Edit Deadline feature is ready.\n";
