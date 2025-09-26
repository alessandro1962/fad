<?php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Course $course;

    /**
     * Create a new notification instance.
     */
    public function __construct(Course $course)
    {
        $this->course = $course;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🎉 Corso completato: ' . $this->course->title)
            ->view('emails.course-completed', [
                'user' => $notifiable,
                'course' => $this->course
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'course_completed',
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
            'course_description' => $this->course->description,
            'course_image' => $this->course->image_url,
            'message' => "Hai completato il corso {$this->course->title}!",
        ];
    }
}
