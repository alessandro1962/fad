<?php

namespace App\Services;

use SendGrid\Mail\Mail;
use SendGrid;
use SendGrid\Mail\Personalization;
use SendGrid\Mail\Substitution;
use Illuminate\Support\Facades\Log;

class SendGridService
{
    protected $sendgrid;
    protected $fromEmail;
    protected $fromName;

    public function __construct()
    {
        $this->sendgrid = new SendGrid(config('sendgrid.api_key'));
        $this->fromEmail = config('sendgrid.from.email');
        $this->fromName = config('sendgrid.from.name');
    }

    /**
     * Invia email con tracking avanzato
     */
    public function sendEmail($to, $subject, $template, $data = [], $category = 'general')
    {
        try {
            $email = new Mail();
            $email->setFrom($this->fromEmail, $this->fromName);
            $email->setSubject($subject);
            $email->addTo($to);
            
            // Aggiungi template HTML
            $email->addContent('text/html', $template);
            
            // Aggiungi categoria per tracking
            $email->addCategory($category);
            
            // Abilita tracking
            $email->setTrackingSettings([
                'click_tracking' => [
                    'enable' => true,
                    'enable_text' => true
                ],
                'open_tracking' => [
                    'enable' => true,
                    'substitution_tag' => '%open-track%'
                ],
                'subscription_tracking' => [
                    'enable' => true,
                    'substitution_tag' => '%unsubscribe%'
                ]
            ]);
            
            // Aggiungi variabili personalizzate
            if (!empty($data)) {
                $personalization = new Personalization();
                $personalization->addTo($to);
                
                foreach ($data as $key => $value) {
                    $personalization->addSubstitution(
                        new Substitution("%{$key}%", $value)
                    );
                }
                
                $email->addPersonalization($personalization);
            }
            
            // Invia email
            $response = $this->sendgrid->send($email);
            
            // Log del risultato
            Log::info('SendGrid email sent', [
                'to' => $to,
                'subject' => $subject,
                'category' => $category,
                'status_code' => $response->statusCode(),
                'response' => $response->body()
            ]);
            
            return [
                'success' => $response->statusCode() >= 200 && $response->statusCode() < 300,
                'status_code' => $response->statusCode(),
                'response' => $response->body()
            ];
            
        } catch (\Exception $e) {
            Log::error('SendGrid email failed', [
                'to' => $to,
                'subject' => $subject,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Invia email di benvenuto
     */
    public function sendWelcomeEmail($user)
    {
        $template = view('emails.user-registered', [
            'user' => $user,
            'trackingId' => 'welcome_' . $user->id
        ])->render();
        
        return $this->sendEmail(
            $user->email,
            'Benvenuto su Campus Digitale Forma! 🎉',
            $template,
            [
                'user_name' => $user->name,
                'user_email' => $user->email
            ],
            'user_registration'
        );
    }

    /**
     * Invia notifica completamento modulo
     */
    public function sendModuleCompletedEmail($user, $course, $module, $progressPercentage)
    {
        $template = view('emails.module-completed', [
            'user' => $user,
            'course' => $course,
            'module' => $module,
            'progressPercentage' => $progressPercentage,
            'trackingId' => 'module_' . $module->id . '_' . $user->id
        ])->render();
        
        return $this->sendEmail(
            $user->email,
            'Modulo completato: ' . $module->title . ' 🎯',
            $template,
            [
                'user_name' => $user->name,
                'course_title' => $course->title,
                'module_title' => $module->title,
                'progress_percentage' => $progressPercentage
            ],
            'module_completed'
        );
    }

    /**
     * Invia notifica completamento corso
     */
    public function sendCourseCompletedEmail($user, $course)
    {
        $template = view('emails.course-completed', [
            'user' => $user,
            'course' => $course,
            'trackingId' => 'course_' . $course->id . '_' . $user->id
        ])->render();
        
        return $this->sendEmail(
            $user->email,
            '🎉 Corso completato: ' . $course->title,
            $template,
            [
                'user_name' => $user->name,
                'course_title' => $course->title
            ],
            'course_completed'
        );
    }

    /**
     * Testa la connessione SendGrid
     */
    public function testConnection()
    {
        try {
            $email = new Mail();
            $email->setFrom($this->fromEmail, $this->fromName);
            $email->setSubject('Test connessione SendGrid');
            $email->addTo($this->fromEmail);
            $email->addContent('text/plain', 'Test di connessione SendGrid completato con successo!');
            
            $response = $this->sendgrid->send($email);
            
            return [
                'success' => $response->statusCode() >= 200 && $response->statusCode() < 300,
                'status_code' => $response->statusCode(),
                'response' => $response->body()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
