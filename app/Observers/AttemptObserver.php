<?php

namespace App\Observers;

use App\Models\Attempt;
use App\Services\BadgeService;

class AttemptObserver
{
    protected BadgeService $badgeService;

    public function __construct(BadgeService $badgeService)
    {
        $this->badgeService = $badgeService;
    }

    /**
     * Handle the Attempt "created" event.
     */
    public function created(Attempt $attempt): void
    {
        // Check for quiz completion badges
        if ($attempt->passed) {
            $this->badgeService->checkQuizCompletionBadges($attempt->user);
            $this->badgeService->checkPerfectScoreBadges($attempt->user);
        }
    }

    /**
     * Handle the Attempt "updated" event.
     */
    public function updated(Attempt $attempt): void
    {
        // Check if attempt was just marked as passed
        if ($attempt->wasChanged('passed') && $attempt->passed) {
            $this->badgeService->checkQuizCompletionBadges($attempt->user);
            $this->badgeService->checkPerfectScoreBadges($attempt->user);
        }
    }
}
