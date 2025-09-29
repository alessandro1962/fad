<?php

namespace Database\Seeders;

use App\Models\Reseller;
use App\Models\ResellerToken;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ResellerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user if not exists
        $admin = User::firstOrCreate(
            ['email' => 'admin@campusdigitale.online'],
            [
                'name' => 'Admin',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        // Create test reseller
        $resellerUser = User::create([
            'name' => 'Mario Rossi',
            'first_name' => 'Mario',
            'last_name' => 'Rossi',
            'email' => 'mario.rossi@rivenditore.com',
            'password' => Hash::make('password'),
            'is_reseller' => true,
            'email_verified_at' => now(),
        ]);

        $reseller = Reseller::create([
            'user_id' => $resellerUser->id,
            'company_name' => 'Rossi Formazione SRL',
            'contact_email' => 'info@rossiformazione.com',
            'contact_phone' => '+39 02 1234567',
            'address' => 'Via Roma 123, Milano',
            'vat_number' => 'IT12345678901',
        ]);

        // Assign tokens to reseller
        ResellerToken::create([
            'reseller_id' => $reseller->id,
            'admin_id' => $admin->id,
            'tokens_assigned' => 100,
            'assigned_at' => now(),
        ]);

        $this->command->info('Reseller test data created successfully!');
        $this->command->info('Admin: admin@campusdigitale.online / password');
        $this->command->info('Reseller: mario.rossi@rivenditore.com / password');
    }
}
