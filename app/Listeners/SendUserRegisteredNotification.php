<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Notifications\UserRegisteredNotification;
class SendUserRegisteredNotification
{

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        \Log::info('SendUserRegisteredNotification: Invio email a ' . $event->user->email . ' con password: ' . $event->temporaryPassword);
        // Invia notifica di benvenuto all'utente appena registrato
        $event->user->notify(new UserRegisteredNotification($event->user, $event->temporaryPassword));
        \Log::info('SendUserRegisteredNotification: Email inviata con successo');
    }
}
