<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Course;
use App\Models\Module;
use App\Events\UserRegistered;
use App\Events\ModuleCompleted;
use App\Events\CourseCompleted;

class TestMailingSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailing:test {--type=all : Tipo di test (all, registration, module, course)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa il sistema di mailing inviando notifiche di prova';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        
        $this->info('🧪 Test del Sistema di Mailing');
        $this->line('');
        
        switch ($type) {
            case 'registration':
                $this->testUserRegistration();
                break;
            case 'module':
                $this->testModuleCompletion();
                break;
            case 'course':
                $this->testCourseCompletion();
                break;
            case 'all':
            default:
                $this->testUserRegistration();
                $this->testModuleCompletion();
                $this->testCourseCompletion();
                break;
        }
        
        $this->line('');
        $this->info('✅ Test completato! Controlla i log email o la coda.');
    }
    
    private function testUserRegistration()
    {
        $this->info('📧 Test: Notifica Registrazione Utente');
        
        // Crea un utente di test
        $user = User::first();
        if (!$user) {
            $this->error('Nessun utente trovato nel database. Crea prima un utente.');
            return;
        }
        
        // Simula l'evento di registrazione
        event(new UserRegistered($user));
        
        $this->line("✅ Evento UserRegistered inviato per: {$user->name} ({$user->email})");
    }
    
    private function testModuleCompletion()
    {
        $this->info('📧 Test: Notifica Completamento Modulo');
        
        $user = User::first();
        $course = Course::first();
        $module = Module::first();
        
        if (!$user || !$course || !$module) {
            $this->error('Dati insufficienti per il test. Assicurati di avere utenti, corsi e moduli.');
            return;
        }
        
        // Simula l'evento di completamento modulo
        event(new ModuleCompleted($user, $course, $module, 75));
        
        $this->line("✅ Evento ModuleCompleted inviato per: {$user->name} - {$module->title}");
    }
    
    private function testCourseCompletion()
    {
        $this->info('📧 Test: Notifica Completamento Corso');
        
        $user = User::first();
        $course = Course::first();
        
        if (!$user || !$course) {
            $this->error('Dati insufficienti per il test. Assicurati di avere utenti e corsi.');
            return;
        }
        
        // Simula l'evento di completamento corso
        event(new CourseCompleted($course));
        
        $this->line("✅ Evento CourseCompleted inviato per: {$user->name} - {$course->title}");
    }
}
