<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'company' => ['nullable', 'string', 'max:255'],
            'profession' => ['nullable', 'string', 'max:255'],
            'privacy_consent' => ['required', 'accepted'],
            'marketing_consent' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'Il nome è obbligatorio.',
            'last_name.required' => 'Il cognome è obbligatorio.',
            'email.required' => 'L\'email è obbligatoria.',
            'email.email' => 'L\'email deve essere un indirizzo valido.',
            'email.unique' => 'Questo indirizzo email è già registrato.',
            'password.required' => 'La password è obbligatoria.',
            'password.confirmed' => 'La conferma della password non corrisponde.',
            'privacy_consent.required' => 'È necessario accettare l\'informativa sulla privacy.',
            'privacy_consent.accepted' => 'È necessario accettare l\'informativa sulla privacy.',
        ];
    }
}
