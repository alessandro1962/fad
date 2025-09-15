<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    protected $fillable = [
        'lesson_id',
        'title',
        'description',
        'settings',
        'passing_score',
        'max_attempts',
        'time_limit_minutes',
        'is_active',
    ];

    protected $casts = [
        'settings' => 'array',
        'passing_score' => 'integer',
        'max_attempts' => 'integer',
        'time_limit_minutes' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the lesson that owns the quiz.
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Get the questions for the quiz.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    /**
     * Get the attempts for the quiz.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(Attempt::class);
    }

    /**
     * Scope a query to only include active quizzes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the total score for the quiz.
     */
    public function getTotalScoreAttribute()
    {
        return $this->questions()->sum('score');
    }

    /**
     * Check if a user has passed the quiz.
     */
    public function hasUserPassed($userId)
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->where('passed', true)
            ->exists();
    }

    /**
     * Get the best attempt for a user.
     */
    public function getBestAttemptForUser($userId)
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->orderBy('score', 'desc')
            ->first();
    }
}
