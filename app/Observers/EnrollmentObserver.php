<?php

namespace App\Observers;

use App\Models\Enrollment;
use App\Notifications\CourseCompletedNotification;
use App\Services\BadgeService;

class EnrollmentObserver
{
    protected BadgeService $badgeService;

    public function __construct(BadgeService $badgeService)
    {
        $this->badgeService = $badgeService;
    }

    /**
     * Handle the Enrollment "updated" event.
     */
    public function updated(Enrollment $enrollment): void
    {
        // Check if enrollment was just completed
        if ($enrollment->wasChanged('status') && $enrollment->status === 'completed') {
            $this->badgeService->checkCourseCompletionBadges($enrollment->user);
            
            // Send notification to the user
            $enrollment->user->notify(new CourseCompletedNotification($enrollment->course));
        }
    }
}
