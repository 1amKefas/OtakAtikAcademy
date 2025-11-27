<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\User;
use App\Models\CourseRegistration;
use App\Models\QuizSubmission;

class AchievementService
{
    /**
     * Check and award badges for user
     */
    public function checkAndAwardBadges(User $user)
    {
        try {
            // Get all achievements
            $achievements = Achievement::all();

            foreach ($achievements as $achievement) {
                // Skip if already earned
                if ($user->achievements()->where('achievements.id', $achievement->id)->exists()) {
                    continue;
                }

                // Check if user meets requirement
                if ($this->meetsRequirement($user, $achievement)) {
                    $user->achievements()->attach($achievement->id, [
                        'earned_at' => now(),
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Silent fail if achievements table doesn't exist
            return;
        }
    }

    /**
     * Check if user meets achievement requirement
     */
    private function meetsRequirement(User $user, Achievement $achievement): bool
    {
        $type = $achievement->requirement_type;
        $value = $achievement->requirement_value;

        return match($type) {
            // 1 = courses_completed
            1 => $this->getCoursesCompleted($user) >= $value,
            
            // 2 = hours_learned
            2 => $this->getTotalHours($user) >= $value,
            
            // 3 = quiz_score (100% on any quiz)
            3 => $this->hasHighQuizScore($user, $value),
            
            // 4 = day_streak
            4 => $this->getDayStreak($user) >= $value,
            
            // 5 = top_performer (top 10%)
            5 => $this->isTopPerformer($user),
            
            // 6 = fast_learner (complete course in < 7 days)
            6 => $this->isFastLearner($user),
            
            default => false,
        };
    }

    /**
     * Get number of completed courses
     */
    private function getCoursesCompleted(User $user): int
    {
        return $user->courseRegistrations()
            ->where('status', 'paid')
            ->where('progress', '>=', 100)
            ->count();
    }

    /**
     * Get total learning hours
     */
    private function getTotalHours(User $user): int
    {
        return $user->courseRegistrations()
            ->where('status', 'paid')
            ->get()
            ->sum(function ($reg) {
                return $reg->course->duration ?? 0;
            });
    }

    /**
     * Check if user has high quiz score
     */
    private function hasHighQuizScore(User $user, int $targetScore): bool
    {
        return $user->quizSubmissions()
            ->where('score', '>=', $targetScore)
            ->exists();
    }

    /**
     * Get current day streak (consecutive days of activity)
     */
    private function getDayStreak(User $user): int
    {
        $submissions = $user->quizSubmissions()
            ->orderBy('created_at', 'desc')
            ->select('created_at')
            ->get();

        if ($submissions->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $currentDate = now()->startOfDay();

        foreach ($submissions as $submission) {
            $submissionDate = $submission->created_at->startOfDay();
            
            if ($submissionDate->diffInDays($currentDate) == $streak) {
                $streak++;
                $currentDate = $submissionDate;
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Check if user is top performer (avg score >= 85%)
     */
    private function isTopPerformer(User $user): bool
    {
        $avgScore = $user->quizSubmissions()->avg('score');
        return $avgScore >= 85;
    }

    /**
     * Check if user is fast learner (complete course < 7 days after enrollment)
     */
    private function isFastLearner(User $user): bool
    {
        return $user->courseRegistrations()
            ->where('status', 'paid')
            ->where('progress', '>=', 100)
            ->whereNotNull('enrolled_at')
            ->get()
            ->some(function ($reg) {
                if (!$reg->enrolled_at || !$reg->updated_at) {
                    return false;
                }
                $days = $reg->enrolled_at->diffInDays($reg->updated_at);
                return $days <= 7;
            });
    }
}
