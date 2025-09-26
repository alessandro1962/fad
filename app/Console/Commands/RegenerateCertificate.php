<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Course;
use App\Models\Certificate;
use App\Services\CertificateService;
use Illuminate\Console\Command;

class RegenerateCertificate extends Command
{
    protected $signature = 'certificate:regenerate {user_email} {course_id}';
    protected $description = 'Regenerate certificate for a user and course using the new template system';

    public function handle()
    {
        $userEmail = $this->argument('user_email');
        $courseId = $this->argument('course_id');

        $user = User::where('email', $userEmail)->first();
        $course = Course::find($courseId);

        if (!$user) {
            $this->error("User with email {$userEmail} not found");
            return 1;
        }

        if (!$course) {
            $this->error("Course with ID {$courseId} not found");
            return 1;
        }

        $this->info("Regenerating certificate for course '{$course->title}' and user '{$user->first_name} {$user->last_name}'");

        // Find existing certificate
        $existingCertificate = Certificate::where('user_id', $user->id)
            ->where('kind', 'course')
            ->where('ref_id', $course->id)
            ->first();

        if (!$existingCertificate) {
            $this->error("No existing certificate found for this user and course");
            return 1;
        }

        $this->info("Found existing certificate: {$existingCertificate->public_uid}");

        // Check if course has a specific template
        $template = \App\Models\CertificateTemplate::where('is_active', true)
            ->where('course_id', $course->id)
            ->first();

        if ($template) {
            $this->info("Using course-specific template: {$template->name}");
        } else {
            $this->info("No course-specific template found, will use default template");
        }

        // Update certificate with correct hours
        $hoursTotal = $course->duration_minutes ? round($course->duration_minutes / 60, 1) : 1;
        $existingCertificate->update([
            'hours_total' => $hoursTotal,
            'metadata' => array_merge($existingCertificate->metadata ?? [], [
                'regenerated_at' => now()->toISOString(),
                'template_id' => $template->id ?? null,
            ]),
        ]);

        $this->info("Updated certificate hours to: {$hoursTotal}");

        // Regenerate PDF using the new template system
        try {
            $certificateService = app(CertificateService::class);
            
            // Get template (course-specific or default)
            if (!$template) {
                $template = \App\Models\CertificateTemplate::where('is_active', true)->first();
                if (!$template) {
                    $this->error("No active template found");
                    return 1;
                }
            }
            
            $pdfPath = $certificateService->generatePdf($existingCertificate, $template, $user, $course);
            
            $this->info("Certificate PDF regenerated successfully!");
            $this->info("PDF path: {$pdfPath}");
            $this->info("Public UID: {$existingCertificate->public_uid}");
            
        } catch (\Exception $e) {
            $this->error("Failed to regenerate certificate PDF: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}