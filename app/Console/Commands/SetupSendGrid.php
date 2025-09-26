<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use SendGrid\Mail\Mail;
use SendGrid;

class SetupSendGrid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendgrid:setup {--api-key= : SendGrid API Key} {--from-email= : Email mittente} {--from-name= : Nome mittente}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configura SendGrid per il sistema di mailing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Configurazione SendGrid per Campus Digitale Forma');
        $this->line('');
        
        // Raccogli informazioni
        $apiKey = $this->option('api-key') ?: $this->ask('Inserisci la SendGrid API Key');
        $fromEmail = $this->option('from-email') ?: $this->ask('Email mittente', 'noreply@campusdigitale.it');
        $fromName = $this->option('from-name') ?: $this->ask('Nome mittente', 'Campus Digitale Forma');
        
        if (!$apiKey) {
            $this->error('❌ API Key richiesta!');
            return 1;
        }
        
        // Testa la connessione
        $this->info('🔍 Test connessione SendGrid...');
        if (!$this->testSendGridConnection($apiKey, $fromEmail, $fromName)) {
            $this->error('❌ Connessione SendGrid fallita! Verifica l\'API Key.');
            return 1;
        }
        
        // Aggiorna file .env
        $this->info('📝 Aggiornamento configurazione...');
        $this->updateEnvFile($apiKey, $fromEmail, $fromName);
        
        // Crea file di configurazione
        $this->createSendGridConfig();
        
        $this->line('');
        $this->info('✅ SendGrid configurato con successo!');
        $this->line('');
        $this->line('📧 Configurazione:');
        $this->line("   • Host: smtp.sendgrid.net");
        $this->line("   • Port: 587");
        $this->line("   • Encryption: tls");
        $this->line("   • From: {$fromEmail} ({$fromName})");
        $this->line('');
        $this->line('🧪 Testa il sistema con: php artisan mailing:test');
        
        return 0;
    }
    
    private function testSendGridConnection($apiKey, $fromEmail, $fromName)
    {
        try {
            $sendgrid = new SendGrid($apiKey);
            
            $email = new Mail();
            $email->setFrom($fromEmail, $fromName);
            $email->setSubject('Test connessione SendGrid - Campus Digitale Forma');
            $email->addTo($fromEmail, 'Test');
            $email->addContent('text/plain', 'Test di connessione SendGrid completato con successo!');
            
            $response = $sendgrid->send($email);
            
            if ($response->statusCode() >= 200 && $response->statusCode() < 300) {
                $this->line('✅ Connessione SendGrid verificata');
                return true;
            } else {
                $this->error("❌ Errore SendGrid: {$response->statusCode()}");
                return false;
            }
        } catch (\Exception $e) {
            $this->error("❌ Errore: {$e->getMessage()}");
            return false;
        }
    }
    
    private function updateEnvFile($apiKey, $fromEmail, $fromName)
    {
        $envPath = base_path('.env');
        
        if (!File::exists($envPath)) {
            $this->error('❌ File .env non trovato!');
            return;
        }
        
        $envContent = File::get($envPath);
        
        // Aggiorna o aggiungi le variabili SendGrid
        $envUpdates = [
            'MAIL_MAILER=smtp',
            'MAIL_HOST=smtp.sendgrid.net',
            'MAIL_PORT=587',
            'MAIL_USERNAME=apikey',
            "MAIL_PASSWORD={$apiKey}",
            'MAIL_ENCRYPTION=tls',
            "MAIL_FROM_ADDRESS={$fromEmail}",
            "MAIL_FROM_NAME=\"{$fromName}\"",
            'QUEUE_CONNECTION=database'
        ];
        
        foreach ($envUpdates as $update) {
            $key = explode('=', $update)[0];
            $pattern = "/^{$key}=.*/m";
            
            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $update, $envContent);
            } else {
                $envContent .= "\n{$update}";
            }
        }
        
        File::put($envPath, $envContent);
        $this->line('✅ File .env aggiornato');
    }
    
    private function createSendGridConfig()
    {
        $configPath = config_path('sendgrid.php');
        
        $config = '<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SendGrid Configuration
    |--------------------------------------------------------------------------
    |
    | Configurazione per SendGrid email service
    |
    */
    
    \'api_key\' => env(\'SENDGRID_API_KEY\'),
    
    \'from\' => [
        \'email\' => env(\'MAIL_FROM_ADDRESS\', \'noreply@campusdigitale.it\'),
        \'name\' => env(\'MAIL_FROM_NAME\', \'Campus Digitale Forma\'),
    ],
    
    \'tracking\' => [
        \'click_tracking\' => true,
        \'open_tracking\' => true,
        \'subscription_tracking\' => true,
    ],
    
    \'categories\' => [
        \'user_registration\',
        \'module_completed\',
        \'course_completed\',
        \'quiz_completed\',
        \'course_assigned\',
        \'course_reminder\',
    ],
];';
        
        File::put($configPath, $config);
        $this->line('✅ File di configurazione SendGrid creato');
    }
}
