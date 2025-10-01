<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CourseEnrollmentNotification;

class EnrollmentController extends Controller
{
    /**
     * Create a new enrollment for a user.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'course_id' => ['required', 'exists:courses,id'],
            'source' => ['required', 'in:assign,purchase,full_vision'],
            'payment_method' => ['nullable', 'in:manual,stripe,bank_transfer'],
            'start_date' => ['required', 'date'],
            'expiry_date' => ['nullable', 'date', 'after:start_date'],
            'send_notification' => ['boolean'],
        ]);

        $user = User::findOrFail($validated['user_id']);
        $course = Course::findOrFail($validated['course_id']);

        // Check if user is already enrolled
        $existingEnrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existingEnrollment) {
            return response()->json([
                'message' => 'L\'utente è già iscritto a questo corso',
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Create enrollment
            $enrollment = Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'source' => $validated['source'],
                'status' => 'active',
                'started_at' => $validated['start_date'],
                'expires_at' => $validated['expiry_date'] ?? null,
            ]);

            // Create order if it's a purchase
            if ($validated['source'] === 'purchase') {
                $order = Order::create([
                    'user_id' => $user->id,
                    'total_cents' => $course->price_cents,
                    'currency' => $course->currency ?? 'EUR',
                    'status' => 'paid',
                    'gateway' => $validated['payment_method'] ?? 'manual',
                    'paid_at' => now(),
                ]);

                OrderItem::create([
                    'order_id' => $order->id,
                    'item_type' => 'course',
                    'item_id' => $course->id,
                    'qty' => 1,
                    'unit_price_cents' => $course->price_cents,
                ]);

                $enrollment->update(['order_id' => $order->id]);
            }

            // If this is the Full Vision course (ID: 11), auto-enroll user in all other courses
            if ($course->id == 11) {
                $this->autoEnrollInAllCourses($user, $validated);
            } else {
                // Send notification if requested (only for individual courses, not Full Vision)
                if ($validated['send_notification'] ?? true) {
                    $user->notify(new CourseEnrollmentNotification($course, $enrollment));
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Utente iscritto al corso con successo',
                'data' => $enrollment->load(['user', 'course']),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Errore durante l\'iscrizione',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user enrollments.
     */
    public function userEnrollments(User $user): JsonResponse
    {
        $enrollments = $user->enrollments()
            ->with(['course', 'order'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $enrollments,
        ]);
    }

    /**
     * Update enrollment status.
     */
    public function updateStatus(Request $request, Enrollment $enrollment): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:active,paused,completed,cancelled'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $enrollment->update($validated);

        return response()->json([
            'message' => 'Status iscrizione aggiornato con successo',
            'data' => $enrollment->load(['user', 'course']),
        ]);
    }

    /**
     * Cancel enrollment.
     */
    public function cancel(Enrollment $enrollment): JsonResponse
    {
        $enrollment->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return response()->json([
            'message' => 'Iscrizione cancellata con successo',
            'data' => $enrollment->load(['user', 'course']),
        ]);
    }

    /**
     * Get enrollment statistics.
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_enrollments' => Enrollment::count(),
            'active_enrollments' => Enrollment::where('status', 'active')->count(),
            'completed_enrollments' => Enrollment::where('status', 'completed')->count(),
            'cancelled_enrollments' => Enrollment::where('status', 'cancelled')->count(),
            'recent_enrollments' => Enrollment::where('created_at', '>=', now()->subDays(30))->count(),
            'enrollments_by_source' => Enrollment::selectRaw('source, COUNT(*) as count')
                ->groupBy('source')
                ->get()
                ->pluck('count', 'source'),
        ];

        return response()->json([
            'data' => $stats,
        ]);
    }

    /**
     * Bulk enroll users to a course.
     */
    public function bulkEnroll(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['exists:users,id'],
            'course_id' => ['required', 'exists:courses,id'],
            'source' => ['required', 'in:assign,purchase,full_vision'],
            'start_date' => ['required', 'date'],
            'expiry_date' => ['nullable', 'date', 'after:start_date'],
            'send_notification' => ['boolean'],
        ]);

        $course = Course::findOrFail($validated['course_id']);
        $enrolledCount = 0;
        $skippedCount = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($validated['user_ids'] as $userId) {
                // Check if user is already enrolled
                $existingEnrollment = Enrollment::where('user_id', $userId)
                    ->where('course_id', $course->id)
                    ->first();

                if ($existingEnrollment) {
                    $skippedCount++;
                    continue;
                }

                try {
                    // Create enrollment
                    $enrollment = Enrollment::create([
                        'user_id' => $userId,
                        'course_id' => $course->id,
                        'source' => $validated['source'],
                        'status' => 'active',
                        'started_at' => $validated['start_date'],
                        'expires_at' => $validated['expiry_date'] ?? null,
                    ]);

                    // If this is the Full Vision course (ID: 11), auto-enroll user in all other courses
                    if ($course->id == 11) {
                        $user = User::find($userId);
                        $this->autoEnrollInAllCourses($user, $validated);
                    } else {
                        // Send notification if requested (only for individual courses, not Full Vision)
                        if ($validated['send_notification'] ?? true) {
                            $user = User::find($userId);
                            $user->notify(new CourseEnrollmentNotification($course, $enrollment));
                        }
                    }

                    $enrolledCount++;

                } catch (\Exception $e) {
                    $errors[] = "Errore per utente ID {$userId}: " . $e->getMessage();
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Iscrizioni bulk completate',
                'data' => [
                    'enrolled_count' => $enrolledCount,
                    'skipped_count' => $skippedCount,
                    'errors' => $errors,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Errore durante le iscrizioni bulk',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Auto-enroll user in all courses when Full Vision is assigned.
     */
    private function autoEnrollInAllCourses(User $user, array $validated): void
    {
        // Get all active courses except the Full Vision course itself
        $allCourses = Course::where('is_active', true)
            ->where('id', '!=', 11) // Exclude Full Vision course
            ->get();

        $enrolledCourses = collect();

        foreach ($allCourses as $course) {
            // Check if user is already enrolled in this course
            $existingEnrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();

            if (!$existingEnrollment) {
                // Create enrollment for this course
                Enrollment::create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'source' => 'full_vision',
                    'status' => 'active',
                    'started_at' => $validated['start_date'],
                    'expires_at' => $validated['expiry_date'] ?? null,
                ]);

                $enrolledCourses->push($course);
            }
        }

        // Send single Full Vision notification if requested and courses were enrolled
        if (($validated['send_notification'] ?? true) && $enrolledCourses->isNotEmpty()) {
            $user->notify(new \App\Notifications\FullVisionAssignedNotification($enrolledCourses->all()));
        }
    }
}
