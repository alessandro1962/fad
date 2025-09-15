<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Badge extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'icon',
        'color',
        'criteria',
        'category',
        'is_active',
    ];

    protected $casts = [
        'criteria' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the users that have this badge.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_badges')
            ->withPivot('awarded_at', 'reason', 'metadata')
            ->withTimestamps();
    }

    /**
     * Scope a query to only include active badges.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include badges of a specific category.
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Check if a user has this badge.
     */
    public function hasUser($userId)
    {
        return $this->users()->where('user_id', $userId)->exists();
    }

    /**
     * Award this badge to a user.
     */
    public function awardTo($userId, $reason = null, $metadata = null)
    {
        if (!$this->hasUser($userId)) {
            $this->users()->attach($userId, [
                'awarded_at' => now(),
                'reason' => $reason,
                'metadata' => $metadata,
            ]);
        }
    }
}
