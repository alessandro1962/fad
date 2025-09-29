<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResellerClient extends Model
{
    protected $fillable = [
        'reseller_id',
        'user_id',
        'organization_id',
        'tokens_used',
    ];

    protected $casts = [
        'tokens_used' => 'integer',
    ];

    /**
     * Get the reseller that owns the client.
     */
    public function reseller(): BelongsTo
    {
        return $this->belongsTo(Reseller::class);
    }

    /**
     * Get the user (client).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the organization (if client is in an organization).
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
