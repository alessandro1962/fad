<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Services\CertificateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    /**
     * Get user's enrollments.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $enrollments = $user->enrollments()
            ->with(['course.modules', 'course.enrollments'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $enrollments,
        ]);
    }

    /**
     * Enroll user in a course.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'source' => ['nullable', 'in:purchase,assign,full_vision,free'],
        ]);

        $user = $request->user();
        $course = Course::findOrFail($request->course_id);

        // Check if user is already enrolled
        if ($user->enrollments()->where('course_id', $course->id)->exists()) {
            return response()->json([
                'message' => 'Sei già iscritto a questo corso.',
            ], 400);
        }

        // Check if user has full vision access
        $hasFullVision = $user->hasFullVisionAccess();
        $source = $request->source ?? ($hasFullVision ? 'full_vision' : 'free');

        $enrollment = $user->enrollments()->create([
            'course_id' => $course->id,
            'source' => $source,
            'status' => 'active',
            'started_at' => now(),
        ]);

        return response()->json([
            'message' => 'Iscrizione al corso completata con successo.',
            'data' => $enrollment->load('course'),
        ], 201);
    }

    /**
     * Get enrollment details.
     */
    public function show(Request $request, Enrollment $enrollment): JsonResponse
    {
        // Check if enrollment belongs to user
        if ($enrollment->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Accesso negato.',
            ], 403);
        }

        $enrollment->load(['course.modules.lessons', 'progress']);

        return response()->json([
            'data' => $enrollment,
        ]);
    }

    /**
     * Update enrollment progress.
     */
    public function updateProgress(Request $request, Enrollment $enrollment): JsonResponse
    {
        // Check if enrollment belongs to user
        if ($enrollment->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Accesso negato.',
            ], 403);
        }

        $request->validate([
            'progress_percentage' => ['required', 'integer', 'min:0', 'max:100'],
            'time_spent_minutes' => ['nullable', 'integer', 'min:0'],
        ]);

        $enrollment->update([
            'progress_percentage' => $request->progress_percentage,
            'time_spent_minutes' => $request->time_spent_minutes ?? $enrollment->time_spent_minutes,
        ]);

        // Mark as completed if 100%
        if ($request->progress_percentage >= 100 && $enrollment->status !== 'completed') {
            $enrollment->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
            
            // Generate certificate for completed course
            $this->generateCertificate($enrollment->user, $enrollment->course);
        }

        return response()->json([
            'message' => 'Progresso aggiornato con successo.',
            'data' => $enrollment,
        ]);
    }

    /**
     * Get active enrollments.
     */
    public function active(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $enrollments = $user->enrollments()
            ->active()
            ->with(['course.modules'])
            ->orderBy('started_at', 'desc')
            ->get();

        return response()->json([
            'data' => $enrollments,
        ]);
    }

    /**
     * Get completed enrollments.
     */
    public function completed(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $enrollments = $user->enrollments()
            ->completed()
            ->with(['course'])
            ->orderBy('completed_at', 'desc')
            ->paginate(12);

        return response()->json([
            'data' => $enrollments->items(),
            'meta' => [
                'current_page' => $enrollments->currentPage(),
                'last_page' => $enrollments->lastPage(),
                'per_page' => $enrollments->perPage(),
                'total' => $enrollments->total(),
            ],
        ]);
    }

    /**
     * Generate certificate for completed course.
     */
    private function generateCertificate($user, $course)
    {
        try {
            $certificateService = app(CertificateService::class);
            
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
