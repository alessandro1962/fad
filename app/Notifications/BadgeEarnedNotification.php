<?php

namespace App\Notifications;

use App\Models\Badge;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BadgeEarnedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Badge $badge;
    protected string $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(Badge $badge, string $reason)
    {
        $this->badge = $badge;
        $this->reason = $reason;
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
            ->subject('🎉 Hai ottenuto un nuovo badge!')
            ->greeting('Congratulazioni!')
            ->line("Hai appena ottenuto il badge **{$this->badge->name}**!")
            ->line($this->badge->description)
            ->line("Motivo: {$this->reason}")
            ->line("Punti guadagnati: {$this->badge->points}")
            ->action('Visualizza i tuoi badge', url('/dashboard/badges'))
            ->line('Continua così! Il tuo impegno sta dando i suoi frutti.')
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
            'type' => 'badge_earned',
            'badge_id' => $this->badge->id,
            'badge_name' => $this->badge->name,
            'badge_icon' => $this->badge->icon,
            'badge_color' => $this->badge->color,
            'badge_points' => $this->badge->points,
            'reason' => $this->reason,
            'message' => "Hai ottenuto il badge {$this->badge->name}!",
        ];
    }
}
