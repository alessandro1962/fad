<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attempt extends Model
{
    protected $fillable = [
        'user_id',
        'quiz_id',
        'attempt_number',
        'score',
        'max_score',
        'passed',
        'started_at',
        'finished_at',
        'answers',
    ];

    protected $casts = [
        'attempt_number' => 'integer',
        'score' => 'integer',
        'max_score' => 'integer',
        'passed' => 'boolean',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'answers' => 'array',
    ];

    /**
     * Get the user that owns the attempt.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the quiz for the attempt.
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Scope a query to only include passed attempts.
     */
    public function scopePassed($query)
    {
        return $query->where('passed', true);
    }

    /**
     * Scope a query to only include failed attempts.
     */
    public function scopeFailed($query)
    {
        return $query->where('passed', false);
    }

    /**
     * Scope a query to only include attempts for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include attempts for a specific quiz.
     */
    public function scopeForQuiz($query, $quizId)
    {
        return $query->where('quiz_id', $quizId);
    }

    /**
     * Get the percentage score.
     */
    public function getPercentageAttribute()
    {
        if ($this->max_score <= 0) {
            return 0;
        }
        
        return round(($this->score / $this->max_score) * 100, 2);
    }

    /**
     * Get the duration of the attempt.
     */
    public function getDurationAttribute()
    {
        if (!$this->started_at || !$this->finished_at) {
            return null;
        }
        
        return $this->started_at->diffInMinutes($this->finished_at);
    }

    /**
     * Check if the attempt is completed.
     */
    public function isCompleted()
    {
        return !is_null($this->finished_at);
    }
}
