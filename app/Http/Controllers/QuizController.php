<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizSubmission;
use App\Models\Course;
use App\Services\QuizGradingService;
use App\Events\QuizPosted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class QuizController extends Controller
{
    protected $gradingService;

    public function __construct(QuizGradingService $gradingService)
    {
        $this->gradingService = $gradingService;
    }

    // ==================== INSTRUCTOR ROUTES ====================

    /**
     * Show all quizzes for a course (Instructor)
     */
    public function index($courseId)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::findOrFail($courseId);
        
        // Check if instructor owns this course
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $quizzes = $course->quizzes()->latest()->get();

        return view('instructor.quiz.index', compact('course', 'quizzes'));
    }

    /**
     * Show create quiz form (Instructor)
     */
    public function create($courseId)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::findOrFail($courseId);
        
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('instructor.quiz.create', compact('course'));
    }

    /**
     * Store new quiz (Instructor)
     */
    public function store(Request $request, $courseId)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::findOrFail($courseId);
        
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:5|max:300',
            'passing_score' => 'required|integer|min:0|max:100',
            'available_from' => 'nullable|date_format:Y-m-d\TH:i',
            'available_until' => 'nullable|date_format:Y-m-d\TH:i|after:available_from',
            'is_published' => 'boolean'
        ]);

        $quiz = Quiz::create([
            'course_id' => $course->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'duration_minutes' => $validated['duration_minutes'],
            'passing_score' => $validated['passing_score'],
            'available_from' => $validated['available_from'] ?? null,
            'available_until' => $validated['available_until'] ?? null,
            'is_published' => $validated['is_published'] ?? false
        ]);

        // Dispatch event to notify students if quiz is published
        if ($quiz->is_published) {
            QuizPosted::dispatch($quiz);
        }

        return redirect()->route('instructor.quiz.edit', [$courseId, $quiz->id])
            ->with('success', 'Quiz berhasil dibuat! Sekarang tambahkan soal.');
    }

    /**
     * Show quiz editor (Instructor)
     */
    public function edit($courseId, $quizId)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::findOrFail($courseId);
        $quiz = Quiz::findOrFail($quizId);

        if ($course->instructor_id !== Auth::id() || $quiz->course_id !== $course->id) {
            abort(403, 'Unauthorized action.');
        }

        $questions = $quiz->questions()->orderBy('order')->get();

        return view('instructor.quiz.edit', compact('course', 'quiz', 'questions'));
    }

    /**
     * Update quiz (Instructor)
     */
    public function update(Request $request, $courseId, $quizId)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::findOrFail($courseId);
        $quiz = Quiz::findOrFail($quizId);

        if ($course->instructor_id !== Auth::id() || $quiz->course_id !== $course->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:5|max:300',
            'passing_score' => 'required|integer|min:0|max:100',
            'available_from' => 'nullable|date_format:Y-m-d\TH:i',
            'available_until' => 'nullable|date_format:Y-m-d\TH:i|after:available_from',
            'is_published' => 'boolean'
        ]);

        $quiz->update($validated);

        return back()->with('success', 'Quiz berhasil diupdate!');
    }

    /**
     * Delete quiz (Instructor)
     */
    public function destroy($courseId, $quizId)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::findOrFail($courseId);
        $quiz = Quiz::findOrFail($quizId);

        if ($course->instructor_id !== Auth::id() || $quiz->course_id !== $course->id) {
            abort(403, 'Unauthorized action.');
        }

        $quiz->delete();

        return redirect()->route('instructor.quiz.index', $courseId)
            ->with('success', 'Quiz berhasil dihapus!');
    }

    /**
     * Show form for creating question
     */
    public function createQuestion($courseId, $quizId)
    {
        $course = Course::findOrFail($courseId);
        $quiz = Quiz::findOrFail($quizId);

        if ($course->instructor_id !== Auth::id() || $quiz->course_id !== $course->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('instructor.quiz.add-question', compact('course', 'quiz'));
    }

    /**
     * Show form for editing question
     */
    public function editQuestion($courseId, $quizId, $questionId)
    {
        $course = Course::findOrFail($courseId);
        $quiz = Quiz::findOrFail($quizId);
        $question = QuizQuestion::findOrFail($questionId);

        if ($course->instructor_id !== Auth::id() || $quiz->course_id !== $course->id || $question->quiz_id !== $quiz->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('instructor.quiz.add-question', compact('course', 'quiz', 'question'));
    }

    /**
     * Store new question
     * [FIXED] Ditambahkan $courseId di parameter pertama
     */
    public function addQuestion(Request $request, $courseId, $quizId)
    {
        // Validasi
        $request->validate([
            'question' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false,essay,multiple_select', 
            'points' => 'nullable|integer|min:1',
        ]);

        $quiz = Quiz::where('course_id', $courseId)->findOrFail($quizId);

        $questionData = [
            'quiz_id' => $quiz->id,
            'question' => $request->question, // [FIX] Ganti 'question_text' jadi 'question'
            'question_type' => $request->question_type, 
            'points' => $request->points ?? 10,
        ];

        // --- Logic Simpan Jawaban (Sama seperti sebelumnya) ---
        
        if ($request->question_type === 'multiple_choice') {
            $request->validate([
                'options' => 'required|array|min:2',
                'correct_answer' => 'required|integer',
            ]);
            $questionData['options'] = json_encode($request->options);
            $questionData['correct_answer'] = $request->correct_answer;

        } elseif ($request->question_type === 'multiple_select') {
            $request->validate([
                'options' => 'required|array|min:2',
                'correct_answers' => 'required|array|min:1',
            ]);
            $questionData['options'] = json_encode($request->options);
            
            $answers = $request->correct_answers;
            sort($answers); 
            $questionData['correct_answer'] = json_encode($answers);

        } elseif ($request->question_type === 'true_false') {
            $request->validate(['correct_answer' => 'required|in:true,false']);
            $questionData['options'] = null;
            $questionData['correct_answer'] = $request->correct_answer;

        } elseif ($request->question_type === 'essay') {
            $questionData['options'] = null;
            $questionData['correct_answer'] = $request->answer_explanation;
        }

        \App\Models\QuizQuestion::create($questionData);

        return back()->with('success', 'Soal berhasil ditambahkan!');
    }

    /**
     * Update question (Instructor)
     */
    public function updateQuestion(Request $request, $courseId, $quizId, $questionId)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::findOrFail($courseId);
        $quiz = Quiz::findOrFail($quizId);
        $question = QuizQuestion::findOrFail($questionId);

        if ($course->instructor_id !== Auth::id() || $quiz->course_id !== $course->id || $question->quiz_id !== $quiz->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'question' => 'required|string',
            'question_type' => ['required', Rule::in(['multiple_choice', 'true_false', 'essay'])],
            'options' => 'nullable|array',
            'options.*' => 'nullable|string|max:255',
            'correct_answer' => 'nullable',
            'points' => 'required|integer|min:1|max:100',
            'order' => 'nullable|integer|min:1',
        ]);

        [$options, $correctAnswer] = $this->prepareQuestionPayload($validated);

        $question->update([
            'question' => $validated['question'],
            'question_type' => $validated['question_type'],
            'options' => $options,
            'correct_answer' => $correctAnswer,
            'points' => $validated['points'],
            'order' => $validated['order'] ?? $question->order,
        ]);

        return redirect()
            ->route('instructor.quiz.edit', [$courseId, $quizId])
            ->with('success', 'Soal berhasil diupdate!');
    }

    /**
     * Delete question (Instructor)
     */
    public function deleteQuestion($courseId, $quizId, $questionId)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::findOrFail($courseId);
        $quiz = Quiz::findOrFail($quizId);
        $question = QuizQuestion::findOrFail($questionId);

        if ($course->instructor_id !== Auth::id() || $quiz->course_id !== $course->id || $question->quiz_id !== $quiz->id) {
            abort(403, 'Unauthorized action.');
        }

        $question->delete();

        $question->delete();

        return redirect()
            ->route('instructor.quiz.edit', [$courseId, $quizId])
            ->with('success', 'Soal berhasil dihapus!');
    }

    /**
     * Normalize options & correct answers for multiple question types
     */
    private function prepareQuestionPayload(array $validated): array
    {
        $type = $validated['question_type'];
        $options = null;
        $correctAnswer = null;

        if ($type === 'multiple_choice') {
            $options = collect($validated['options'] ?? [])
                ->map(fn ($option) => $option !== null ? trim($option) : null)
                ->filter(fn ($option) => $option !== null && $option !== '')
                ->values()
                ->all();

            if (count($options) < 2) {
                throw ValidationException::withMessages([
                    'options' => 'Tambahkan minimal dua pilihan untuk soal pilihan ganda.',
                ]);
            }

            $selectedIndex = (int) ($validated['correct_answer'] ?? 0);

            if (!array_key_exists($selectedIndex, $options)) {
                throw ValidationException::withMessages([
                    'correct_answer' => 'Pilih jawaban benar yang valid.',
                ]);
            }

            $correctAnswer = $selectedIndex;
        } elseif ($type === 'true_false') {
            $value = $validated['correct_answer'];

            if (!in_array($value, ['true', 'false'], true)) {
                throw ValidationException::withMessages([
                    'correct_answer' => 'Pilih Benar atau Salah sebagai jawaban.',
                ]);
            }

            $correctAnswer = $value;
        }

        return [$options, $correctAnswer];
    }

    /**
     * View quiz submissions (Instructor)
     */
    public function submissions($courseId, $quizId)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::findOrFail($courseId);
        $quiz = Quiz::findOrFail($quizId);

        if ($course->instructor_id !== Auth::id() || $quiz->course_id !== $course->id) {
            abort(403, 'Unauthorized action.');
        }

        $submissions = $quiz->submissions()->with('user')->latest()->paginate(15);
        $stats = [
            'total_submissions' => $quiz->submissions()->count(),
            'average_score' => $this->gradingService->getAverageScore($quiz),
            'pass_rate' => $this->gradingService->getPassRate($quiz),
        ];

        return view('instructor.quiz.submissions', compact('course', 'quiz', 'submissions', 'stats'));
    }

    /**
     * View specific submission detail (Instructor)
     */
    public function submissionDetail($courseId, $quizId, $submissionId)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::findOrFail($courseId);
        $quiz = Quiz::findOrFail($quizId);
        $submission = QuizSubmission::findOrFail($submissionId);

        if ($course->instructor_id !== Auth::id() || $quiz->course_id !== $course->id || $submission->quiz_id !== $quiz->id) {
            abort(403, 'Unauthorized action.');
        }

        $submission->load('user', 'quiz.questions');

        return view('instructor.quiz.submission-detail', compact('course', 'quiz', 'submission'));
    }

    // ==================== STUDENT ROUTES ====================

    /**
     * Show available quizzes for student in a course
     */
    public function studentQuizzes($courseId)
    {
        $course = Course::findOrFail($courseId);

        // Check if student is enrolled
        $registration = Auth::user()->courseRegistrations()
            ->where('course_id', $courseId)
            ->where('status', 'paid')
            ->firstOrFail();

        $quizzes = $course->quizzes()
            ->where('is_published', true)
            ->with('submissions')
            ->get()
            ->map(function ($quiz) {
                $quiz->user_submission = $quiz->submissions()
                    ->where('user_id', Auth::id())
                    ->first();
                return $quiz;
            });

        return view('student.quiz.index', compact('course', 'quizzes', 'registration'));
    }

    /**
     * Start quiz (Student)
     */
    public function start($courseId, $quizId)
    {
        $course = Course::findOrFail($courseId);
        $quiz = Quiz::findOrFail($quizId);

        // Check if student is enrolled
        $registration = Auth::user()->courseRegistrations()
            ->where('course_id', $courseId)
            ->where('status', 'paid')
            ->firstOrFail();

        // Check if quiz is published and available
        if (!$quiz->is_published) {
            abort(403, 'Quiz tidak tersedia.');
        }

        if ($quiz->available_from && $quiz->available_from > now()) {
            abort(403, 'Quiz belum bisa diakses.');
        }

        if ($quiz->available_until && $quiz->available_until < now()) {
            abort(403, 'Quiz sudah ditutup.');
        }

        // Check if student already has ongoing submission
        $ongoingSubmission = $quiz->submissions()
            ->where('user_id', Auth::id())
            ->whereNull('submitted_at')
            ->first();

        if ($ongoingSubmission) {
            return redirect()->route('student.quiz.continue', [$courseId, $quizId, $ongoingSubmission->id]);
        }

        // Create new submission
        $submission = $quiz->submissions()->create([
            'user_id' => Auth::id(),
            'started_at' => now(),
            'answers' => []
        ]);

        return redirect()->route('student.quiz.continue', [$courseId, $quizId, $submission->id]);
    }

    /**
     * Continue/Take quiz (Student)
     */
    public function continue($courseId, $quizId, $submissionId)
    {
        $course = Course::findOrFail($courseId);
        $quiz = Quiz::findOrFail($quizId);
        $submission = QuizSubmission::findOrFail($submissionId);

        // Check if student owns this submission
        if ($submission->user_id !== Auth::id() || $submission->quiz_id !== $quiz->id) {
            abort(403, 'Unauthorized access.');
        }

        // Check if already submitted
        if ($submission->submitted_at) {
            return redirect()->route('student.quiz.result', [$courseId, $quizId, $submissionId]);
        }

        $questions = $quiz->questions()->orderBy('order')->get();
        $timeRemaining = $this->getTimeRemaining($submission, $quiz);

        return view('student.quiz.take', compact('course', 'quiz', 'submission', 'questions', 'timeRemaining'));
    }

    /**
     * Submit quiz (Student)
     */
    public function submit(Request $request, $courseId, $quizId, $submissionId)
    {
        $quiz = Quiz::where('course_id', $courseId)->findOrFail($quizId);
        
        // Cari Submission
        $submission = \App\Models\QuizSubmission::where('quiz_id', $quizId)
            ->where('user_id', Auth::id())
            ->where('id', $submissionId)
            ->firstOrFail();

        // [FIX 1] Tambahkan $courseId di sini
        if ($submission->status === 'submitted') {
            return redirect()->route('student.quiz.result', [$courseId, $quizId, $submissionId]);
        }

        // Simpan Jawaban
        $answers = $request->input('answers', []);
        $submission->answers = $answers;
        $submission->submitted_at = now();
        $submission->status = 'submitted'; 
        $submission->save();

        // Logic Grading (Hitung Nilai)
        $score = 0;
        $totalPoints = $quiz->questions->sum('points');
        $earnedPoints = 0;
        $correctCount = 0;

        foreach ($quiz->questions as $question) {
            $userAns = $answers[$question->id] ?? null;
            
            if (!$userAns) continue;

            $isCorrect = false;
            
            if ($question->question_type == 'multiple_choice' || $question->question_type == 'true_false') {
                if ((string)$userAns === (string)$question->correct_answer) $isCorrect = true;
            } elseif ($question->question_type == 'multiple_select') {
                $keyArr = json_decode($question->correct_answer, true) ?? [];
                $ansArr = is_array($userAns) ? $userAns : [];
                sort($keyArr); sort($ansArr);
                if ($keyArr == $ansArr) $isCorrect = true;
            }
            
            if ($isCorrect) {
                $earnedPoints += $question->points;
                $correctCount++;
            }
        }
        
        // Hitung Skor (0-100)
        if ($totalPoints > 0) {
            $score = round(($earnedPoints / $totalPoints) * 100);
        }
        
        $submission->score = $score;
        $submission->correct_answers_count = $correctCount;
        $submission->save();

        // Update Progress di CourseProgress (Tandai Selesai)
        \App\Models\CourseProgress::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'course_id' => $courseId,
                'content_id' => $quizId,
                'content_type' => 'quiz',
            ],
            [
                'course_module_id' => $quiz->course_module_id,
                'is_completed' => true,
                'completed_at' => now(),
            ]
        );

        // [FIX 2] Tambahkan $courseId di sini juga
        return redirect()->route('student.quiz.result', [$courseId, $quizId, $submissionId])
            ->with('success', 'Jawaban berhasil dikirim!');
    }

    /**
     * View quiz result (Student)
     */
    public function result($courseId, $quizId, $submissionId)
    {
        $course = Course::findOrFail($courseId);
        $quiz = Quiz::findOrFail($quizId);
        $submission = QuizSubmission::findOrFail($submissionId);

        // Check if student owns this submission
        if ($submission->user_id !== Auth::id() || $submission->quiz_id !== $quiz->id) {
            abort(403, 'Unauthorized access.');
        }

        // Check if submitted
        if (!$submission->submitted_at) {
            abort(403, 'Quiz belum disubmit.');
        }

        $submission->load('quiz.questions');

        return view('student.quiz.result', compact('course', 'quiz', 'submission'));
    }

    // ==================== HELPER METHODS ====================

    /**
     * Calculate time remaining for quiz
     */
    private function getTimeRemaining(QuizSubmission $submission, Quiz $quiz): int
    {
        $startTime = $submission->started_at;
        $durationMinutes = $quiz->duration_minutes;
        $endTime = $startTime->addMinutes($durationMinutes);

        $remainingMinutes = now()->diffInMinutes($endTime);
        
        return max($remainingMinutes * 60, 0); // Return in seconds
    }
}
