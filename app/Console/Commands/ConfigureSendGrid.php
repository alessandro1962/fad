<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ConfigureSendGrid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendgrid:configure {api-key} {--from-email=noreply@campusdigitale.it} {--from-name="Campus Digitale Forma"}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configura SendGrid con API Key e template';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apiKey = $this->argument('api-key');
        $fromEmail = $this->option('from-email');
        $fromName = $this->option('from-name');
        
        $this->info('🚀 Configurazione SendGrid per Campus Digitale Forma');
        $this->line('');
        
        // Aggiorna file .env
        $this->updateEnvFile($apiKey, $fromEmail, $fromName);
        
        $this->line('');
        $this->info('✅ SendGrid configurato con successo!');
        $this->line('');
        $this->line('📧 Configurazione:');
        $this->line("   • Host: smtp.sendgrid.net");
        $this->line("   • Port: 587");
        $this->line("   • Encryption: tls");
        $this->line("   • From: {$fromEmail} ({$fromName})");
        $this->line('');
        $this->line('🧪 Testa il sistema con: php artisan mailing:test --type=sendgrid');
        
        return 0;
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
}
