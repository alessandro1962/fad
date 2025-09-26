<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Course;
use App\Services\CertificateService;
use Illuminate\Console\Command;

class CompleteCourse extends Command
{
    protected $signature = 'course:complete {user_email} {course_id}';
    protected $description = 'Complete a course for a user and generate certificate';

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

        $this->info("Completing course '{$course->title}' for user '{$user->first_name} {$user->last_name}'");

        // Check if user is enrolled
        $enrollment = $user->enrollments()->where('course_id', $course->id)->first();
        if (!$enrollment) {
            $this->error("User is not enrolled in this course");
            return 1;
        }

        // Mark all lessons as completed
        $lessons = $course->modules()->with('lessons')->get()->pluck('lessons')->flatten();
        $this->info("Marking {$lessons->count()} lessons as completed...");

        foreach ($lessons as $lesson) {
            $progress = $user->progress()->where('lesson_id', $lesson->id)->first();
            if (!$progress) {
                $user->progress()->create([
                    'lesson_id' => $lesson->id,
                    'completed' => true,
                    'completed_at' => now(),
                ]);
            } else {
                $progress->update([
                    'completed' => true,
                    'completed_at' => now(),
                ]);
            }
        }

        // Update enrollment to completed
        $enrollment->update([
            'progress_percentage' => 100,
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $this->info("Course marked as completed!");

        // Generate certificate
        try {
            $certificateService = app(CertificateService::class);
            $certificate = $certificateService->generateCertificate($user, 'course', $course->id);
            $this->info("Certificate generated with ID: {$certificate->id}");
            $this->info("Public UID: {$certificate->public_uid}");
        } catch (\Exception $e) {
            $this->error("Failed to generate certificate: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}