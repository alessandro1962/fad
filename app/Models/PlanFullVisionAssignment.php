<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanFullVisionAssignment extends Model
{
    protected $fillable = [
        'user_id',
        'active',
        'since',
        'until',
        'auto_assign_new_courses',
    ];

    protected $casts = [
        'active' => 'boolean',
        'since' => 'datetime',
        'until' => 'datetime',
        'auto_assign_new_courses' => 'boolean',
    ];

    /**
     * Get the user that owns the full vision assignment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include active assignments.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope a query to only include assignments that are currently valid.
     */
    public function scopeCurrentlyValid($query)
    {
        $now = now();
        return $query->where('active', true)
            ->where('since', '<=', $now)
            ->where(function ($q) use ($now) {
                $q->whereNull('until')
                  ->orWhere('until', '>=', $now);
            });
    }

    /**
     * Scope a query to only include assignments with auto-assign enabled.
     */
    public function scopeAutoAssign($query)
    {
        return $query->where('auto_assign_new_courses', true);
    }

    /**
     * Check if the assignment is currently valid.
     */
    public function isCurrentlyValid()
    {
        if (!$this->active) {
            return false;
        }

        $now = now();
        
        if ($this->since > $now) {
            return false;
        }

        if ($this->until && $this->until < $now) {
            return false;
        }

        return true;
    }

    /**
     * Get the remaining days for this assignment.
     */
    public function getRemainingDaysAttribute()
    {
        if (!$this->until) {
            return null; // Unlimited
        }

        $now = now();
        if ($this->until <= $now) {
            return 0;
        }

        return $this->until->diffInDays($now);
    }
}
