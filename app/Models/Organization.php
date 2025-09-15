<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    protected $fillable = [
        'name',
        'vat',
        'sso_type',
        'sso_metadata',
        'is_active',
    ];

    protected $casts = [
        'sso_metadata' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the users for the organization.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the orders for the organization.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the events for the organization.
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
