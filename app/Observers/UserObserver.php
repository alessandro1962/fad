<?php

namespace App\Observers;

use App\Models\User;
use App\Services\BadgeService;

class UserObserver
{
    protected BadgeService $badgeService;

    public function __construct(BadgeService $badgeService)
    {
        $this->badgeService = $badgeService;
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Check for first login badge
        $this->badgeService->checkFirstLoginBadge($user);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Check for profile completion badge
        $this->badgeService->checkProfileCompletionBadge($user);
    }
}
