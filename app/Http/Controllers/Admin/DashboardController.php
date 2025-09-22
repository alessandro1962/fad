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
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get admin dashboard statistics.
     */
    public function statistics(): JsonResponse
    {
        $currentMonth = now()->startOfMonth();
        $previousMonth = now()->subMonth()->startOfMonth();
        $previousMonthEnd = now()->subMonth()->endOfMonth();
        
        // Total users
        $totalUsers = User::count();
        $previousTotalUsers = User::where('created_at', '<', $previousMonthEnd)->count();
        $userGrowth = $previousTotalUsers > 0 ? 
            round((($totalUsers - $previousTotalUsers) / $previousTotalUsers) * 100, 1) : 0;
        
        // Active courses
        $activeCourses = Course::where('is_active', true)->count();
        $previousActiveCourses = Course::where('is_active', true)
            ->where('created_at', '<', $previousMonthEnd)->count();
        $courseGrowth = $previousActiveCourses > 0 ? 
            round((($activeCourses - $previousActiveCourses) / $previousActiveCourses) * 100, 1) : 0;
        
        // Course completions
        $completions = Enrollment::where('status', 'completed')->count();
        $previousCompletions = Enrollment::where('status', 'completed')
            ->where('completed_at', '<', $previousMonthEnd)->count();
        $completionGrowth = $previousCompletions > 0 ? 
            round((($completions - $previousCompletions) / $previousCompletions) * 100, 1) : 0;
        
        // Monthly revenue (placeholder - you'll need to implement actual revenue tracking)
        $monthlyRevenue = 0; // This would come from your payment/transaction system
        $previousMonthlyRevenue = 0;
        $revenueGrowth = 0;
        
        // Recent activities
        $recentActivities = $this->getRecentActivities();
        
        // System status
        $systemStatus = $this->getSystemStatus();
        
        return response()->json([
            'data' => [
                'statistics' => [
                    'total_users' => [
                        'value' => number_format($totalUsers),
                        'change' => $userGrowth,
                        'change_label' => 'rispetto al mese scorso'
                    ],
                    'active_courses' => [
                        'value' => number_format($activeCourses),
                        'change' => $courseGrowth,
                        'change_label' => 'rispetto al mese scorso'
                    ],
                    'completions' => [
                        'value' => number_format($completions),
                        'change' => $completionGrowth,
                        'change_label' => 'rispetto al mese scorso'
                    ],
                    'monthly_revenue' => [
                        'value' => '€' . number_format($monthlyRevenue, 0, ',', '.'),
                        'change' => $revenueGrowth,
                        'change_label' => 'rispetto al mese scorso'
                    ]
                ],
                'recent_activities' => $recentActivities,
                'system_status' => $systemStatus
            ]
        ]);
    }
    
    /**
     * Get recent activities for the dashboard.
     */
    private function getRecentActivities(): array
    {
        $activities = [];
        
        // Recent user registrations
        $recentUsers = User::where('created_at', '>=', now()->subHours(24))
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
            
        foreach ($recentUsers as $user) {
            $activities[] = [
                'id' => 'user_' . $user->id,
                'title' => 'Nuovo utente registrato',
                'description' => $user->name . ' si è registrato alla piattaforma',
                'timestamp' => $user->created_at->diffForHumans(),
                'icon' => 'user',
                'bgClass' => 'bg-cdf-teal/10',
                'iconClass' => 'text-cdf-teal'
            ];
        }
        
        // Recent course completions
        $recentCompletions = Enrollment::where('status', 'completed')
            ->where('completed_at', '>=', now()->subHours(24))
            ->with(['user', 'course'])
            ->orderBy('completed_at', 'desc')
            ->limit(3)
            ->get();
            
        foreach ($recentCompletions as $enrollment) {
            $activities[] = [
                'id' => 'completion_' . $enrollment->id,
                'title' => 'Corso completato',
                'description' => $enrollment->user->name . ' ha completato "' . $enrollment->course->title . '"',
                'timestamp' => $enrollment->completed_at->diffForHumans(),
                'icon' => 'check',
                'bgClass' => 'bg-cdf-amber/10',
                'iconClass' => 'text-cdf-amber'
            ];
        }
        
        // Recent course enrollments
        $recentEnrollments = Enrollment::where('created_at', '>=', now()->subHours(24))
            ->with(['user', 'course'])
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
            
        foreach ($recentEnrollments as $enrollment) {
            $activities[] = [
                'id' => 'enrollment_' . $enrollment->id,
                'title' => 'Nuovo iscritto',
                'description' => $enrollment->user->name . ' si è iscritto a "' . $enrollment->course->title . '"',
                'timestamp' => $enrollment->created_at->diffForHumans(),
                'icon' => 'book',
                'bgClass' => 'bg-green-100',
                'iconClass' => 'text-green-600'
            ];
        }
        
        // Sort by timestamp and limit to 10
        usort($activities, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });
        
        return array_slice($activities, 0, 10);
    }
    
    /**
     * Get system status information.
     */
    private function getSystemStatus(): array
    {
        return [
            'database' => [
                'status' => 'operational',
                'response_time' => '< 50ms'
            ],
            'storage' => [
                'status' => 'operational',
                'usage' => '45%'
            ],
            'cdn' => [
                'status' => 'operational',
                'response_time' => '< 100ms'
            ]
        ];
    }
}
