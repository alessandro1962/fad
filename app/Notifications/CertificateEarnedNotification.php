<?php

namespace App\Notifications;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateEarnedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Certificate $certificate;

    /**
     * Create a new notification instance.
     */
    public function __construct(Certificate $certificate)
    {
        $this->certificate = $certificate;
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
            ->subject('🎓 Hai ottenuto un nuovo certificato!')
            ->greeting('Congratulazioni!')
            ->line("Hai completato con successo e ottenuto il certificato:")
            ->line("**{$this->certificate->title}**")
            ->line("Il tuo certificato è ora disponibile per il download e la condivisione.")
            ->action('Visualizza i tuoi certificati', url('/dashboard/certificates'))
            ->action('Scarica il certificato', $this->certificate->publicUrl)
            ->line('Continua così! Il tuo percorso di apprendimento sta progredendo magnificamente.')
            ->salutation('Il team di Campus Digitale Forma');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'certificate_earned',
            'certificate_id' => $this->certificate->id,
            'certificate_title' => $this->certificate->title,
            'certificate_type' => $this->certificate->reference_type,
            'reference_title' => $this->certificate->reference->title ?? 'N/A',
            'public_url' => $this->certificate->publicUrl,
            'message' => "Hai ottenuto il certificato {$this->certificate->title}!",
        ];
    }
}
