<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CourseResource;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of courses.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Course::query()
            ->active()
            ->published()
            ->with(['modules.lessons', 'enrollments']);

        // Filtri
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('level')) {
            $query->where('level', $request->get('level'));
        }

        if ($request->has('tags')) {
            $tags = explode(',', $request->get('tags'));
            $query->whereJsonContains('tags', $tags);
        }

        if ($request->has('min_price')) {
            $query->where('price_cents', '>=', $request->get('min_price') * 100);
        }

        if ($request->has('max_price')) {
            $query->where('price_cents', '<=', $request->get('max_price') * 100);
        }

        // Ordinamento
        $sortBy = $request->get('sort_by', 'published_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSortFields = ['title', 'price_cents', 'duration_minutes', 'published_at', 'created_at'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Paginazione
        $perPage = min($request->get('per_page', 12), 50);
        $courses = $query->paginate($perPage);

        return response()->json([
            'data' => CourseResource::collection($courses->items()),
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
            ],
        ]);
    }

    /**
     * Display the specified course.
     */
    public function show(Request $request, Course $course): JsonResponse
    {
        $course->load(['modules.lessons', 'enrollments', 'tracks']);

        return response()->json([
            'data' => new CourseResource($course),
        ]);
    }

    /**
     * Get featured courses.
     */
    public function featured(Request $request): JsonResponse
    {
        $courses = Course::query()
            ->active()
            ->published()
            ->with(['modules.lessons', 'enrollments'])
            ->withCount('enrollments')->orderBy('enrollments_count', 'desc')
            ->limit(6)
            ->get();

        return response()->json([
            'data' => CourseResource::collection($courses),
        ]);
    }

    /**
     * Get courses by level.
     */
    public function byLevel(Request $request, string $level): JsonResponse
    {
        $validLevels = ['beginner', 'intermediate', 'expert'];
        
        if (!in_array($level, $validLevels)) {
            return response()->json([
                'message' => 'Livello non valido.',
            ], 400);
        }

        $courses = Course::query()
            ->active()
            ->published()
            ->where('level', $level)
            ->with(['modules.lessons', 'enrollments'])
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return response()->json([
            'data' => CourseResource::collection($courses->items()),
            'meta' => [
                'current_page' => $courses->currentPage(),
                'last_page' => $courses->lastPage(),
                'per_page' => $courses->perPage(),
                'total' => $courses->total(),
            ],
        ]);
    }
}
