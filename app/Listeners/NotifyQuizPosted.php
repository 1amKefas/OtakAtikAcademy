<?php

namespace App\Listeners;

use App\Events\QuizPosted;
use App\Models\Notification;

class NotifyQuizPosted
{
    public function handle(QuizPosted $event): void
    {
        $quiz = $event->quiz;
        $course = $quiz->course;

        // Only notify if quiz is published
        if (!$quiz->is_published) {
            return;
        }

        // Get all enrolled students in this course
        $enrolledStudents = $course->activeRegistrations()
            ->pluck('user_id')
            ->toArray();

        // Create notification for each student
        foreach ($enrolledStudents as $userId) {
            Notification::create([
                'user_id' => $userId,
                'type' => 'quiz_posted',
                'title' => 'New Quiz: ' . $quiz->title,
                'message' => "Your instructor posted a new quiz: '{$quiz->title}' ({$quiz->duration_minutes} minutes)",
                'course_id' => $course->id,
                'quiz_id' => $quiz->id,
            ]);
        }
    }
}
