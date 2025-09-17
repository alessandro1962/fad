<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Badge;
use App\Models\UserBadge;
use App\Services\BadgeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GamificationController extends Controller
{
    protected BadgeService $badgeService;

    public function __construct(BadgeService $badgeService)
    {
        $this->badgeService = $badgeService;
    }

    /**
     * Get user's gamification statistics.
     */
    public function getStats(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Calculate user level
        $level = $this->calculateUserLevel($user);
        
        // Get user statistics
        $stats = [
            'total_points' => $this->calculateTotalPoints($user),
            'current_level_points' => $this->calculateCurrentLevelPoints($user),
            'next_level_points' => $this->calculateNextLevelPoints($user),
            'points_to_next_level' => $this->calculatePointsToNextLevel($user),
            'completed_courses' => $user->enrollments()->completed()->count(),
            'completed_lessons' => $user->progress()->completed()->count(),
            'passed_quizzes' => $user->attempts()->passed()->distinct('quiz_id')->count(),
            'learning_hours' => round($user->progress()->sum('seconds_watched') / 3600, 1),
            'learning_streak' => $this->calculateLearningStreak($user),
            'total_learning_days' => $this->calculateTotalLearningDays($user),
        ];

        return response()->json([
            'data' => [
                'stats' => $stats,
                'level' => $level,
            ],
        ]);
    }

    /**
     * Get upcoming achievements for the user.
     */
    public function getAchievements(Request $request): JsonResponse
    {
        $user = $request->user();
        $achievements = [];

        // Get all badges the user doesn't have yet
        $userBadgeIds = $user->badges()->pluck('badges.id')->toArray();
        $availableBadges = Badge::active()
            ->whereNotIn('id', $userBadgeIds)
            ->get();

        foreach ($availableBadges as $badge) {
            $progress = $this->calculateBadgeProgress($user, $badge);
            
            // Skip badges with invalid targets
            if ($progress['target'] <= 0) {
                continue;
            }
            
            $progressPercentage = min(100, ($progress['current'] / $progress['target']) * 100);
            
            // Only include badges that are not yet completed (progress < 100%)
            if ($progress['current'] > 0 && $progressPercentage < 100) {
                $achievements[] = [
                    'id' => $badge->id,
                    'name' => $badge->name,
                    'icon' => $badge->icon,
                    'color' => $badge->color,
                    'current' => $progress['current'],
                    'target' => $progress['target'],
                    'progress' => $progressPercentage,
                ];
            }
        }

        // Sort by progress (closest to completion first)
        usort($achievements, function ($a, $b) {
            return $b['progress'] <=> $a['progress'];
        });

        return response()->json([
            'data' => array_slice($achievements, 0, 5), // Return top 5
        ]);
    }

    /**
     * Get leaderboard.
     */
    public function getLeaderboard(Request $request): JsonResponse
    {
        $currentUser = $request->user();
        
        // Get top 10 users by points
        $users = User::with('badges')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'points' => $this->calculateTotalPoints($user),
                    'isCurrentUser' => false,
                ];
            })
            ->sortByDesc('points')
            ->take(10)
            ->values()
            ->toArray();

        // Mark current user
        foreach ($users as &$user) {
            if ($user['id'] === $currentUser->id) {
                $user['isCurrentUser'] = true;
            }
        }

        // If current user is not in top 10, add them
        $currentUserInTop10 = collect($users)->contains('id', $currentUser->id);
        if (!$currentUserInTop10) {
            $users[] = [
                'id' => $currentUser->id,
                'name' => $currentUser->name,
                'points' => $this->calculateTotalPoints($currentUser),
                'isCurrentUser' => true,
            ];
        }

        return response()->json([
            'data' => $users,
        ]);
    }

    /**
     * Get user's learning streak calendar.
     */
    public function getStreakCalendar(Request $request): JsonResponse
    {
        $user = $request->user();
        $calendar = [];
        
        // Generate last 30 days
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $hasActivity = $user->progress()
                ->whereDate('last_seen_at', $date->toDateString())
                ->exists();
            
            $calendar[] = [
                'date' => $date->toDateString(),
                'day' => $date->day,
                'has_activity' => $hasActivity,
            ];
        }

        return response()->json([
            'data' => $calendar,
        ]);
    }

    /**
     * Calculate user level based on total points.
     */
    private function calculateUserLevel(User $user): array
    {
        $totalPoints = $this->calculateTotalPoints($user);
        
        $levels = [
            1 => ['name' => 'Principiante', 'description' => 'Hai appena iniziato il tuo percorso di apprendimento'],
            2 => ['name' => 'Apprendista', 'description' => 'Stai facendo progressi costanti'],
            3 => ['name' => 'Studioso', 'description' => 'Hai una buona base di conoscenze'],
            4 => ['name' => 'Esperto', 'description' => 'Sei un esperto nel tuo campo'],
            5 => ['name' => 'Maestro', 'description' => 'Hai raggiunto la padronanza'],
        ];

        // More realistic level thresholds for 100+ courses platform
        $levelThresholds = [0, 500, 1500, 3000, 5000];

        $level = 1;
        for ($i = 1; $i < count($levelThresholds); $i++) {
            if ($totalPoints >= $levelThresholds[$i]) {
                $level = $i + 1;
            }
        }

        return [
            'level' => $level,
            'name' => $levels[$level]['name'],
            'description' => $levels[$level]['description'],
        ];
    }

    /**
     * Calculate total points for a user.
     */
    private function calculateTotalPoints(User $user): int
    {
        $points = 0;
        
        // Points for completed courses (200 points each - main source)
        $points += $user->enrollments()->completed()->count() * 200;
        
        // Points for completed lessons (5 points each)
        $points += $user->progress()->completed()->count() * 5;
        
        // Points for passed quizzes (15 points each)
        $points += $user->attempts()->passed()->distinct('quiz_id')->count() * 15;
        
        // Points for learning hours (10 points per hour)
        $points += round($user->progress()->sum('seconds_watched') / 3600) * 10;
        
        // Points for badges (bonus points - reduced)
        $points += $user->badges()->sum('points') ?? 0;
        
        return $points;
    }

    /**
     * Calculate current level points.
     */
    private function calculateCurrentLevelPoints(User $user): int
    {
        $totalPoints = $this->calculateTotalPoints($user);
        $level = $this->calculateUserLevel($user)['level'];
        
        $levelThresholds = [0, 500, 1500, 3000, 5000];
        $currentLevelStart = $levelThresholds[$level - 1] ?? 0;
        
        return $totalPoints - $currentLevelStart;
    }

    /**
     * Calculate next level points.
     */
    private function calculateNextLevelPoints(User $user): int
    {
        $level = $this->calculateUserLevel($user)['level'];
        $levelThresholds = [0, 500, 1500, 3000, 5000];
        
        if ($level >= 5) {
            return 0; // Max level reached
        }
        
        $nextLevelStart = $levelThresholds[$level] ?? 0;
        $currentLevelStart = $levelThresholds[$level - 1] ?? 0;
        
        return $nextLevelStart - $currentLevelStart;
    }

    /**
     * Calculate points to next level.
     */
    private function calculatePointsToNextLevel(User $user): int
    {
        $totalPoints = $this->calculateTotalPoints($user);
        $level = $this->calculateUserLevel($user)['level'];
        $levelThresholds = [0, 500, 1500, 3000, 5000];
        
        if ($level >= 5) {
            return 0; // Max level reached
        }
        
        $nextLevelStart = $levelThresholds[$level] ?? 0;
        
        return max(0, $nextLevelStart - $totalPoints);
    }

    /**
     * Calculate learning streak.
     */
    private function calculateLearningStreak(User $user): int
    {
        $streak = 0;
        $currentDate = now();
        
        while (true) {
            $hasActivity = $user->progress()
                ->whereDate('last_seen_at', $currentDate->toDateString())
                ->exists();
            
            if (!$hasActivity) {
                break;
            }
            
            $streak++;
            $currentDate = $currentDate->subDay();
        }
        
        return $streak;
    }

    /**
     * Calculate total learning days.
     */
    private function calculateTotalLearningDays(User $user): int
    {
        return $user->progress()
            ->selectRaw('DATE(last_seen_at) as date')
            ->distinct()
            ->count();
    }

    /**
     * Calculate badge progress.
     */
    private function calculateBadgeProgress(User $user, Badge $badge): array
    {
        $criteria = $badge->criteria;
        $current = 0;
        $target = 0;

        if (isset($criteria['courses_completed'])) {
            $current = $user->enrollments()->completed()->count();
            $target = $criteria['courses_completed'];
        } elseif (isset($criteria['lessons_completed'])) {
            $current = $user->progress()->completed()->count();
            $target = $criteria['lessons_completed'];
        } elseif (isset($criteria['hours_watched'])) {
            $current = round($user->progress()->sum('seconds_watched') / 3600);
            $target = $criteria['hours_watched'];
        } elseif (isset($criteria['quizzes_passed'])) {
            $current = $user->attempts()->passed()->distinct('quiz_id')->count();
            $target = $criteria['quizzes_passed'];
        } elseif (isset($criteria['perfect_scores'])) {
            $current = $user->attempts()
                ->whereRaw('score = max_score')
                ->distinct('quiz_id')
                ->count();
            $target = $criteria['perfect_scores'];
        }

        return [
            'current' => $current,
            'target' => $target,
        ];
    }
}
