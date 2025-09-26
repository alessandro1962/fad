<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enrollment extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'order_id',
        'source',
        'status',
        'started_at',
        'completed_at',
        'progress_percentage',
        'time_spent_minutes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'progress_percentage' => 'integer',
        'time_spent_minutes' => 'integer',
    ];

    /**
     * Get the user that owns the enrollment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course for the enrollment.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the order for the enrollment.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the progress records for the enrollment.
     */
    public function progress(): HasMany
    {
        return $this->hasMany(Progress::class, 'user_id', 'user_id')
            ->whereHas('lesson.module', function ($query) {
                $query->where('course_id', $this->course_id);
            });
    }

    /**
     * Scope a query to only include active enrollments.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include completed enrollments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include enrollments from a specific source.
     */
    public function scopeFromSource($query, $source)
    {
        return $query->where('source', $source);
    }
}
