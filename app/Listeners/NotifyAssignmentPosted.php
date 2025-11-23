<?php

namespace App\Listeners;

use App\Events\AssignmentPosted;
use App\Models\Notification;

class NotifyAssignmentPosted
{
    public function handle(AssignmentPosted $event): void
    {
        $assignment = $event->assignment;
        $course = $assignment->course;

        // Get all enrolled students in this course
        // use Course model's activeRegistrations() helper which already filters paid
        $enrolledStudents = $course->activeRegistrations()
            ->pluck('user_id')
            ->toArray();

        // Create notification for each student
        foreach ($enrolledStudents as $userId) {
            Notification::create([
                'user_id' => $userId,
                'type' => 'assignment_posted',
                'title' => 'New Assignment: ' . $assignment->title,
                'message' => "Your instructor posted a new assignment: '{$assignment->title}' (Due: {$assignment->due_date->format('M d, Y')})",
                'course_id' => $course->id,
                'course_assignment_id' => $assignment->id,
            ]);
        }
    }
}
