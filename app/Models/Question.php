<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    protected $fillable = [
        'quiz_id',
        'text',
        'type',
        'options',
        'correct_answer',
        'score',
        'explanation',
        'order',
        'is_active',
    ];

    protected $casts = [
        'options' => 'array',
        'correct_answer' => 'array',
        'score' => 'integer',
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the quiz that owns the question.
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Scope a query to only include active questions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include questions of a specific type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Check if an answer is correct.
     */
    public function isCorrectAnswer($answer)
    {
        if ($this->type === 'multiple_choice' || $this->type === 'single_choice') {
            return in_array($answer, $this->correct_answer);
        } elseif ($this->type === 'true_false') {
            return $answer === $this->correct_answer[0];
        } elseif ($this->type === 'text' || $this->type === 'number') {
            return strtolower(trim($answer)) === strtolower(trim($this->correct_answer[0]));
        }
        
        return false;
    }

    /**
     * Get the options for multiple choice questions.
     */
    public function getOptionsArrayAttribute()
    {
        if ($this->type === 'multiple_choice' || $this->type === 'single_choice') {
            return $this->options ?? [];
        }
        
        return [];
    }
}
