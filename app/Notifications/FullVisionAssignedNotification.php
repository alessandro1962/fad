<?php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FullVisionAssignedNotification extends Notification
{
    use Queueable;

    protected array $courses;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $courses)
    {
        $this->courses = $courses;
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
        $url = url('/i-miei-corsi');
        
        $message = (new MailMessage)
            ->subject('🎉 Benvenuto in Campus Digitale Forma - Accesso Full Vision Attivato!')
            ->greeting('Ciao ' . $notifiable->name . '!')
            ->line('🎊 **Congratulazioni!** Ti è stato assegnato l\'accesso **Full Vision** alla nostra piattaforma di formazione.')
            ->line('Questo significa che hai accesso a **tutti i corsi** del nostro catalogo, inclusi:')
            ->line('');

        // Aggiungi la lista dei corsi
        foreach ($this->courses as $course) {
            $message->line('• **' . $course->title . '** - ' . $course->level . ' (' . $course->duration_minutes . ' min)');
        }

        $message->line('')
            ->line('**Cosa puoi fare ora:**')
            ->line('• Accedi alla tua area personale per vedere tutti i corsi disponibili')
            ->line('• Inizia da qualsiasi corso ti interessi di più')
            ->line('• I tuoi progressi verranno tracciati automaticamente')
            ->line('• Riceverai attestati per ogni corso completato')
            ->line('')
            ->action('Vai ai Miei Corsi', $url)
            ->line('Buon apprendimento e benvenuto nella community di Campus Digitale Forma!')
            ->salutation('Il Team di Campus Digitale Forma');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'full_vision_assigned',
            'courses_count' => count($this->courses),
            'courses' => collect($this->courses)->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'level' => $course->level,
                    'duration_minutes' => $course->duration_minutes,
                ];
            })->toArray(),
            'message' => 'Ti è stato assegnato l\'accesso Full Vision con ' . count($this->courses) . ' corsi disponibili',
        ];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'full_vision_assigned',
            'courses_count' => count($this->courses),
            'courses' => collect($this->courses)->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'level' => $course->level,
                    'duration_minutes' => $course->duration_minutes,
                ];
            })->toArray(),
            'message' => 'Ti è stato assegnato l\'accesso Full Vision con ' . count($this->courses) . ' corsi disponibili',
            'action_url' => '/i-miei-corsi',
        ];
    }
}
