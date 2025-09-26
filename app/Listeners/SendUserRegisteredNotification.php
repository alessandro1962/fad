<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;

class SendUserRegisteredNotification
{
    public function handle(UserRegistered $event): void
    {
        $user = $event->user;
        $temporaryPassword = $event->temporaryPassword;
        
        // Controlla se l'email è già stata inviata per questo utente
        $cacheKey = "user_registered_email_sent_{$user->id}";
        if (Cache::has($cacheKey)) {
            \Log::info("Email già inviata per utente {$user->email}, salto l'invio");
            return;
        }
        
        \Log::info("SendUserRegisteredNotification: Invio email a {$user->email} con password: {$temporaryPassword}");
        
        try {
            // Usa Laravel Mail invece dell'API SendGrid diretta
            Mail::send('emails.user-registered', [
                'user' => $user,
                'temporaryPassword' => $temporaryPassword
            ], function ($message) use ($user) {
                $message->to($user->email, $user->first_name . ' ' . $user->last_name)
                        ->subject('Benvenuto in Campus Digitale Forma');
            });
            
            // Marca l'email come inviata per 5 minuti
            Cache::put($cacheKey, true, 300);
            
            \Log::info("Email inviata con successo tramite Laravel Mail");
            
        } catch (\Exception $e) {
            \Log::error("Errore nell'invio email: " . $e->getMessage());
        }
    }
}
