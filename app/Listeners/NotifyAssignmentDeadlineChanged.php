<?php

namespace App\Listeners;

use App\Events\AssignmentDeadlineChanged;
use App\Models\Notification;

class NotifyAssignmentDeadlineChanged
{
    public function handle(AssignmentDeadlineChanged $event): void
    {
        $assignment = $event->assignment;
        $course = $assignment->course;
        $oldDeadline = $event->oldDeadline;
        $newDeadline = $event->newDeadline;

        // Get all enrolled students in this course
        $enrolledStudents = $course->activeRegistrations()
            ->pluck('user_id')
            ->toArray();

        // Create notification for each student
        foreach ($enrolledStudents as $userId) {
            Notification::create([
                'user_id' => $userId,
                'type' => 'assignment_deadline_changed',
                'title' => 'Assignment Deadline Updated: ' . $assignment->title,
                'message' => "The deadline for '{$assignment->title}' has been changed from " .
                    $oldDeadline->format('M d, Y H:i') . " to " .
                    $newDeadline->format('M d, Y H:i'),
                'course_id' => $course->id,
                'course_assignment_id' => $assignment->id,
            ]);
        }
    }
}
