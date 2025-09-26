<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ModuleController extends Controller
{
    /**
     * Display a listing of modules for a course.
     */
    public function index(Course $course): JsonResponse
    {
        $modules = $course->modules()
            ->with(['lessons' => function ($query) {
                $query->orderBy('order');
            }])
            ->orderBy('order')
            ->get();

        return response()->json([
            'data' => $modules
        ]);
    }

    /**
     * Store a newly created module.
     */
    public function store(Request $request, Course $course): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'order' => 'nullable|integer|min:1',
        ]);

        // Set order if not provided
        if (!isset($validated['order'])) {
            $validated['order'] = $course->modules()->max('order') + 1;
        }

        $validated['course_id'] = $course->id;
        $validated['is_active'] = true;

        $module = Module::create($validated);

        return response()->json([
            'message' => 'Modulo creato con successo.',
            'data' => $module->load('lessons')
        ], 201);
    }

    /**
     * Display the specified module.
     */
    public function show(Module $module): JsonResponse
    {
        $module->load(['course', 'lessons' => function ($query) {
            $query->orderBy('order');
        }]);

        return response()->json([
            'data' => $module
        ]);
    }

    /**
     * Update the specified module.
     */
    public function update(Request $request, Module $module): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'order' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        $module->update($validated);

        return response()->json([
            'message' => 'Modulo aggiornato con successo.',
            'data' => $module->load('lessons')
        ]);
    }

    /**
     * Remove the specified module.
     */
    public function destroy(Module $module): JsonResponse
    {
        // Delete all lessons in this module first
        $module->lessons()->delete();
        
        $module->delete();

        return response()->json([
            'message' => 'Modulo eliminato con successo.'
        ]);
    }

    /**
     * Reorder modules.
     */
    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'modules' => 'required|array',
            'modules.*.id' => 'required|exists:modules,id',
            'modules.*.order' => 'required|integer|min:1',
        ]);

        foreach ($validated['modules'] as $moduleData) {
            Module::where('id', $moduleData['id'])
                ->update(['order' => $moduleData['order']]);
        }

        return response()->json([
            'message' => 'Ordine moduli aggiornato con successo.'
        ]);
    }
}
