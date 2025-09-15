<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'item_type',
        'item_id',
        'quantity',
        'unit_price_cents',
        'total_cents',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price_cents' => 'integer',
        'total_cents' => 'integer',
    ];

    /**
     * Get the order that owns the order item.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the item (course, track, etc.) for this order item.
     */
    public function item()
    {
        if ($this->item_type === 'course') {
            return $this->belongsTo(Course::class, 'item_id');
        } elseif ($this->item_type === 'track') {
            return $this->belongsTo(Track::class, 'item_id');
        } elseif ($this->item_type === 'full_vision') {
            return null; // Full vision is not a specific item
        }
        
        return null;
    }

    /**
     * Get the unit price in euros.
     */
    public function getUnitPriceEurosAttribute()
    {
        return $this->unit_price_cents / 100;
    }

    /**
     * Get the total price in euros.
     */
    public function getTotalEurosAttribute()
    {
        return $this->total_cents / 100;
    }

    /**
     * Scope a query to only include course items.
     */
    public function scopeCourses($query)
    {
        return $query->where('item_type', 'course');
    }

    /**
     * Scope a query to only include track items.
     */
    public function scopeTracks($query)
    {
        return $query->where('item_type', 'track');
    }

    /**
     * Scope a query to only include full vision items.
     */
    public function scopeFullVision($query)
    {
        return $query->where('item_type', 'full_vision');
    }
}
