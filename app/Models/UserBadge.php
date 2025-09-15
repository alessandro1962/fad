<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBadge extends Model
{
    protected $fillable = [
        'user_id',
        'badge_id',
        'awarded_at',
        'reason',
        'metadata',
    ];

    protected $casts = [
        'awarded_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the user that owns the user badge.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the badge for the user badge.
     */
    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class);
    }

    /**
     * Scope a query to only include badges awarded recently.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('awarded_at', '>=', now()->subDays($days));
    }

    /**
     * Scope a query to only include badges of a specific category.
     */
    public function scopeCategory($query, $category)
    {
        return $query->whereHas('badge', function ($q) use ($category) {
            $q->where('category', $category);
        });
    }
}
