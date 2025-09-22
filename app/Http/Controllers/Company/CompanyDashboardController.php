<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Progress;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CompanyDashboardController extends Controller
{
    /**
     * Get company dashboard statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        // Verifica che l'utente sia un company manager
        if (!$user->is_company_manager || !$user->organization_id) {
            return response()->json(['error' => 'Accesso negato'], 403);
        }

        $organizationId = $user->organization_id;
        $period = $request->get('period', 30); // Default to last 30 days
        $startDate = now()->subDays($period);

        // Dipendenti totali dell'azienda
        $totalEmployees = User::where('organization_id', $organizationId)
            ->where('is_admin', false)
            ->where('is_company_manager', false)
            ->count();

        // Nuovi dipendenti nell'ultimo periodo
        $newEmployeesLastPeriod = User::where('organization_id', $organizationId)
            ->where('is_admin', false)
            ->where('is_company_manager', false)
            ->where('created_at', '>=', $startDate)
            ->count();

        // Corsi attivi assegnati all'azienda
        $activeCourses = Course::where('is_active', true)->count();

        // Completamenti totali dei dipendenti
        $totalCompletions = Enrollment::whereHas('user', function($query) use ($organizationId) {
            $query->where('organization_id', $organizationId);
        })->where('status', 'completed')->count();

        // Completamenti nell'ultimo periodo
        $newCompletionsLastPeriod = Enrollment::whereHas('user', function($query) use ($organizationId) {
            $query->where('organization_id', $organizationId);
        })->where('status', 'completed')
        ->where('updated_at', '>=', $startDate)
        ->count();

        // Progresso medio dell'azienda
        $averageProgress = Progress::whereHas('user', function($query) use ($organizationId) {
            $query->where('organization_id', $organizationId);
        })->avg('progress_percentage') ?? 0;

        return response()->json([
            'data' => [
                'statistics' => [
                    'total_employees' => ['value' => number_format($totalEmployees), 'change' => 0, 'change_label' => 'dipendenti totali'],
                    'new_employees' => ['value' => number_format($newEmployeesLastPeriod), 'change' => 0, 'change_label' => 'nuovi dipendenti'],
                    'active_courses' => ['value' => number_format($activeCourses), 'change' => 0, 'change_label' => 'corsi attivi'],
                    'total_completions' => ['value' => number_format($totalCompletions), 'change' => 0, 'change_label' => 'corsi completati'],
                    'average_progress' => ['value' => number_format($averageProgress, 1) . '%', 'change' => 0, 'change_label' => 'progresso medio'],
                ],
            ],
        ]);
    }

    /**
     * Get employees list with their progress
     */
    public function employees(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user->is_company_manager || !$user->organization_id) {
            return response()->json(['error' => 'Accesso negato'], 403);
        }

        $organizationId = $user->organization_id;
        $perPage = $request->get('per_page', 15);
        $search = $request->get('search');

        $query = User::where('organization_id', $organizationId)
            ->where('is_admin', false)
            ->where('is_company_manager', false)
            ->with(['enrollments.course', 'progress']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            });
        }

        $employees = $query->paginate($perPage);

        // Aggiungi statistiche per ogni dipendente
        $employees->getCollection()->transform(function($employee) {
            $totalCourses = $employee->enrollments->count();
            $completedCourses = $employee->enrollments->where('status', 'completed')->count();
            $averageProgress = $employee->progress->avg('progress_percentage') ?? 0;
            
            $employee->stats = [
                'total_courses' => $totalCourses,
                'completed_courses' => $completedCourses,
                'average_progress' => round($averageProgress, 1),
                'completion_rate' => $totalCourses > 0 ? round(($completedCourses / $totalCourses) * 100, 1) : 0,
            ];

            return $employee;
        });

        return response()->json([
            'data' => $employees->items(),
            'pagination' => [
                'current_page' => $employees->currentPage(),
                'last_page' => $employees->lastPage(),
                'per_page' => $employees->perPage(),
                'total' => $employees->total(),
            ],
        ]);
    }

    /**
     * Get employee details with course progress
     */
    public function employeeDetails($employeeId): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user->is_company_manager || !$user->organization_id) {
            return response()->json(['error' => 'Accesso negato'], 403);
        }

        $employee = User::where('id', $employeeId)
            ->where('organization_id', $user->organization_id)
            ->where('is_admin', false)
            ->where('is_company_manager', false)
            ->with(['enrollments.course', 'progress.lesson'])
            ->first();

        if (!$employee) {
            return response()->json(['error' => 'Dipendente non trovato'], 404);
        }

        // Calcola statistiche dettagliate
        $enrollments = $employee->enrollments->map(function($enrollment) {
            $course = $enrollment->course;
            $progress = $employee->progress->where('course_id', $course->id)->first();
            
            return [
                'id' => $enrollment->id,
                'course' => [
                    'id' => $course->id,
                    'title' => $course->title,
                    'description' => $course->description,
                    'thumbnail' => $course->thumbnail,
                ],
                'status' => $enrollment->status,
                'enrolled_at' => $enrollment->created_at,
                'completed_at' => $enrollment->updated_at,
                'progress_percentage' => $progress ? $progress->progress_percentage : 0,
                'last_activity' => $progress ? $progress->updated_at : $enrollment->created_at,
            ];
        });

        $stats = [
            'total_courses' => $employee->enrollments->count(),
            'completed_courses' => $employee->enrollments->where('status', 'completed')->count(),
            'in_progress_courses' => $employee->enrollments->where('status', 'in_progress')->count(),
            'average_progress' => $employee->progress->avg('progress_percentage') ?? 0,
            'total_time_spent' => $employee->progress->sum('time_spent') ?? 0,
        ];

        return response()->json([
            'data' => [
                'employee' => [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'email' => $employee->email,
                    'company' => $employee->company,
                    'profession' => $employee->profession,
                    'created_at' => $employee->created_at,
                    'last_login_at' => $employee->last_login_at,
                ],
                'enrollments' => $enrollments,
                'stats' => $stats,
            ],
        ]);
    }

    /**
     * Get courses assigned to company employees
     */
    public function courses(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user->is_company_manager || !$user->organization_id) {
            return response()->json(['error' => 'Accesso negato'], 403);
        }

        $organizationId = $user->organization_id;

        // Corsi con statistiche per l'azienda
        $courses = Course::where('is_active', true)
            ->with(['enrollments' => function($query) use ($organizationId) {
                $query->whereHas('user', function($q) use ($organizationId) {
                    $q->where('organization_id', $organizationId);
                });
            }])
            ->get()
            ->map(function($course) use ($organizationId) {
                $enrollments = $course->enrollments;
                $totalEnrollments = $enrollments->count();
                $completedEnrollments = $enrollments->where('status', 'completed')->count();
                
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'description' => $course->description,
                    'thumbnail' => $course->thumbnail,
                    'stats' => [
                        'total_enrollments' => $totalEnrollments,
                        'completed_enrollments' => $completedEnrollments,
                        'completion_rate' => $totalEnrollments > 0 ? round(($completedEnrollments / $totalEnrollments) * 100, 1) : 0,
                        'average_progress' => $enrollments->avg('progress_percentage') ?? 0,
                    ],
                ];
            });

        return response()->json([
            'data' => $courses,
        ]);
    }
}