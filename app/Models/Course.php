<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'summary',
        'description',
        'level',
        'duration_minutes',
        'price_cents',
        'currency',
        'tags',
        'is_active',
        'published_at',
        'featured_image',
        'video_preview_url',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_active' => 'boolean',
        'published_at' => 'datetime',
        'price_cents' => 'integer',
        'duration_minutes' => 'integer',
    ];

    /**
     * Get the modules for the course.
     */
    public function modules(): HasMany
    {
        return $this->hasMany(Module::class)->orderBy('order');
    }

    /**
     * Get the enrollments for the course.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the tracks that include this course.
     */
    public function tracks(): BelongsToMany
    {
        return $this->belongsToMany(Track::class, 'course_track')->withPivot('order')->orderBy('course_track.order');
    }

    /**
     * Get the certificates for the course.
     */
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class, 'ref_id')->where('kind', 'course');
    }

    /**
     * Get the progress for all users in this course.
     */
    public function progress(): HasMany
    {
        return $this->hasManyThrough(Progress::class, Lesson::class, 'module_id', 'lesson_id', 'id', 'id');
    }

    /**
     * Scope a query to only include active courses.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include published courses.
     */
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }
}
