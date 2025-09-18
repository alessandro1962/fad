<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SendGrid Configuration
    |--------------------------------------------------------------------------
    |
    | Configurazione per SendGrid email service
    |
    */
    
    'api_key' => env('MAIL_PASSWORD'),
    
    'from' => [
        'email' => env('MAIL_FROM_ADDRESS', 'noreply@campusdigitale.it'),
        'name' => env('MAIL_FROM_NAME', 'Campus Digitale Forma'),
    ],
    
    'tracking' => [
        'click_tracking' => true,
        'open_tracking' => true,
        'subscription_tracking' => true,
    ],
    
    'categories' => [
        'user_registration',
        'module_completed',
        'course_completed',
        'quiz_completed',
        'course_assigned',
        'course_reminder',
    ],
];
