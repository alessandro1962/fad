<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class LessonController extends Controller
{
    /**
     * Display a listing of lessons for a module.
     */
    public function index(Module $module): JsonResponse
    {
        $lessons = $module->lessons()
            ->with('quiz.questions')
            ->orderBy('order')
            ->get();

        return response()->json([
            'data' => $lessons
        ]);
    }

    /**
     * Store a newly created lesson.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'module_id' => 'required|exists:modules,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:video,pdf,quiz,slide,link',
            'duration_minutes' => 'required|integer|min:1',
            'order' => 'nullable|integer|min:1',
            'payload' => 'nullable|array',
            'is_active' => 'nullable|boolean',
        ]);

        // Set order if not provided
        if (!isset($validated['order'])) {
            $module = Module::find($validated['module_id']);
            $validated['order'] = $module->lessons()->max('order') + 1;
        }

        $validated['is_active'] = $validated['is_active'] ?? true;

        DB::beginTransaction();
        try {
            $lesson = Lesson::create($validated);

            // Create quiz if lesson type is quiz
            if ($validated['type'] === 'quiz' && isset($validated['payload']['questions'])) {
                $this->createQuizForLesson($lesson, $validated['payload']);
            }

            DB::commit();

            return response()->json([
                'message' => 'Lezione creata con successo.',
                'data' => $lesson->load(['module', 'quiz.questions'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Errore nella creazione della lezione.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified lesson.
     */
    public function show(Lesson $lesson): JsonResponse
    {
        $lesson->load(['module.course', 'quiz.questions']);

        return response()->json([
            'data' => $lesson
        ]);
    }

    /**
     * Update the specified lesson.
     */
    public function update(Request $request, Lesson $lesson): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:video,pdf,quiz,slide,link',
            'duration_minutes' => 'required|integer|min:1',
            'order' => 'nullable|integer|min:1',
            'payload' => 'nullable|array',
            'is_active' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $lesson->update($validated);

            // Update quiz if lesson type is quiz
            if ($validated['type'] === 'quiz' && isset($validated['payload']['questions'])) {
                $this->updateQuizForLesson($lesson, $validated['payload']);
            } elseif ($validated['type'] !== 'quiz' && $lesson->quiz) {
                // Delete quiz if lesson type changed from quiz
                $lesson->quiz->questions()->delete();
                $lesson->quiz->delete();
            }

            DB::commit();

            return response()->json([
                'message' => 'Lezione aggiornata con successo.',
                'data' => $lesson->load(['module', 'quiz.questions'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Errore nell\'aggiornamento della lezione.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified lesson.
     */
    public function destroy(Lesson $lesson): JsonResponse
    {
        DB::beginTransaction();
        try {
            // Delete quiz and questions if exists
            if ($lesson->quiz) {
                $lesson->quiz->questions()->delete();
                $lesson->quiz->delete();
            }

            $lesson->delete();

            DB::commit();

            return response()->json([
                'message' => 'Lezione eliminata con successo.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Errore nell\'eliminazione della lezione.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reorder lessons.
     */
    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lessons' => 'required|array',
            'lessons.*.id' => 'required|exists:lessons,id',
            'lessons.*.order' => 'required|integer|min:1',
        ]);

        foreach ($validated['lessons'] as $lessonData) {
            Lesson::where('id', $lessonData['id'])
                ->update(['order' => $lessonData['order']]);
        }

        return response()->json([
            'message' => 'Ordine lezioni aggiornato con successo.'
        ]);
    }

    /**
     * Create quiz for lesson.
     */
    private function createQuizForLesson(Lesson $lesson, array $payload): void
    {
        $quiz = Quiz::create([
            'lesson_id' => $lesson->id,
            'title' => $payload['quiz_title'] ?? 'Quiz',
            'description' => $payload['quiz_description'] ?? '',
            'settings' => [
                'passing_score' => $payload['passing_score'] ?? 70,
                'max_attempts' => $payload['max_attempts'] ?? 3,
                'time_limit' => $payload['time_limit'] ?? 0,
                'block_progression' => $payload['block_progression'] ?? true,
            ],
            'passing_score' => $payload['passing_score'] ?? 70,
            'max_attempts' => $payload['max_attempts'] ?? 3,
            'time_limit_minutes' => $payload['time_limit'] ?? 0,
            'is_active' => true,
        ]);

        // Create questions
        if (isset($payload['questions']) && is_array($payload['questions'])) {
            foreach ($payload['questions'] as $index => $questionData) {
                Question::create([
                    'quiz_id' => $quiz->id,
                    'text' => $questionData['text'],
                    'type' => $questionData['type'],
                    'options' => $questionData['options'] ?? [],
                    'correct_answer' => $questionData['correct_answer'] ?? [],
                    'score' => $questionData['score'] ?? 1,
                    'explanation' => $questionData['explanation'] ?? '',
                    'order' => $index + 1,
                    'is_active' => true,
                ]);
            }
        }
    }

    /**
     * Update quiz for lesson.
     */
    private function updateQuizForLesson(Lesson $lesson, array $payload): void
    {
        $quiz = $lesson->quiz;

        if (!$quiz) {
            $this->createQuizForLesson($lesson, $payload);
            return;
        }

        // Update quiz settings
        $quiz->update([
            'title' => $payload['quiz_title'] ?? $quiz->title,
            'description' => $payload['quiz_description'] ?? $quiz->description,
            'settings' => [
                'passing_score' => $payload['passing_score'] ?? 70,
                'max_attempts' => $payload['max_attempts'] ?? 3,
                'time_limit' => $payload['time_limit'] ?? 0,
                'block_progression' => $payload['block_progression'] ?? true,
            ],
            'passing_score' => $payload['passing_score'] ?? 70,
            'max_attempts' => $payload['max_attempts'] ?? 3,
            'time_limit_minutes' => $payload['time_limit'] ?? 0,
        ]);

        // Delete existing questions
        $quiz->questions()->delete();

        // Create new questions
        if (isset($payload['questions']) && is_array($payload['questions'])) {
            foreach ($payload['questions'] as $index => $questionData) {
                Question::create([
                    'quiz_id' => $quiz->id,
                    'text' => $questionData['text'],
                    'type' => $questionData['type'],
                    'options' => $questionData['options'] ?? [],
                    'correct_answer' => $questionData['correct_answer'] ?? [],
                    'score' => $questionData['score'] ?? 1,
                    'explanation' => $questionData['explanation'] ?? '',
                    'order' => $index + 1,
                    'is_active' => true,
                ]);
            }
        }
    }
}
