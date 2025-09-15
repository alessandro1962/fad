<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Progress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    /**
     * Get user's progress for a specific lesson.
     */
    public function show(Request $request, Lesson $lesson): JsonResponse
    {
        $user = $request->user();
        
        $progress = $user->progress()
            ->where('lesson_id', $lesson->id)
            ->first();

        if (!$progress) {
            return response()->json([
                'data' => [
                    'lesson_id' => $lesson->id,
                    'seconds_watched' => 0,
                    'completed' => false,
                    'last_position_sec' => 0,
                    'progress_percentage' => 0,
                ],
            ]);
        }

        return response()->json([
            'data' => [
                'lesson_id' => $progress->lesson_id,
                'seconds_watched' => $progress->seconds_watched,
                'completed' => $progress->completed,
                'last_position_sec' => $progress->last_position_sec,
                'progress_percentage' => $progress->progress_percentage,
                'last_seen_at' => $progress->last_seen_at?->toISOString(),
                'completed_at' => $progress->completed_at?->toISOString(),
            ],
        ]);
    }

    /**
     * Update lesson progress.
     */
    public function update(Request $request, Lesson $lesson): JsonResponse
    {
        $request->validate([
            'seconds_watched' => ['required', 'integer', 'min:0'],
            'last_position_sec' => ['nullable', 'integer', 'min:0'],
            'completed' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();
        
        // Check if user is enrolled in the course
        $course = $lesson->module->course;
        $enrollment = $user->enrollments()
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            return response()->json([
                'message' => 'Non sei iscritto a questo corso.',
            ], 403);
        }

        $progress = $user->progress()
            ->where('lesson_id', $lesson->id)
            ->first();

        $data = [
            'seconds_watched' => $request->seconds_watched,
            'last_position_sec' => $request->last_position_sec ?? 0,
            'last_seen_at' => now(),
        ];

        // Mark as completed if specified or if watched enough
        $isCompleted = $request->completed ?? false;
        $lessonDuration = $lesson->duration_minutes * 60;
        
        if (!$isCompleted && $request->seconds_watched >= ($lessonDuration * 0.9)) {
            $isCompleted = true;
        }

        if ($isCompleted && (!$progress || !$progress->completed)) {
            $data['completed'] = true;
            $data['completed_at'] = now();
        }

        if ($progress) {
            $progress->update($data);
        } else {
            $data['user_id'] = $user->id;
            $data['lesson_id'] = $lesson->id;
            $progress = Progress::create($data);
        }

        // Update enrollment progress
        $this->updateEnrollmentProgress($enrollment);

        return response()->json([
            'message' => 'Progresso aggiornato con successo.',
            'data' => [
                'lesson_id' => $progress->lesson_id,
                'seconds_watched' => $progress->seconds_watched,
                'completed' => $progress->completed,
                'last_position_sec' => $progress->last_position_sec,
                'progress_percentage' => $progress->progress_percentage,
                'completed_at' => $progress->completed_at?->toISOString(),
            ],
        ]);
    }

    /**
     * Get user's progress for a course.
     */
    public function courseProgress(Request $request, $courseId): JsonResponse
    {
        $user = $request->user();
        
        $enrollment = $user->enrollments()
            ->where('course_id', $courseId)
            ->first();

        if (!$enrollment) {
            return response()->json([
                'message' => 'Non sei iscritto a questo corso.',
            ], 403);
        }

        $course = $enrollment->course;
        $lessons = $course->modules()
            ->with('lessons')
            ->get()
            ->pluck('lessons')
            ->flatten();

        $progressData = [];
        $totalLessons = $lessons->count();
        $completedLessons = 0;
        $totalSecondsWatched = 0;

        foreach ($lessons as $lesson) {
            $progress = $user->progress()
                ->where('lesson_id', $lesson->id)
                ->first();

            $lessonProgress = [
                'lesson_id' => $lesson->id,
                'lesson_title' => $lesson->title,
                'lesson_type' => $lesson->type,
                'duration_minutes' => $lesson->duration_minutes,
                'seconds_watched' => $progress?->seconds_watched ?? 0,
                'completed' => $progress?->completed ?? false,
                'progress_percentage' => $progress?->progress_percentage ?? 0,
                'last_seen_at' => $progress?->last_seen_at?->toISOString(),
            ];

            if ($lessonProgress['completed']) {
                $completedLessons++;
            }
            
            $totalSecondsWatched += $lessonProgress['seconds_watched'];
            $progressData[] = $lessonProgress;
        }

        $courseProgressPercentage = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

        return response()->json([
            'data' => [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'total_lessons' => $totalLessons,
                'completed_lessons' => $completedLessons,
                'course_progress_percentage' => $courseProgressPercentage,
                'total_time_watched_minutes' => round($totalSecondsWatched / 60),
                'enrollment_progress' => $enrollment->progress_percentage,
                'lessons' => $progressData,
            ],
        ]);
    }

    /**
     * Get user's overall learning statistics.
     */
    public function statistics(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $totalProgress = $user->progress()->count();
        $completedProgress = $user->progress()->completed()->count();
        $totalSecondsWatched = $user->progress()->sum('seconds_watched');
        
        $activeEnrollments = $user->enrollments()->active()->count();
        $completedEnrollments = $user->enrollments()->completed()->count();
        
        $totalCertificates = $user->certificates()->count();
        $totalBadges = $user->badges()->count();

        return response()->json([
            'data' => [
                'total_lessons_started' => $totalProgress,
                'total_lessons_completed' => $completedProgress,
                'completion_rate' => $totalProgress > 0 ? round(($completedProgress / $totalProgress) * 100) : 0,
                'total_time_watched_hours' => round($totalSecondsWatched / 3600, 1),
                'active_courses' => $activeEnrollments,
                'completed_courses' => $completedEnrollments,
                'total_certificates' => $totalCertificates,
                'total_badges' => $totalBadges,
                'learning_level' => $user->learning_level,
                'total_learning_hours' => $user->total_learning_hours,
            ],
        ]);
    }

    /**
     * Update enrollment progress based on lesson completions.
     */
    private function updateEnrollmentProgress($enrollment): void
    {
        $course = $enrollment->course;
        $user = $enrollment->user;
        
        $totalLessons = $course->modules()
            ->with('lessons')
            ->get()
            ->pluck('lessons')
            ->flatten()
            ->count();

        if ($totalLessons === 0) {
            return;
        }

        $completedLessons = $user->progress()
            ->whereHas('lesson.module', function ($query) use ($course) {
                $query->where('course_id', $course->id);
            })
            ->completed()
            ->count();

        $progressPercentage = round(($completedLessons / $totalLessons) * 100);
        
        $enrollment->update([
            'progress_percentage' => $progressPercentage,
            'status' => $progressPercentage >= 100 ? 'completed' : 'active',
            'completed_at' => $progressPercentage >= 100 ? now() : null,
        ]);
    }
}
