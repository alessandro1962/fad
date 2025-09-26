<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Attempt;
use App\Models\Quiz;
use App\Models\Lesson;
use App\Models\Progress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    /**
     * Get quiz for a specific lesson.
     */
    public function getQuizByLesson(Request $request, Lesson $lesson): JsonResponse
    {
        $user = $request->user();
        
        \Log::info('QuizController: getQuizByLesson - user check', [
            'user' => $user ? $user->toArray() : 'null',
            'authenticated' => $user ? 'yes' : 'no',
            'lesson_id' => $lesson->id
        ]);
        
        if (!$user) {
            return response()->json([
                'message' => 'Utente non autenticato.',
            ], 401);
        }
        
        // Check if user is enrolled in the course
        $course = $lesson->module->course;
        $enrollment = $user->enrollments()
            ->where('course_id', $course->id)
            ->whereIn('status', ['active', 'completed'])
            ->first();

        \Log::info('QuizController: getQuizByLesson - enrollment check', [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
            'enrollment' => $enrollment ? $enrollment->toArray() : null
        ]);

        if (!$enrollment) {
            return response()->json([
                'message' => 'Non sei iscritto a questo corso.',
            ], 403);
        }

        // Get the quiz for this lesson
        $quiz = $lesson->quiz;
        
        if (!$quiz) {
            return response()->json([
                'message' => 'Quiz non trovato per questa lezione.',
            ], 404);
        }

        // Load quiz with questions
        $quiz->load(['questions' => function ($query) {
            $query->where('is_active', true)->orderBy('order');
        }]);

        return response()->json([
            'data' => [
                'id' => $quiz->id,
                'quiz_title' => $quiz->title,
                'description' => $quiz->description,
                'passing_score' => $quiz->passing_score,
                'max_attempts' => $quiz->max_attempts,
                'time_limit_minutes' => $quiz->time_limit_minutes,
                'questions' => $quiz->questions->map(function ($question) {
                    return [
                        'id' => $question->id,
                        'text' => $question->text,
                        'type' => $question->type,
                        'options' => $question->options,
                        'score' => $question->score,
                        'explanation' => $question->explanation,
                    ];
                }),
            ]
        ]);
    }

    /**
     * Get quiz details for a lesson.
     */
    public function show(Request $request, Quiz $quiz): JsonResponse
    {
        $user = $request->user();
        
        // Check if user is enrolled in the course
        $course = $quiz->lesson->module->course;
        $enrollment = $user->enrollments()
            ->where('course_id', $course->id)
            ->whereIn('status', ['active', 'completed'])
            ->first();

        if (!$enrollment) {
            return response()->json([
                'message' => 'Non sei iscritto a questo corso.',
            ], 403);
        }

        $questions = $quiz->questions()
            ->active()
            ->orderBy('order')
            ->get()
            ->map(function ($question) {
                return [
                    'id' => $question->id,
                    'text' => $question->text,
                    'type' => $question->type,
                    'options' => $question->options,
                    'score' => $question->score,
                    'order' => $question->order,
                ];
            });

        // Get user's attempts
        $attempts = $user->attempts()
            ->where('quiz_id', $quiz->id)
            ->orderBy('attempt_number', 'desc')
            ->get()
            ->map(function ($attempt) {
                return [
                    'id' => $attempt->id,
                    'attempt_number' => $attempt->attempt_number,
                    'score' => $attempt->score,
                    'max_score' => $attempt->max_score,
                    'percentage' => $attempt->percentage,
                    'passed' => $attempt->passed,
                    'started_at' => $attempt->started_at?->toISOString(),
                    'finished_at' => $attempt->finished_at?->toISOString(),
                    'duration' => $attempt->duration,
                ];
            });

        $canTakeQuiz = $attempts->count() < $quiz->max_attempts;
        $bestAttempt = $attempts->where('passed', true)->first();

        return response()->json([
            'data' => [
                'id' => $quiz->id,
                'title' => $quiz->title,
                'description' => $quiz->description,
                'passing_score' => $quiz->passing_score,
                'max_attempts' => $quiz->max_attempts,
                'time_limit_minutes' => $quiz->time_limit_minutes,
                'total_questions' => $questions->count(),
                'total_score' => $quiz->total_score,
                'can_take_quiz' => $canTakeQuiz,
                'has_passed' => $bestAttempt !== null,
                'best_attempt' => $bestAttempt,
                'attempts' => $attempts,
                'questions' => $questions,
            ],
        ]);
    }

    /**
     * Start a new quiz attempt.
     */
    public function start(Request $request, Quiz $quiz): JsonResponse
    {
        $user = $request->user();
        
        \Log::info('QuizController: start quiz - user check', [
            'user' => $user ? $user->toArray() : 'null',
            'authenticated' => $user ? 'yes' : 'no'
        ]);
        
        if (!$user) {
            return response()->json([
                'message' => 'Utente non autenticato.',
            ], 401);
        }
        
        // Check if user is enrolled in the course
        $course = $quiz->lesson->module->course;
        $enrollment = $user->enrollments()
            ->where('course_id', $course->id)
            ->whereIn('status', ['active', 'completed'])
            ->first();

        \Log::info('QuizController: start quiz check', [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'quiz_id' => $quiz->id,
            'enrollment' => $enrollment ? $enrollment->toArray() : null,
            'all_enrollments' => $user->enrollments()->get()->toArray()
        ]);

        if (!$enrollment) {
            return response()->json([
                'message' => 'Non sei iscritto a questo corso.',
            ], 403);
        }

        // Check if user can take the quiz
        $attemptCount = $user->attempts()
            ->where('quiz_id', $quiz->id)
            ->count();

        \Log::info('QuizController: start quiz - attempt check', [
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'attempt_count' => $attemptCount,
            'max_attempts' => $quiz->max_attempts,
            'enrollment_status' => $enrollment->status,
            'unlimited_attempts' => true
        ]);

        // Allow unlimited attempts for all users
        // No limit on quiz attempts

        // Check if user has a pending attempt
        $pendingAttempt = $user->attempts()
            ->where('quiz_id', $quiz->id)
            ->whereNull('finished_at')
            ->first();

        if ($pendingAttempt) {
            return response()->json([
                'message' => 'Hai già un tentativo in corso.',
                'data' => [
                    'attempt_id' => $pendingAttempt->id,
                    'started_at' => $pendingAttempt->started_at->toISOString(),
                ],
            ], 409);
        }

        // Create new attempt
        $attempt = Attempt::create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'attempt_number' => $attemptCount + 1,
            'started_at' => now(),
        ]);

        // Get questions for the attempt
        $questions = $quiz->questions()
            ->active()
            ->orderBy('order')
            ->get()
            ->map(function ($question) {
                return [
                    'id' => $question->id,
                    'text' => $question->text,
                    'type' => $question->type,
                    'options' => $question->options,
                    'score' => $question->score,
                    'order' => $question->order,
                ];
            });

        return response()->json([
            'message' => 'Quiz avviato con successo.',
            'data' => [
                'attempt_id' => $attempt->id,
                'quiz_id' => $quiz->id,
                'attempt_number' => $attempt->attempt_number,
                'time_limit_minutes' => $quiz->time_limit_minutes,
                'total_questions' => $questions->count(),
                'started_at' => $attempt->started_at->toISOString(),
                'questions' => $questions,
            ],
        ]);
    }

    /**
     * Submit quiz answers.
     */
    public function submit(Request $request, Quiz $quiz): JsonResponse
    {
        $request->validate([
            'attempt_id' => ['required', 'integer'],
            'answers' => ['required', 'array'],
            'answers.*.question_id' => ['required', 'integer'],
            'answers.*.answer' => ['required'],
        ]);

        $user = $request->user();
        
        // Find the attempt
        $attempt = $user->attempts()
            ->where('id', $request->attempt_id)
            ->where('quiz_id', $quiz->id)
            ->whereNull('finished_at')
            ->first();

        if (!$attempt) {
            return response()->json([
                'message' => 'Tentativo non trovato o già completato.',
            ], 404);
        }

        // Calculate score
        $totalScore = 0;
        $maxScore = 0;
        $answers = [];

        foreach ($request->answers as $answerData) {
            $question = $quiz->questions()
                ->where('id', $answerData['question_id'])
                ->first();

            if (!$question) {
                continue;
            }

            $maxScore += $question->score;
            $isCorrect = $question->isCorrectAnswer($answerData['answer']);
            
            if ($isCorrect) {
                $totalScore += $question->score;
            }

            $answers[] = [
                'question_id' => $question->id,
                'answer' => $answerData['answer'],
                'is_correct' => $isCorrect,
                'score' => $isCorrect ? $question->score : 0,
            ];
        }

        $percentage = $maxScore > 0 ? round(($totalScore / $maxScore) * 100) : 0;
        $passed = $percentage >= $quiz->passing_score;

        // Update attempt
        $attempt->update([
            'score' => $totalScore,
            'max_score' => $maxScore,
            'passed' => $passed,
            'finished_at' => now(),
            'answers' => $answers,
        ]);

        // Update lesson progress if quiz is passed
        if ($passed) {
            $lesson = $quiz->lesson;
            $course = $lesson->module->course;
            
            // Check if user is enrolled in the course
            $enrollment = $user->enrollments()
                ->where('course_id', $course->id)
                ->whereIn('status', ['active', 'completed'])
                ->first();

            if ($enrollment) {
                // Update lesson progress
                $progress = $user->progress()
                    ->where('lesson_id', $lesson->id)
                    ->first();

                if ($progress) {
                    $progress->update([
                        'completed' => true,
                        'completed_at' => now(),
                    ]);
                } else {
                    $user->progress()->create([
                        'lesson_id' => $lesson->id,
                        'completed' => true,
                        'completed_at' => now(),
                    ]);
                }

                // Update enrollment progress
                $this->updateEnrollmentProgress($enrollment);
            }
        }

        return response()->json([
            'message' => 'Quiz completato con successo.',
            'data' => [
                'attempt_id' => $attempt->id,
                'score' => $totalScore,
                'max_score' => $maxScore,
                'percentage' => $percentage,
                'passed' => $passed,
                'passing_score' => $quiz->passing_score,
                'finished_at' => $attempt->finished_at->toISOString(),
                'answers' => $answers,
            ],
        ]);
    }

    /**
     * Get quiz results for a specific attempt.
     */
    public function results(Request $request, Quiz $quiz, Attempt $attempt): JsonResponse
    {
        $user = $request->user();
        
        // Verify the attempt belongs to the user
        if ($attempt->user_id !== $user->id || $attempt->quiz_id !== $quiz->id) {
            return response()->json([
                'message' => 'Tentativo non trovato.',
            ], 404);
        }

        $questions = $quiz->questions()
            ->active()
            ->orderBy('order')
            ->get();

        $results = [];
        foreach ($questions as $question) {
            $userAnswer = collect($attempt->answers)
                ->where('question_id', $question->id)
                ->first();

            $results[] = [
                'question_id' => $question->id,
                'question_text' => $question->text,
                'question_type' => $question->type,
                'correct_answer' => $question->correct_answer,
                'user_answer' => $userAnswer['answer'] ?? null,
                'is_correct' => $userAnswer['is_correct'] ?? false,
                'score' => $userAnswer['score'] ?? 0,
                'max_score' => $question->score,
                'explanation' => $question->explanation,
            ];
        }

        return response()->json([
            'data' => [
                'attempt_id' => $attempt->id,
                'quiz_title' => $quiz->title,
                'score' => $attempt->score,
                'max_score' => $attempt->max_score,
                'percentage' => $attempt->percentage,
                'passed' => $attempt->passed,
                'passing_score' => $quiz->passing_score,
                'started_at' => $attempt->started_at->toISOString(),
                'finished_at' => $attempt->finished_at->toISOString(),
                'duration' => $attempt->duration,
                'results' => $results,
            ],
        ]);
    }

    /**
     * Get user's quiz attempts for a specific lesson.
     */
    public function getAttemptsByLesson(Request $request): JsonResponse
    {
        $request->validate([
            'lesson_id' => ['required', 'integer', 'exists:lessons,id'],
        ]);

        $user = $request->user();
        $lesson = Lesson::findOrFail($request->lesson_id);
        
        // Check if user is enrolled in the course
        $course = $lesson->module->course;
        $enrollment = $user->enrollments()
            ->where('course_id', $course->id)
            ->whereIn('status', ['active', 'completed'])
            ->first();

        if (!$enrollment) {
            return response()->json([
                'message' => 'Non sei iscritto a questo corso.',
            ], 403);
        }

        $quiz = $lesson->quiz;
        if (!$quiz) {
            return response()->json([
                'data' => []
            ]);
        }

        $attempts = $user->attempts()
            ->where('quiz_id', $quiz->id)
            ->orderBy('attempt_number', 'desc')
            ->get()
            ->map(function ($attempt) {
                return [
                    'id' => $attempt->id,
                    'attempt_number' => $attempt->attempt_number,
                    'score' => $attempt->score,
                    'max_score' => $attempt->max_score,
                    'percentage' => $attempt->percentage,
                    'passed' => $attempt->passed,
                    'started_at' => $attempt->started_at?->toISOString(),
                    'finished_at' => $attempt->finished_at?->toISOString(),
                    'duration' => $attempt->duration,
                ];
            });

        return response()->json([
            'data' => $attempts
        ]);
    }

    /**
     * Get user's quiz history for a specific quiz.
     */
    public function history(Request $request, Quiz $quiz): JsonResponse
    {
        $user = $request->user();
        
        $attempts = $user->attempts()
            ->where('quiz_id', $quiz->id)
            ->orderBy('attempt_number', 'desc')
            ->get()
            ->map(function ($attempt) {
                return [
                    'id' => $attempt->id,
                    'attempt_number' => $attempt->attempt_number,
                    'score' => $attempt->score,
                    'max_score' => $attempt->max_score,
                    'percentage' => $attempt->percentage,
                    'passed' => $attempt->passed,
                    'started_at' => $attempt->started_at?->toISOString(),
                    'finished_at' => $attempt->finished_at?->toISOString(),
                    'duration' => $attempt->duration,
                ];
            });

        return response()->json([
            'data' => [
                'quiz_id' => $quiz->id,
                'quiz_title' => $quiz->title,
                'max_attempts' => $quiz->max_attempts,
                'passing_score' => $quiz->passing_score,
                'total_attempts' => $attempts->count(),
                'best_score' => $attempts->max('percentage'),
                'has_passed' => $attempts->where('passed', true)->isNotEmpty(),
                'attempts' => $attempts,
            ],
        ]);
    }

    /**
     * Update enrollment progress based on completed lessons.
     */
    private function updateEnrollmentProgress($enrollment)
    {
        $user = $enrollment->user;
        $course = $enrollment->course;
        
        $totalLessons = $course->modules()
            ->withCount('lessons')
            ->get()
            ->sum('lessons_count');

        if ($totalLessons === 0) {
            \Log::info('QuizController: Nessuna lezione trovata per il corso', ['course_id' => $course->id]);
            return;
        }

        $completedLessons = $user->progress()
            ->whereHas('lesson.module', function ($query) use ($course) {
                $query->where('course_id', $course->id);
            })
            ->completed()
            ->count();

        $progressPercentage = round(($completedLessons / $totalLessons) * 100);
        $wasCompleted = $enrollment->status === 'completed';
        
        \Log::info('QuizController: Aggiornamento progresso enrollment', [
            'course_id' => $course->id,
            'user_id' => $user->id,
            'total_lessons' => $totalLessons,
            'completed_lessons' => $completedLessons,
            'progress_percentage' => $progressPercentage,
            'was_completed' => $wasCompleted
        ]);
        
        $enrollment->update([
            'progress_percentage' => $progressPercentage,
            'status' => $progressPercentage >= 100 ? 'completed' : 'active',
            'completed_at' => $progressPercentage >= 100 ? now() : null,
        ]);

        // Generate certificate if course was just completed
        if ($progressPercentage >= 100 && !$wasCompleted) {
            \Log::info('QuizController: Corso completato, generazione certificato', ['course_id' => $course->id]);
            $this->generateCertificate($user, $course);
        }
    }

    /**
     * Generate certificate for completed course.
     */
    private function generateCertificate($user, $course)
    {
        try {
            $certificateService = app(\App\Services\CertificateService::class);
            
            // Check if certificate already exists
            $existingCertificate = $user->certificates()
                ->where('kind', 'course')
                ->where('ref_id', $course->id)
                ->first();

            if (!$existingCertificate) {
                $certificate = $certificateService->generateCertificate($user, 'course', $course->id);
                \Log::info("Certificate generated for user {$user->id} and course {$course->id}", [
                    'certificate_id' => $certificate->id,
                    'public_uid' => $certificate->public_uid
                ]);
            }
        } catch (\Exception $e) {
            \Log::error("Failed to generate certificate for user {$user->id} and course {$course->id}: " . $e->getMessage());
        }
    }
}
