<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ImportUsersController extends Controller
{
    /**
     * Import users from CSV with Google OAuth setup
     */
    public function importGoogleUsers(Request $request): JsonResponse
    {
        $request->validate([
            'csv_data' => ['required', 'string'],
            'organization_id' => ['nullable', 'exists:organizations,id'],
        ]);

        $csvData = $request->csv_data;
        $organizationId = $request->organization_id;
        
        // Parse CSV data
        $lines = explode("\n", $csvData);
        $headers = str_getcsv(array_shift($lines)); // Get headers
        
        $imported = 0;
        $skipped = 0;
        $errors = [];
        
        foreach ($lines as $lineNumber => $line) {
            if (empty(trim($line))) continue;
            
            $data = str_getcsv($line);
            
            if (count($data) < 3) {
                $errors[] = "Riga " . ($lineNumber + 2) . ": Dati insufficienti";
                $skipped++;
                continue;
            }
            
            // Expected format: nome, cognome, email, azienda (optional)
            $firstName = trim($data[0] ?? '');
            $lastName = trim($data[1] ?? '');
            $email = trim($data[2] ?? '');
            $company = trim($data[3] ?? '');
            
            if (empty($firstName) || empty($lastName) || empty($email)) {
                $errors[] = "Riga " . ($lineNumber + 2) . ": Nome, cognome e email sono obbligatori";
                $skipped++;
                continue;
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Riga " . ($lineNumber + 2) . ": Email non valida: " . $email;
                $skipped++;
                continue;
            }
            
            // Check if user already exists
            $existingUser = User::where('email', $email)->first();
            if ($existingUser) {
                $errors[] = "Riga " . ($lineNumber + 2) . ": Utente già esistente: " . $email;
                $skipped++;
                continue;
            }
            
            try {
                // Create user with Google provider setup
                $user = User::create([
                    'name' => $firstName . ' ' . $lastName,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'company' => $company,
                    'organization_id' => $organizationId,
                    'provider' => 'google', // Set to Google for OAuth matching
                    'provider_id' => null, // Will be set when user logs in with Google
                    'password' => Hash::make(Str::random(32)), // Random password, won't be used
                    'email_verified_at' => now(),
                    'privacy_consent' => true, // Assume consent for imported users
                    'marketing_consent' => false,
                ]);
                
                $imported++;
                
            } catch (\Exception $e) {
                $errors[] = "Riga " . ($lineNumber + 2) . ": Errore durante la creazione: " . $e->getMessage();
                $skipped++;
            }
        }
        
        return response()->json([
            'message' => 'Import completato',
            'data' => [
                'imported' => $imported,
                'skipped' => $skipped,
                'errors' => $errors,
            ]
        ]);
    }
    
    /**
     * Get CSV template for Google users import
     */
    public function getTemplate(): JsonResponse
    {
        $template = "nome,cognome,email,azienda\n";
        $template .= "Mario,Rossi,mario.rossi@azienda.com,Azienda SRL\n";
        $template .= "Giulia,Bianchi,giulia.bianchi@azienda.com,Azienda SRL\n";
        
        return response()->json([
            'template' => $template,
            'headers' => ['nome', 'cognome', 'email', 'azienda'],
            'description' => 'Formato CSV: nome, cognome, email (obbligatori), azienda (opzionale)'
        ]);
    }
}