<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\User;
use App\Models\UserBadge;
use App\Notifications\BadgeEarnedNotification;
use Illuminate\Support\Facades\Log;

class BadgeService
{
    /**
     * Check and award badges for course completion.
     */
    public function checkCourseCompletionBadges(User $user): void
    {
        $completedCourses = $user->enrollments()->completed()->count();
        
        // Check for course completion badges
        $badges = Badge::active()
            ->where('category', 'learning')
            ->whereJsonContains('criteria->courses_completed', $completedCourses)
            ->get();

        foreach ($badges as $badge) {
            $this->awardBadge($user, $badge, "Completato {$completedCourses} corsi");
        }
    }

    /**
     * Check and award badges for lesson completion.
     */
    public function checkLessonCompletionBadges(User $user): void
    {
        $completedLessons = $user->progress()->completed()->count();
        
        // Check for lesson completion badges
        $badges = Badge::active()
            ->where('category', 'learning')
            ->whereJsonContains('criteria->lessons_completed', $completedLessons)
            ->get();

        foreach ($badges as $badge) {
            $this->awardBadge($user, $badge, "Completato {$completedLessons} lezioni");
        }
    }

    /**
     * Check and award badges for quiz completion.
     */
    public function checkQuizCompletionBadges(User $user): void
    {
        $passedQuizzes = $user->attempts()->passed()->distinct('quiz_id')->count();
        
        // Check for quiz completion badges
        $badges = Badge::active()
            ->where('category', 'quiz')
            ->whereJsonContains('criteria->quizzes_passed', $passedQuizzes)
            ->get();

        foreach ($badges as $badge) {
            $this->awardBadge($user, $badge, "Superato {$passedQuizzes} quiz");
        }
    }

    /**
     * Check and award badges for perfect quiz scores.
     */
    public function checkPerfectScoreBadges(User $user): void
    {
        $perfectScores = $user->attempts()
            ->whereRaw('score = max_score')
            ->distinct('quiz_id')
            ->count();
        
        // Check for perfect score badges
        $badges = Badge::active()
            ->where('category', 'quiz')
            ->whereJsonContains('criteria->perfect_scores', $perfectScores)
            ->get();

        foreach ($badges as $badge) {
            $this->awardBadge($user, $badge, "Punteggio perfetto in {$perfectScores} quiz");
        }
    }

    /**
     * Check and award badges for learning hours.
     */
    public function checkLearningHoursBadges(User $user): void
    {
        $totalHours = $user->progress()->sum('seconds_watched') / 3600;
        
        // Check for learning hours badges
        $badges = Badge::active()
            ->where('category', 'learning')
            ->get()
            ->filter(function ($badge) use ($totalHours) {
                $criteria = $badge->criteria;
                return isset($criteria['hours_watched']) && $totalHours >= $criteria['hours_watched'];
            });

        foreach ($badges as $badge) {
            $this->awardBadge($user, $badge, "Completato {$totalHours} ore di apprendimento");
        }
    }

    /**
     * Check and award badges for certificate earning.
     */
    public function checkCertificateBadges(User $user): void
    {
        $totalCertificates = $user->certificates()->count();
        
        // Check for certificate badges
        $badges = Badge::active()
            ->where('category', 'achievement')
            ->whereJsonContains('criteria->certificates_earned', $totalCertificates)
            ->get();

        foreach ($badges as $badge) {
            $this->awardBadge($user, $badge, "Ottenuto {$totalCertificates} certificati");
        }
    }

    /**
     * Check and award badges for track completion.
     */
    public function checkTrackCompletionBadges(User $user): void
    {
        // TODO: Implement when user_tracks table is created
        // $completedTracks = $user->userTracks()->completed()->count();
        
        // Check for track completion badges
        // $badges = Badge::active()
        //     ->where('category', 'learning')
        //     ->whereJsonContains('criteria->tracks_completed', $completedTracks)
        //     ->get();

        // foreach ($badges as $badge) {
        //     $this->awardBadge($user, $badge, "Completato {$completedTracks} track");
        // }
    }

    /**
     * Check and award badges for first login.
     */
    public function checkFirstLoginBadge(User $user): void
    {
        $badge = Badge::active()
            ->where('category', 'milestone')
            ->where('name', 'Primo Accesso')
            ->first();

        if ($badge && !$user->badges()->where('badges.id', $badge->id)->exists()) {
            $this->awardBadge($user, $badge, 'Primo accesso alla piattaforma');
        }
    }

    /**
     * Check and award badges for profile completion.
     */
    public function checkProfileCompletionBadge(User $user): void
    {
        $badge = Badge::active()
            ->where('category', 'milestone')
            ->where('name', 'Profilo Completo')
            ->first();

        if ($badge && !$user->badges()->where('badges.id', $badge->id)->exists()) {
            // Check if profile is complete
            $isComplete = !empty($user->first_name) && 
                         !empty($user->last_name) && 
                         !empty($user->company) && 
                         !empty($user->profession);

            if ($isComplete) {
                $this->awardBadge($user, $badge, 'Profilo completato al 100%');
            }
        }
    }

    /**
     * Award a badge to a user if they don't already have it.
     */
    private function awardBadge(User $user, Badge $badge, string $reason): void
    {
        if ($user->badges()->where('badges.id', $badge->id)->exists()) {
            return; // User already has this badge
        }

        try {
            $user->badges()->attach($badge->id, [
                'awarded_at' => now(),
                'reason' => $reason,
                'metadata' => json_encode([
                    'awarded_via' => 'automatic',
                    'awarded_at_timestamp' => now()->timestamp,
                ]),
            ]);

            Log::info("Badge awarded", [
                'user_id' => $user->id,
                'badge_id' => $badge->id,
                'badge_name' => $badge->name,
                'reason' => $reason,
            ]);

            // Send notification to the user (skip if notification fails)
            try {
                $user->notify(new BadgeEarnedNotification($badge, $reason));
            } catch (\Exception $notificationError) {
                Log::warning("Failed to send badge notification", [
                    'user_id' => $user->id,
                    'badge_id' => $badge->id,
                    'error' => $notificationError->getMessage(),
                ]);
            }

        } catch (\Exception $e) {
            Log::error("Failed to award badge", [
                'user_id' => $user->id,
                'badge_id' => $badge->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Check all badges for a user (useful for migration or manual checks).
     */
    public function checkAllBadges(User $user): void
    {
        $this->checkFirstLoginBadge($user);
        $this->checkProfileCompletionBadge($user);
        $this->checkCourseCompletionBadges($user);
        $this->checkLessonCompletionBadges($user);
        $this->checkQuizCompletionBadges($user);
        $this->checkPerfectScoreBadges($user);
        $this->checkLearningHoursBadges($user);
        $this->checkCertificateBadges($user);
        $this->checkTrackCompletionBadges($user);
    }
}
