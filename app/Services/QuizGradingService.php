<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\QuizSubmission;
use App\Models\QuizQuestion;
use Illuminate\Support\Collection;

/**
 * Service untuk handle auto-grading quiz
 * Mendukung: multiple choice, true/false, essay (manual)
 */
class QuizGradingService
{
    /**
     * Grade a quiz submission
     * @param QuizSubmission $submission
     * @param array $answers Format: ['question_id' => 'answer']
     * @return array ['score' => float, 'total_points' => float, 'details' => Collection]
     */
    public function gradeSubmission(QuizSubmission $submission, array $answers = []): array
    {
        $quiz = $submission->quiz;
        $questions = $quiz->questions;
        
        $totalQuestions = 0;
        $correctAnswers = 0;
        $details = [];

        foreach ($questions as $question) {
            $totalQuestions++;

            // Get answer dari submission atau dari parameter
            $userAnswer = $answers[$question->id] ?? $submission->answers[$question->id] ?? null;

            $isCorrect = false;
            $feedback = '';

            // Auto-grade untuk multiple choice dan true/false
            if ($question->question_type === 'multiple_choice' || $question->question_type === 'true_false') {
                $isCorrect = $this->checkAnswer($question, $userAnswer);
                if ($isCorrect) {
                    $correctAnswers++;
                    $feedback = 'Correct!';
                } else {
                    $feedback = 'Incorrect. Correct answer: ' . $question->correct_answer;
                }
            } 
            // Essay: perlu manual grading
            elseif ($question->question_type === 'essay') {
                $feedback = 'Pending manual grading';
            }

            $details[] = [
                'question_id' => $question->id,
                'question_text' => $question->question,
                'question_type' => $question->question_type,
                'user_answer' => $userAnswer,
                'correct_answer' => $question->correct_answer,
                'is_correct' => $isCorrect,
                'feedback' => $feedback
            ];
        }

        $percentage = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;
        $isPassed = $percentage >= $quiz->passing_score;

        // Update submission dengan hasil
        $submission->update([
            'score' => round($percentage, 2),
            'correct_answers' => $correctAnswers,
            'answers' => $answers ?: $submission->answers,
            'graded_at' => now(),
            'graded_by' => 'auto' // untuk multiple choice/true false
        ]);

        return [
            'score' => round($percentage, 2),
            'correct_answers' => $correctAnswers,
            'total_questions' => $totalQuestions,
            'percentage' => round($percentage, 2),
            'is_passed' => $isPassed,
            'details' => collect($details)
        ];
    }

    /**
     * Check if user answer matches correct answer
     * @param QuizQuestion $question
     * @param mixed $userAnswer
     * @return bool
     */
    private function checkAnswer(QuizQuestion $question, $userAnswer): bool
    {
        if ($userAnswer === null || $userAnswer === '') {
            return false;
        }

        // For multiple choice (compare as numbers/string indices)
        if ($question->question_type === 'multiple_choice') {
            // Convert both to string for comparison to handle "0" vs 0
            $userAnswerStr = (string)$userAnswer;
            $correctAnswerStr = (string)$question->correct_answer;
            return $userAnswerStr === $correctAnswerStr;
        }

        // For true/false
        if ($question->question_type === 'true_false') {
            $userAnswerStr = strtolower(trim((string)$userAnswer));
            $correctAnswerStr = strtolower(trim((string)$question->correct_answer));
            
            // Normalize variations
            $normalized = ['true' => 'true', '1' => 'true', 'yes' => 'true', 
                          'false' => 'false', '0' => 'false', 'no' => 'false'];
            
            $normalizedUser = $normalized[$userAnswerStr] ?? $userAnswerStr;
            $normalizedCorrect = $normalized[$correctAnswerStr] ?? $correctAnswerStr;
            
            return $normalizedUser === $normalizedCorrect;
        }

        return false;
    }

    /**
     * Get average score for a quiz
     */
    public function getAverageScore(Quiz $quiz): float
    {
        $submissions = $quiz->submissions()->whereNotNull('score')->get();
        
        if ($submissions->isEmpty()) {
            return 0;
        }

        return $submissions->avg('percentage');
    }

    /**
     * Get pass rate for a quiz
     */
    public function getPassRate(Quiz $quiz): float
    {
        $submissions = $quiz->submissions()->whereNotNull('score')->get();
        
        if ($submissions->isEmpty()) {
            return 0;
        }

        $passed = $submissions->where('is_passed', true)->count();
        return ($passed / $submissions->count()) * 100;
    }
}
