<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin Campus Digitale',
            'first_name' => 'Admin',
            'last_name' => 'Campus Digitale',
            'email' => 'admin@campusdigitale.it',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
            'is_admin' => true,
            'privacy_consent' => true,
            'marketing_consent' => false,
        ]);

        // Create Giulia Bianchi user
        $giulia = User::create([
            'name' => 'Giulia Bianchi',
            'first_name' => 'Giulia',
            'last_name' => 'Bianchi',
            'email' => 'giulia.bianchi@example.com',
            'password' => Hash::make('giulia123'),
            'email_verified_at' => now(),
            'is_admin' => false,
            'privacy_consent' => true,
            'marketing_consent' => true,
            'profession' => 'Sviluppatrice Web',
            'company' => 'Tech Solutions SRL',
        ]);

        $this->command->info('Utenti creati con successo:');
        $this->command->info('Admin: admin@campusdigitale.it / admin123');
        $this->command->info('Giulia: giulia.bianchi@example.com / giulia123');
    }
}