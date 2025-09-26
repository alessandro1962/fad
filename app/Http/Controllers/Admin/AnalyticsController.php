<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Progress;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AnalyticsExport;

class AnalyticsController extends Controller
{
    /**
     * Get analytics dashboard data.
     */
    public function index(Request $request): JsonResponse
    {
        $period = $request->get('period', 30);
        $organizationId = $request->get('organization_id');
        
        $startDate = now()->subDays($period);
        $previousStartDate = now()->subDays($period * 2);
        $previousEndDate = now()->subDays($period);
        
        // Get analytics data
        $analytics = $this->getAnalyticsData($startDate, $previousStartDate, $previousEndDate, $organizationId);
        
        // Get top courses
        $topCourses = $this->getTopCourses($startDate, $organizationId);
        
        return response()->json([
            'data' => [
                'analytics' => $analytics,
                'top_courses' => $topCourses,
            ],
        ]);
    }

    /**
     * Export analytics data.
     */
    public function export(Request $request)
    {
        $period = $request->get('period', 30);
        $organizationId = $request->get('organization_id');
        
        $startDate = now()->subDays($period);
        
        return Excel::download(
            new AnalyticsExport($startDate, $organizationId),
            "analytics_{$period}days.xlsx"
        );
    }

    /**
     * Get analytics data for the dashboard.
     */
    private function getAnalyticsData($startDate, $previousStartDate, $previousEndDate, $organizationId = null): array
    {
        $query = User::query();
        $previousQuery = User::query();
        
        if ($organizationId) {
            $query->where('organization_id', $organizationId);
            $previousQuery->where('organization_id', $organizationId);
        }
        
        // Active users (users who have logged in within the period)
        $activeUsers = $query->where('last_login_at', '>=', $startDate)->count();
        $previousActiveUsers = $previousQuery->whereBetween('last_login_at', [$previousStartDate, $previousEndDate])->count();
        $activeUsersChange = $this->calculatePercentageChange($activeUsers, $previousActiveUsers);
        
        // Completed courses
        $completedCourses = Enrollment::where('status', 'completed')
            ->where('completed_at', '>=', $startDate)
            ->when($organizationId, function ($q) use ($organizationId) {
                return $q->whereHas('user', function ($userQuery) use ($organizationId) {
                    $userQuery->where('organization_id', $organizationId);
                });
            })
            ->count();
            
        $previousCompletedCourses = Enrollment::where('status', 'completed')
            ->whereBetween('completed_at', [$previousStartDate, $previousEndDate])
            ->when($organizationId, function ($q) use ($organizationId) {
                return $q->whereHas('user', function ($userQuery) use ($organizationId) {
                    $userQuery->where('organization_id', $organizationId);
                });
            })
            ->count();
        $completedCoursesChange = $this->calculatePercentageChange($completedCourses, $previousCompletedCourses);
        
        // Completion rate
        $totalEnrollments = Enrollment::where('created_at', '>=', $startDate)
            ->when($organizationId, function ($q) use ($organizationId) {
                return $q->whereHas('user', function ($userQuery) use ($organizationId) {
                    $userQuery->where('organization_id', $organizationId);
                });
            })
            ->count();
        $completionRate = $totalEnrollments > 0 ? round(($completedCourses / $totalEnrollments) * 100, 1) : 0;
        
        $previousTotalEnrollments = Enrollment::whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->when($organizationId, function ($q) use ($organizationId) {
                return $q->whereHas('user', function ($userQuery) use ($organizationId) {
                    $userQuery->where('organization_id', $organizationId);
                });
            })
            ->count();
        $previousCompletionRate = $previousTotalEnrollments > 0 ? round(($previousCompletedCourses / $previousTotalEnrollments) * 100, 1) : 0;
        $completionRateChange = $this->calculatePercentageChange($completionRate, $previousCompletionRate);
        
        // Learning hours
        $learningHours = Progress::where('last_seen_at', '>=', $startDate)
            ->when($organizationId, function ($q) use ($organizationId) {
                return $q->whereHas('user', function ($userQuery) use ($organizationId) {
                    $userQuery->where('organization_id', $organizationId);
                });
            })
            ->sum('seconds_watched') / 3600;
        $learningHours = round($learningHours, 1);
        
        $previousLearningHours = Progress::whereBetween('last_seen_at', [$previousStartDate, $previousEndDate])
            ->when($organizationId, function ($q) use ($organizationId) {
                return $q->whereHas('user', function ($userQuery) use ($organizationId) {
                    $userQuery->where('organization_id', $organizationId);
                });
            })
            ->sum('seconds_watched') / 3600;
        $previousLearningHours = round($previousLearningHours, 1);
        $learningHoursChange = $this->calculatePercentageChange($learningHours, $previousLearningHours);
        
        // New users
        $newUsers = User::where('created_at', '>=', $startDate)
            ->when($organizationId, function ($q) use ($organizationId) {
                return $q->where('organization_id', $organizationId);
            })
            ->count();
        
        // Average session time
        $avgSessionTime = $this->calculateAverageSessionTime($startDate, $organizationId);
        
        return [
            'active_users' => $activeUsers,
            'active_users_change' => $activeUsersChange,
            'completed_courses' => $completedCourses,
            'completed_courses_change' => $completedCoursesChange,
            'completion_rate' => $completionRate,
            'completion_rate_change' => $completionRateChange,
            'learning_hours' => $learningHours,
            'learning_hours_change' => $learningHoursChange,
            'new_users' => $newUsers,
            'avg_session_time' => $avgSessionTime,
        ];
    }

    /**
     * Get top courses by enrollments and completion rate.
     */
    private function getTopCourses($startDate, $organizationId = null): array
    {
        $courses = Course::withCount([
            'enrollments' => function ($query) use ($startDate, $organizationId) {
                $query->where('created_at', '>=', $startDate)
                    ->when($organizationId, function ($q) use ($organizationId) {
                        return $q->whereHas('user', function ($userQuery) use ($organizationId) {
                            $userQuery->where('organization_id', $organizationId);
                        });
                    });
            },
            'enrollments as completed_enrollments_count' => function ($query) use ($startDate, $organizationId) {
                $query->where('status', 'completed')
                    ->where('completed_at', '>=', $startDate)
                    ->when($organizationId, function ($q) use ($organizationId) {
                        return $q->whereHas('user', function ($userQuery) use ($organizationId) {
                            $userQuery->where('organization_id', $organizationId);
                        });
                    });
            }
        ])
        ->orderBy('enrollments_count', 'desc')
        ->limit(5)
        ->get()
        ->map(function ($course) {
            $completionRate = $course->enrollments_count > 0 
                ? round(($course->completed_enrollments_count / $course->enrollments_count) * 100, 1)
                : 0;
                
            return [
                'id' => $course->id,
                'title' => $course->title,
                'enrollments' => $course->enrollments_count,
                'completion_rate' => $completionRate,
            ];
        });

        return $courses->toArray();
    }

    /**
     * Calculate percentage change between two values.
     */
    private function calculatePercentageChange($current, $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        
        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * Calculate average session time.
     */
    private function calculateAverageSessionTime($startDate, $organizationId = null): string
    {
        $avgSeconds = Progress::where('last_seen_at', '>=', $startDate)
            ->when($organizationId, function ($q) use ($organizationId) {
                return $q->whereHas('user', function ($userQuery) use ($organizationId) {
                    $userQuery->where('organization_id', $organizationId);
                });
            })
            ->avg('seconds_watched');
        
        if (!$avgSeconds) {
            return '0m';
        }
        
        $minutes = round($avgSeconds / 60);
        return $minutes . 'm';
    }

    /**
     * Get user engagement metrics.
     */
    public function engagement(Request $request): JsonResponse
    {
        $period = $request->get('period', 30);
        $organizationId = $request->get('organization_id');
        $startDate = now()->subDays($period);
        
        // Daily active users
        $dailyActiveUsers = $this->getDailyActiveUsers($startDate, $organizationId);
        
        // Course completion trends
        $completionTrends = $this->getCompletionTrends($startDate, $organizationId);
        
        // Learning time distribution
        $learningTimeDistribution = $this->getLearningTimeDistribution($startDate, $organizationId);
        
        return response()->json([
            'data' => [
                'daily_active_users' => $dailyActiveUsers,
                'completion_trends' => $completionTrends,
                'learning_time_distribution' => $learningTimeDistribution,
            ],
        ]);
    }

    /**
     * Get daily active users data.
     */
    private function getDailyActiveUsers($startDate, $organizationId = null): array
    {
        $query = User::selectRaw('DATE(last_login_at) as date, COUNT(*) as count')
            ->where('last_login_at', '>=', $startDate)
            ->when($organizationId, function ($q) use ($organizationId) {
                return $q->where('organization_id', $organizationId);
            })
            ->groupBy('date')
            ->orderBy('date');
        
        return $query->get()->toArray();
    }

    /**
     * Get completion trends data.
     */
    private function getCompletionTrends($startDate, $organizationId = null): array
    {
        $query = Enrollment::selectRaw('DATE(completed_at) as date, COUNT(*) as count')
            ->where('status', 'completed')
            ->where('completed_at', '>=', $startDate)
            ->when($organizationId, function ($q) use ($organizationId) {
                return $q->whereHas('user', function ($userQuery) use ($organizationId) {
                    $userQuery->where('organization_id', $organizationId);
                });
            })
            ->groupBy('date')
            ->orderBy('date');
        
        return $query->get()->toArray();
    }

    /**
     * Get learning time distribution.
     */
    private function getLearningTimeDistribution($startDate, $organizationId = null): array
    {
        $query = Progress::selectRaw('
                CASE 
                    WHEN seconds_watched < 300 THEN "0-5 min"
                    WHEN seconds_watched < 900 THEN "5-15 min"
                    WHEN seconds_watched < 1800 THEN "15-30 min"
                    WHEN seconds_watched < 3600 THEN "30-60 min"
                    ELSE "60+ min"
                END as time_range,
                COUNT(*) as count
            ')
            ->where('last_seen_at', '>=', $startDate)
            ->when($organizationId, function ($q) use ($organizationId) {
                return $q->whereHas('user', function ($userQuery) use ($organizationId) {
                    $userQuery->where('organization_id', $organizationId);
                });
            })
            ->groupBy('time_range')
            ->orderByRaw('
                CASE 
                    WHEN time_range = "0-5 min" THEN 1
                    WHEN time_range = "5-15 min" THEN 2
                    WHEN time_range = "15-30 min" THEN 3
                    WHEN time_range = "30-60 min" THEN 4
                    ELSE 5
                END
            ');
        
        return $query->get()->toArray();
    }
}
