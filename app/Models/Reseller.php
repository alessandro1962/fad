<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reseller extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'contact_email',
        'contact_phone',
        'address',
        'vat_number',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the reseller.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tokens for the reseller.
     */
    public function tokens(): HasOne
    {
        return $this->hasOne(ResellerToken::class);
    }

    /**
     * Get the clients for the reseller.
     */
    public function clients(): HasMany
    {
        return $this->hasMany(ResellerClient::class);
    }

    /**
     * Get the users created by this reseller.
     */
    public function createdUsers(): HasMany
    {
        return $this->hasMany(User::class, 'created_by_reseller_id');
    }

    /**
     * Get the organizations created by this reseller.
     */
    public function createdOrganizations(): HasMany
    {
        return $this->hasMany(Organization::class, 'created_by_reseller_id');
    }

    /**
     * Get total tokens assigned and purchased.
     */
    public function getTotalTokensAttribute(): int
    {
        $tokens = $this->tokens;
        if (!$tokens) {
            return 0;
        }
        
        return $tokens->tokens_assigned + $tokens->tokens_purchased;
    }

    /**
     * Get total tokens used.
     */
    public function getUsedTokensAttribute(): int
    {
        return $this->tokens ? $this->tokens->tokens_used : 0;
    }

    /**
     * Get total clients count.
     */
    public function getClientsCountAttribute(): int
    {
        return $this->clients()->count();
    }
}
