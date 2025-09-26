<?php

namespace App\Observers;

use App\Models\Enrollment;
use App\Notifications\CourseCompletedNotification;
use App\Services\BadgeService;
use App\Services\CertificateService;

class EnrollmentObserver
{
    protected BadgeService $badgeService;
    protected CertificateService $certificateService;

    public function __construct(BadgeService $badgeService, CertificateService $certificateService)
    {
        $this->badgeService = $badgeService;
        $this->certificateService = $certificateService;
    }

    /**
     * Handle the Enrollment "updated" event.
     */
    public function updated(Enrollment $enrollment): void
    {
        // Check if enrollment was just completed
        if ($enrollment->wasChanged('status') && $enrollment->status === 'completed') {
            $this->badgeService->checkCourseCompletionBadges($enrollment->user);
            
            // Generate certificate for completed course
            $this->generateCertificate($enrollment->user, $enrollment->course);
            
            // Send notification to the user
            $enrollment->user->notify(new CourseCompletedNotification($enrollment->course));
        }
    }

    /**
     * Generate certificate for completed course.
     */
    private function generateCertificate($user, $course)
    {
        try {
            // Check if certificate already exists
            $existingCertificate = $user->certificates()
                ->where('kind', 'course')
                ->where('ref_id', $course->id)
                ->first();

            if (!$existingCertificate) {
                $certificate = $this->certificateService->generateCertificate($user, 'course', $course->id);
                \Log::info("Certificate generated for user {$user->id} and course {$course->id}", [
                    'certificate_id' => $certificate->id,
                    'public_uid' => $certificate->public_uid
                ]);
            }
        } catch (\Exception $e) {
            \Log::error("Failed to generate certificate for user {$user->id} and course {$course->id}: " . $e->getMessage());
        }
    }
}
