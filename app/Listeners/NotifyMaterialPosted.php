<?php

namespace App\Listeners;

use App\Events\MaterialPosted;
use App\Models\Notification;

class NotifyMaterialPosted
{
    public function handle(MaterialPosted $event): void
    {
        $material = $event->material;
        $course = $material->course;

        // Get all enrolled students in this course
        $enrolledStudents = $course->activeRegistrations()
            ->pluck('user_id')
            ->toArray();

        // Create notification for each student
        foreach ($enrolledStudents as $userId) {
            Notification::create([
                'user_id' => $userId,
                'type' => 'material_posted',
                'title' => 'New Material: ' . $material->title,
                'message' => "Your instructor uploaded new material: '{$material->title}'",
                'course_id' => $course->id,
            ]);
        }
    }
}
