<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\UserBadge;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BadgeController extends Controller
{
    /**
     * Get all available badges.
     */
    public function index(Request $request): JsonResponse
    {
        $badges = Badge::active()
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->map(function ($badge) {
                return [
                    'id' => $badge->id,
                    'name' => $badge->name,
                    'description' => $badge->description,
                    'icon' => $badge->icon,
                    'color' => $badge->color,
                    'category' => $badge->category,
                    'rarity' => $badge->rarity,
                    'points' => $badge->points,
                    'criteria' => $badge->criteria,
                ];
            });

        return response()->json([
            'data' => $badges,
        ]);
    }

    /**
     * Get user's badges.
     */
    public function userBadges(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $userBadges = $user->badges()
            ->withPivot('awarded_at', 'reason', 'metadata')
            ->orderBy('user_badges.awarded_at', 'desc')
            ->get()
            ->map(function ($badge) {
                return [
                    'id' => $badge->id,
                    'name' => $badge->name,
                    'description' => $badge->description,
                    'icon' => $badge->icon,
                    'color' => $badge->color,
                    'category' => $badge->category,
                    'rarity' => $badge->rarity,
                    'points' => $badge->points,
                    'awarded_at' => $badge->pivot->awarded_at,
                    'reason' => $badge->pivot->reason,
                    'metadata' => $badge->pivot->metadata,
                ];
            });

        // Get user's badge statistics
        $totalBadges = $userBadges->count();
        $totalPoints = $userBadges->sum('points');
        $categories = $userBadges->groupBy('category')->map->count();
        $rarities = $userBadges->groupBy('rarity')->map->count();

        return response()->json([
            'data' => [
                'badges' => $userBadges,
                'statistics' => [
                    'total_badges' => $totalBadges,
                    'total_points' => $totalPoints,
                    'categories' => $categories,
                    'rarities' => $rarities,
                ],
            ],
        ]);
    }

    /**
     * Get user's recent badges.
     */
    public function recent(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $recentBadges = $user->badges()
            ->withPivot('awarded_at', 'reason', 'metadata')
            ->orderBy('user_badges.awarded_at', 'desc')
            ->limit(6)
            ->get()
            ->map(function ($badge) {
                return [
                    'id' => $badge->id,
                    'name' => $badge->name,
                    'description' => $badge->description,
                    'icon' => $badge->icon,
                    'color' => $badge->color,
                    'category' => $badge->category,
                    'awarded_at' => $badge->pivot->awarded_at,
                    'reason' => $badge->pivot->reason,
                ];
            });

        return response()->json([
            'data' => $recentBadges,
        ]);
    }

    /**
     * Get user's badge progress (badges not yet earned).
     */
    public function progress(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $earnedBadgeIds = $user->badges()->pluck('badges.id');
        
        $availableBadges = Badge::active()
            ->whereNotIn('id', $earnedBadgeIds)
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->map(function ($badge) use ($user) {
                $progress = $this->calculateBadgeProgress($badge, $user);
                
                return [
                    'id' => $badge->id,
                    'name' => $badge->name,
                    'description' => $badge->description,
                    'icon' => $badge->icon,
                    'color' => $badge->color,
                    'category' => $badge->category,
                    'rarity' => $badge->rarity,
                    'points' => $badge->points,
                    'criteria' => $badge->criteria,
                    'progress' => $progress,
                ];
            });

        return response()->json([
            'data' => $availableBadges,
        ]);
    }

    /**
     * Get a specific badge.
     */
    public function show(Request $request, Badge $badge): JsonResponse
    {
        $user = $request->user();
        
        $userBadge = $user->badges()
            ->where('badges.id', $badge->id)
            ->withPivot('awarded_at', 'reason', 'metadata')
            ->first();

        $badgeData = [
            'id' => $badge->id,
            'name' => $badge->name,
            'description' => $badge->description,
            'icon' => $badge->icon,
            'color' => $badge->color,
            'category' => $badge->category,
            'rarity' => $badge->rarity,
            'points' => $badge->points,
            'criteria' => $badge->criteria,
            'earned' => $userBadge !== null,
        ];

        if ($userBadge) {
            $badgeData['awarded_at'] = $userBadge->pivot->awarded_at->toISOString();
            $badgeData['reason'] = $userBadge->pivot->reason;
            $badgeData['metadata'] = $userBadge->pivot->metadata;
        } else {
            $badgeData['progress'] = $this->calculateBadgeProgress($badge, $user);
        }

        return response()->json([
            'data' => $badgeData,
        ]);
    }

    /**
     * Award a badge to a user (admin only).
     */
    public function award(Request $request, Badge $badge): JsonResponse
    {
        $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'reason' => ['nullable', 'string', 'max:255'],
            'metadata' => ['nullable', 'array'],
        ]);

        $user = \App\Models\User::find($request->user_id);
        
        // Check if user already has this badge
        if ($user->badges()->where('badges.id', $badge->id)->exists()) {
            return response()->json([
                'message' => 'L\'utente ha già questo badge.',
            ], 409);
        }

        // Award the badge
        $user->badges()->attach($badge->id, [
            'awarded_at' => now(),
            'reason' => $request->reason ?? 'Badge assegnato manualmente',
            'metadata' => $request->metadata ?? [],
        ]);

        return response()->json([
            'message' => 'Badge assegnato con successo.',
            'data' => [
                'badge_id' => $badge->id,
                'badge_name' => $badge->name,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'awarded_at' => now()->toISOString(),
            ],
        ]);
    }

    /**
     * Calculate progress towards earning a badge.
     */
    private function calculateBadgeProgress(Badge $badge, $user): array
    {
        $criteria = $badge->criteria;
        $progress = [];

        switch ($badge->category) {
            case 'learning':
                if (isset($criteria['courses_completed'])) {
                    $completed = $user->enrollments()->completed()->count();
                    $progress['courses_completed'] = [
                        'current' => $completed,
                        'target' => $criteria['courses_completed'],
                        'percentage' => min(100, round(($completed / $criteria['courses_completed']) * 100)),
                    ];
                }
                
                if (isset($criteria['lessons_completed'])) {
                    $completed = $user->progress()->completed()->count();
                    $progress['lessons_completed'] = [
                        'current' => $completed,
                        'target' => $criteria['lessons_completed'],
                        'percentage' => min(100, round(($completed / $criteria['lessons_completed']) * 100)),
                    ];
                }
                
                if (isset($criteria['hours_watched'])) {
                    $hours = $user->progress()->sum('seconds_watched') / 3600;
                    $progress['hours_watched'] = [
                        'current' => round($hours, 1),
                        'target' => $criteria['hours_watched'],
                        'percentage' => min(100, round(($hours / $criteria['hours_watched']) * 100)),
                    ];
                }
                break;

            case 'quiz':
                if (isset($criteria['quizzes_passed'])) {
                    $passed = $user->attempts()->passed()->distinct('quiz_id')->count();
                    $progress['quizzes_passed'] = [
                        'current' => $passed,
                        'target' => $criteria['quizzes_passed'],
                        'percentage' => min(100, round(($passed / $criteria['quizzes_passed']) * 100)),
                    ];
                }
                
                if (isset($criteria['perfect_scores'])) {
                    $perfect = $user->attempts()
                        ->whereRaw('score = max_score')
                        ->distinct('quiz_id')
                        ->count();
                    $progress['perfect_scores'] = [
                        'current' => $perfect,
                        'target' => $criteria['perfect_scores'],
                        'percentage' => min(100, round(($perfect / $criteria['perfect_scores']) * 100)),
                    ];
                }
                break;

            case 'streak':
                if (isset($criteria['daily_streak'])) {
                    // This would require implementing a daily activity tracking system
                    $progress['daily_streak'] = [
                        'current' => 0, // Placeholder
                        'target' => $criteria['daily_streak'],
                        'percentage' => 0,
                    ];
                }
                break;
        }

        return $progress;
    }
}