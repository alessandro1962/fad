<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Models\Course;
use App\Models\Module;

class ModuleCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;
    protected $course;
    protected $module;
    protected $progressPercentage;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, Course $course, Module $module, int $progressPercentage)
    {
        $this->user = $user;
        $this->course = $course;
        $this->module = $module;
        $this->progressPercentage = $progressPercentage;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Modulo completato: ' . $this->module->title . ' 🎯')
            ->view('emails.module-completed', [
                'user' => $this->user,
                'course' => $this->course,
                'module' => $this->module,
                'progressPercentage' => $this->progressPercentage
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
            'type' => 'module_completed',
            'title' => 'Modulo completato!',
            'message' => "Hai completato il modulo '{$this->module->title}' del corso '{$this->course->title}'",
            'user_id' => $this->user->id,
            'course_id' => $this->course->id,
            'module_id' => $this->module->id,
            'progress_percentage' => $this->progressPercentage,
            'action_url' => url('/corso/' . $this->course->id),
            'action_text' => 'Continua il Corso'
        ];
    }
}
