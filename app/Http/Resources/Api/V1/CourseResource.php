<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'summary' => $this->summary,
            'description' => $this->description,
            'level' => $this->level,
            'duration_minutes' => $this->duration_minutes,
            'duration_hours' => round($this->duration_minutes / 60, 1),
            'price_cents' => $this->price_cents,
            'price_euros' => round($this->price_cents / 100, 2),
            'currency' => $this->currency,
            'tags' => $this->tags,
            'featured_image' => $this->featured_image,
            'video_preview_url' => $this->video_preview_url,
            'is_active' => $this->is_active,
            'published_at' => $this->published_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            
            // Relazioni
            'modules_count' => $this->whenLoaded('modules', fn() => $this->modules->count()),
            'enrollments_count' => $this->whenLoaded('enrollments', fn() => $this->enrollments->count()),
            
            // Dati aggiuntivi per l'utente autenticato
            'is_enrolled' => $this->when(
                $request->user(),
                fn() => $request->user()->enrollments()->where('course_id', $this->id)->exists()
            ),
            'progress_percentage' => $this->when(
                $request->user(),
                fn() => $this->getUserProgressPercentage($request->user())
            ),
        ];
    }

    /**
     * Get user progress percentage for this course.
     */
    private function getUserProgressPercentage($user): int
    {
        $enrollment = $user->enrollments()->where('course_id', $this->id)->first();
        return $enrollment ? $enrollment->progress_percentage : 0;
    }
}
