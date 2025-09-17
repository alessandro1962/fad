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
            // Handle different correct_answer formats
            $correctAnswer = $this->correct_answer;
            
            // If it's a JSON string, decode it
            if (is_string($correctAnswer) && (str_starts_with($correctAnswer, '[') || str_starts_with($correctAnswer, '"'))) {
                $correctAnswer = json_decode($correctAnswer, true);
            }
            
            if (is_array($correctAnswer)) {
                return in_array($answer, $correctAnswer);
            } else {
                // correct_answer is an integer index
                $correctOption = $this->options[$correctAnswer] ?? null;
                return $answer === $correctOption;
            }
        } elseif ($this->type === 'true_false') {
            $correctAnswer = $this->correct_answer;
            if (is_string($correctAnswer) && (str_starts_with($correctAnswer, '[') || str_starts_with($correctAnswer, '"'))) {
                $correctAnswer = json_decode($correctAnswer, true);
            }
            $correctAnswer = is_array($correctAnswer) ? $correctAnswer[0] : $correctAnswer;
            return $answer === $correctAnswer;
        } elseif ($this->type === 'text' || $this->type === 'number') {
            $correctAnswer = $this->correct_answer;
            if (is_string($correctAnswer) && (str_starts_with($correctAnswer, '[') || str_starts_with($correctAnswer, '"'))) {
                $correctAnswer = json_decode($correctAnswer, true);
            }
            $correctAnswer = is_array($correctAnswer) ? $correctAnswer[0] : $correctAnswer;
            return strtolower(trim($answer)) === strtolower(trim($correctAnswer));
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
