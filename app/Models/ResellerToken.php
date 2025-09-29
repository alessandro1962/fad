<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResellerToken extends Model
{
    protected $fillable = [
        'reseller_id',
        'admin_id',
        'tokens_assigned',
        'tokens_used',
        'tokens_purchased',
        'price_per_token',
        'total_paid',
        'assigned_at',
        'purchased_at',
    ];

    protected $casts = [
        'tokens_assigned' => 'integer',
        'tokens_used' => 'integer',
        'tokens_purchased' => 'integer',
        'price_per_token' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'assigned_at' => 'datetime',
        'purchased_at' => 'datetime',
    ];

    protected $appends = [
        'available_tokens',
    ];

    /**
     * Get the reseller that owns the tokens.
     */
    public function reseller(): BelongsTo
    {
        return $this->belongsTo(Reseller::class);
    }

    /**
     * Get the admin who assigned the tokens.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get available tokens (assigned + purchased - used).
     */
    public function getAvailableTokensAttribute(): int
    {
        return $this->tokens_assigned + $this->tokens_purchased - $this->tokens_used;
    }

    /**
     * Check if reseller has enough tokens.
     */
    public function hasEnoughTokens(int $required): bool
    {
        return $this->available_tokens >= $required;
    }

    /**
     * Use tokens.
     */
    public function useTokens(int $amount): bool
    {
        if (!$this->hasEnoughTokens($amount)) {
            return false;
        }

        $this->tokens_used += $amount;
        return $this->save();
    }
}
