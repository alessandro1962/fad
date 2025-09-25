<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Add proper authorization logic here
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $courseId = $this->route('course') ? $this->route('course')->id : null;

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:courses,slug,' . $courseId],
            'summary' => ['required', 'string', 'max:500'],
            'description' => ['required', 'string'],
            'level' => ['required', 'in:beginner,intermediate,expert'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'price_cents' => ['required', 'integer', 'min:0'],
            'currency' => ['required', 'string', 'in:EUR,USD,GBP'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'featured_image' => ['nullable', 'string', 'max:255'],
            'video_preview_url' => ['nullable', 'url', 'max:255'],
            'is_active' => ['boolean'],
            'published_at' => ['nullable', 'date'],
            'add_to_full_vision' => ['boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Il titolo del corso è obbligatorio',
            'title.max' => 'Il titolo non può superare i 255 caratteri',
            'slug.unique' => 'Questo slug è già utilizzato da un altro corso',
            'summary.required' => 'Il riassunto del corso è obbligatorio',
            'summary.max' => 'Il riassunto non può superare i 500 caratteri',
            'description.required' => 'La descrizione del corso è obbligatoria',
            'level.required' => 'Il livello del corso è obbligatorio',
            'level.in' => 'Il livello deve essere: principiante, intermedio o esperto',
            'duration_minutes.required' => 'La durata del corso è obbligatoria',
            'duration_minutes.min' => 'La durata deve essere almeno 1 minuto',
            'price_cents.required' => 'Il prezzo del corso è obbligatorio',
            'price_cents.min' => 'Il prezzo non può essere negativo',
            'currency.required' => 'La valuta del corso è obbligatoria',
            'currency.in' => 'La valuta deve essere EUR, USD o GBP',
            'tags.array' => 'I tag devono essere un array',
            'tags.*.string' => 'Ogni tag deve essere una stringa',
            'tags.*.max' => 'Ogni tag non può superare i 50 caratteri',
            'video_preview_url.url' => 'L\'URL del video deve essere valido',
            'published_at.date' => 'La data di pubblicazione deve essere valida',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert price from euros to cents if needed
        if ($this->has('price_euros') && !$this->has('price_cents')) {
            $this->merge([
                'price_cents' => (int) ($this->price_euros * 100)
            ]);
        }

        // Ensure tags is an array
        if ($this->has('tags') && is_string($this->tags)) {
            $this->merge([
                'tags' => array_filter(explode(',', $this->tags))
            ]);
        }
    }
}