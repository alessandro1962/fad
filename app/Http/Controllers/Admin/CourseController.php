<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CourseRequest;
use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    /**
     * Display a listing of courses for admin
     */
    public function index(Request $request): JsonResponse
    {
        $query = Course::with(['modules', 'enrollments'])
            ->withCount(['modules', 'enrollments']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by level
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'published') {
                $query->whereNotNull('published_at');
            } elseif ($request->status === 'draft') {
                $query->whereNull('published_at');
            }
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Check if all courses are requested (for dropdowns)
        if ($request->boolean('all')) {
            $courses = $query->get();
            return response()->json([
                'data' => $courses
            ]);
        }

        $courses = $query->paginate(15);

        return response()->json([
            'data' => $courses->items(),
            'meta' => [
                'current_page' => $courses->currentPage(),
                'last_page' => $courses->lastPage(),
                'per_page' => $courses->perPage(),
                'total' => $courses->total(),
            ],
            'links' => [
                'first' => $courses->url(1),
                'last' => $courses->url($courses->lastPage()),
                'prev' => $courses->previousPageUrl(),
                'next' => $courses->nextPageUrl(),
            ]
        ]);
    }

    /**
     * Store a newly created course
     */
    public function store(CourseRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        // Ensure slug is unique
        $originalSlug = $data['slug'];
        $counter = 1;
        while (Course::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        $course = Course::create($data);

        return response()->json([
            'message' => 'Corso creato con successo',
            'data' => $course->load(['modules', 'enrollments'])
        ], 201);
    }

    /**
     * Display the specified course
     */
    public function show(Course $course): JsonResponse
    {
        $course->load([
            'modules.lessons',
            'enrollments.user',
            'enrollments' => function ($query) {
                $query->withCount('progress');
            }
        ]);

        return response()->json([
            'data' => $course
        ]);
    }

    /**
     * Update the specified course
     */
    public function update(CourseRequest $request, Course $course): JsonResponse
    {
        $data = $request->validated();
        
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        // Ensure slug is unique (excluding current course)
        if ($data['slug'] !== $course->slug) {
            $originalSlug = $data['slug'];
            $counter = 1;
            while (Course::where('slug', $data['slug'])->where('id', '!=', $course->id)->exists()) {
                $data['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $course->update($data);

        return response()->json([
            'message' => 'Corso aggiornato con successo',
            'data' => $course->load(['modules', 'enrollments'])
        ]);
    }

    /**
     * Remove the specified course
     */
    public function destroy(Request $request, Course $course): JsonResponse
    {
        // Check if course has enrollments
        if ($course->enrollments()->count() > 0) {
            // If force delete is requested, delete enrollments first
            if ($request->boolean('force')) {
                $course->enrollments()->delete();
                $course->delete();
                
                return response()->json([
                    'message' => 'Corso eliminato con successo (incluse le iscrizioni)'
                ]);
            }
            
            return response()->json([
                'message' => 'Impossibile eliminare il corso: ci sono iscrizioni attive. Usa "Elimina forzatamente" per rimuovere anche le iscrizioni.'
            ], 422);
        }

        $course->delete();

        return response()->json([
            'message' => 'Corso eliminato con successo'
        ]);
    }

    /**
     * Toggle course active status
     */
    public function toggleStatus(Course $course): JsonResponse
    {
        $course->update(['is_active' => !$course->is_active]);

        return response()->json([
            'message' => $course->is_active ? 'Corso attivato' : 'Corso disattivato',
            'data' => $course
        ]);
    }

    /**
     * Publish/unpublish course
     */
    public function togglePublish(Course $course): JsonResponse
    {
        $publishedAt = $course->published_at ? null : now();
        $course->update(['published_at' => $publishedAt]);

        return response()->json([
            'message' => $publishedAt ? 'Corso pubblicato' : 'Corso rimosso dalla pubblicazione',
            'data' => $course
        ]);
    }

    /**
     * Get course statistics
     */
    public function statistics(Course $course): JsonResponse
    {
        $stats = [
            'total_enrollments' => $course->enrollments()->count(),
            'active_enrollments' => $course->enrollments()->where('status', 'active')->count(),
            'completed_enrollments' => $course->enrollments()->where('status', 'completed')->count(),
            'average_progress' => $course->enrollments()->avg('progress_percentage') ?? 0,
            'total_modules' => $course->modules()->count(),
            'total_lessons' => $course->modules()->withCount('lessons')->get()->sum('lessons_count'),
            'total_duration' => $course->modules()->with('lessons')->get()
                ->sum(function ($module) {
                    return $module->lessons->sum('duration_minutes');
                })
        ];

        return response()->json([
            'data' => $stats
        ]);
    }
}