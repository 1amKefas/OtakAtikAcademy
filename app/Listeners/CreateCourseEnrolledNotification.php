<?php

namespace App\Listeners;

use App\Events\CourseEnrolled;
use App\Models\Notification;

class CreateCourseEnrolledNotification
{
    public function handle(CourseEnrolled $event): void
    {
        $registration = $event->registration;
        
        Notification::create([
            'user_id' => $registration->user_id,
            'type' => 'course_purchased',
            'title' => 'Course Enrollment Successful',
            'message' => "You have successfully enrolled in '{$registration->course->title}'",
            'course_id' => $registration->course_id,
        ]);
    }
}
