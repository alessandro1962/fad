<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Progress extends Model
{
    protected $fillable = [
        'user_id',
        'lesson_id',
        'seconds_watched',
        'completed',
        'last_position_sec',
        'last_seen_at',
        'completed_at',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'seconds_watched' => 'integer',
        'last_position_sec' => 'integer',
        'last_seen_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the progress.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the lesson for the progress.
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Scope a query to only include completed progress.
     */
    public function scopeCompleted($query)
    {
        return $query->where('completed', true);
    }

    /**
     * Scope a query to only include progress for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include progress for a specific lesson.
     */
    public function scopeForLesson($query, $lessonId)
    {
        return $query->where('lesson_id', $lessonId);
    }

    /**
     * Get the progress percentage for a lesson.
     */
    public function getProgressPercentageAttribute()
    {
        if (!$this->lesson || $this->lesson->duration_minutes <= 0) {
            return $this->completed ? 100 : 0;
        }

        $totalSeconds = $this->lesson->duration_minutes * 60;
        return min(100, round(($this->seconds_watched / $totalSeconds) * 100));
    }
}
