<?php

namespace App\Observers;

use App\Models\Progress;
use App\Services\BadgeService;

class ProgressObserver
{
    protected BadgeService $badgeService;

    public function __construct(BadgeService $badgeService)
    {
        $this->badgeService = $badgeService;
    }

    /**
     * Handle the Progress "created" event.
     */
    public function created(Progress $progress): void
    {
        // Check for lesson completion badges
        if ($progress->completed) {
            $this->badgeService->checkLessonCompletionBadges($progress->user);
            $this->badgeService->checkLearningHoursBadges($progress->user);
        }
    }

    /**
     * Handle the Progress "updated" event.
     */
    public function updated(Progress $progress): void
    {
        // Check if progress was just marked as completed
        if ($progress->wasChanged('completed') && $progress->completed) {
            $this->badgeService->checkLessonCompletionBadges($progress->user);
            $this->badgeService->checkLearningHoursBadges($progress->user);
        }
    }
}
