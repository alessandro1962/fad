<?php

namespace App\Notifications;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseEnrollmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Course $course;
    protected Enrollment $enrollment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Course $course, Enrollment $enrollment)
    {
        $this->course = $course;
        $this->enrollment = $enrollment;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $url = url('/corso/' . $this->course->id);

        return (new MailMessage)
            ->subject('Sei stato iscritto al corso: ' . $this->course->title)
            ->greeting('Ciao ' . $notifiable->name . '!')
            ->line('Siamo felici di informarti che sei stato iscritto al corso:')
            ->line('**' . $this->course->title . '**')
            ->line($this->course->description)
            ->line('**Dettagli del corso:**')
            ->line('• Livello: ' . $this->course->level)
            ->line('• Durata: ' . $this->course->duration_minutes . ' minuti')
            ->line('• Data inizio: ' . $this->enrollment->started_at->format('d/m/Y'))
            ->when($this->enrollment->expires_at, function ($message) {
                return $message->line('• Data scadenza: ' . $this->enrollment->expires_at->format('d/m/Y'));
            })
            ->action('Inizia il Corso', $url)
            ->line('Buon apprendimento!')
            ->salutation('Il Team di Campus Digitale Forma');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'course_enrollment',
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
            'enrollment_id' => $this->enrollment->id,
            'started_at' => $this->enrollment->started_at->toISOString(),
            'expires_at' => $this->enrollment->expires_at?->toISOString(),
            'message' => 'Sei stato iscritto al corso: ' . $this->course->title,
        ];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'course_enrollment',
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
            'enrollment_id' => $this->enrollment->id,
            'started_at' => $this->enrollment->started_at->toISOString(),
            'expires_at' => $this->enrollment->expires_at?->toISOString(),
            'message' => 'Sei stato iscritto al corso: ' . $this->course->title,
            'action_url' => '/corso/' . $this->course->id,
        ];
    }
}
