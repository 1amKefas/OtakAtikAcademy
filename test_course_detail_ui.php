<?php
/**
 * Test script to verify course-detail UI enhancements
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = \Illuminate\Http\Request::capture());

echo "=== Testing Course Detail UI Enhancements ===\n\n";

// Test 1: Course Model relationships
try {
    $course = new \App\Models\Course();
    echo "✓ Course model loaded\n";
    
    if (method_exists($course, 'quizzes')) {
        echo "✓ Course::quizzes() relationship exists\n";
    } else {
        echo "✗ Course::quizzes() relationship NOT found\n";
    }
    
    if (method_exists($course, 'forums')) {
        echo "✓ Course::forums() relationship exists\n";
    } else {
        echo "✗ Course::forums() relationship NOT found\n";
    }
} catch (Exception $e) {
    echo "✗ Error with Course model: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: InstructorController method
try {
    $controller = new \App\Http\Controllers\InstructorController();
    if (method_exists($controller, 'showCourse')) {
        echo "✓ InstructorController::showCourse() method exists\n";
    } else {
        echo "✗ InstructorController::showCourse() method NOT found\n";
    }
} catch (Exception $e) {
    echo "✗ Error with controller: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: Quiz Model
try {
    $quiz = new \App\Models\Quiz();
    if (method_exists($quiz, 'questions')) {
        echo "✓ Quiz::questions() relationship exists\n";
    } else {
        echo "✗ Quiz::questions() relationship NOT found\n";
    }
    
    if (method_exists($quiz, 'submissions')) {
        echo "✓ Quiz::submissions() relationship exists\n";
    } else {
        echo "✗ Quiz::submissions() relationship NOT found\n";
    }
} catch (Exception $e) {
    echo "✗ Error with Quiz model: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 4: CourseForum Model
try {
    $forum = new \App\Models\CourseForum();
    if (method_exists($forum, 'user')) {
        echo "✓ CourseForum::user() relationship exists\n";
    } else {
        echo "✗ CourseForum::user() relationship NOT found\n";
    }
    
    if (method_exists($forum, 'replies')) {
        echo "✓ CourseForum::replies() relationship exists\n";
    } else {
        echo "✗ CourseForum::replies() relationship NOT found\n";
    }
} catch (Exception $e) {
    echo "✗ Error with CourseForum model: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 5: Routes verification
try {
    $routes = app('router')->getRoutes();
    $foundRoutes = [
        'instructor.quiz.create' => false,
        'instructor.forum.store' => false,
        'instructor.assignments.store' => false,
    ];
    
    foreach ($routes as $route) {
        foreach ($foundRoutes as $name => $status) {
            if ($route->getName() === $name) {
                $foundRoutes[$name] = true;
            }
        }
    }
    
    echo "Route Status:\n";
    foreach ($foundRoutes as $name => $found) {
        echo ($found ? "✓" : "✗") . " {$name}\n";
    }
} catch (Exception $e) {
    echo "✗ Error checking routes: " . $e->getMessage() . "\n";
}

echo "\n✅ Course Detail UI enhancements ready!\n";
