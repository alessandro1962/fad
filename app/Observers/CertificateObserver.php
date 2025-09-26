<?php

namespace App\Observers;

use App\Models\Certificate;
use App\Notifications\CertificateEarnedNotification;
use App\Services\BadgeService;

class CertificateObserver
{
    protected BadgeService $badgeService;

    public function __construct(BadgeService $badgeService)
    {
        $this->badgeService = $badgeService;
    }

    /**
     * Handle the Certificate "created" event.
     */
    public function created(Certificate $certificate): void
    {
        // Check for certificate badges
        $this->badgeService->checkCertificateBadges($certificate->user);
        
        // Send notification to the user
        $certificate->user->notify(new CertificateEarnedNotification($certificate));
    }
}
