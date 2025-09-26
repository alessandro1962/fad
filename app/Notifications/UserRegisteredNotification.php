<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class UserRegisteredNotification extends Notification
{
    use Queueable;

    protected $user;
    protected $temporaryPassword;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, ?string $temporaryPassword = null)
    {
        $this->user = $user;
        $this->temporaryPassword = $temporaryPassword;
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
            ->subject('Benvenuto su Campus Digitale Forma! 🎉')
            ->view('emails.user-registered', [
                'user' => $this->user,
                'temporaryPassword' => $this->temporaryPassword
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
            'type' => 'user_registered',
            'title' => 'Benvenuto su Campus Digitale Forma!',
            'message' => 'Il tuo account è stato creato con successo. Inizia subito il tuo percorso di apprendimento!',
            'user_id' => $this->user->id,
            'action_url' => url('/dashboard'),
            'action_text' => 'Accedi alla Dashboard'
        ];
    }
}
