<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;

    private $importedCount = 0;
    private $skippedCount = 0;
    private $errors = [];

    public function model(array $row)
    {
        // Skip if email already exists
        if (User::where('email', $row['email'])->exists()) {
            $this->skippedCount++;
            $this->errors[] = "Email {$row['email']} già esistente - riga saltata";
            return null;
        }

        // Generate password if not provided
        $password = $row['password'] ?? Str::random(12);

        $this->importedCount++;

        return new User([
            'name' => trim($row['nome'] . ' ' . $row['cognome']),
            'first_name' => $row['nome'],
            'last_name' => $row['cognome'],
            'email' => $row['email'],
            'password' => Hash::make($password),
            'company' => $row['azienda'] ?? null,
            'profession' => $row['professione'] ?? null,
            'is_admin' => filter_var($row['admin'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'marketing_consent' => filter_var($row['consenso_marketing'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'privacy_consent' => filter_var($row['consenso_privacy'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'email_verified_at' => now(),
        ]);
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'cognome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'azienda' => ['nullable', 'string', 'max:255'],
            'professione' => ['nullable', 'string', 'max:255'],
            'admin' => ['nullable', 'boolean'],
            'consenso_marketing' => ['nullable', 'boolean'],
            'consenso_privacy' => ['nullable', 'boolean'],
            'password' => ['nullable', 'string', 'min:8'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nome.required' => 'Il nome è obbligatorio',
            'cognome.required' => 'Il cognome è obbligatorio',
            'email.required' => 'L\'email è obbligatoria',
            'email.email' => 'L\'email deve essere valida',
        ];
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
