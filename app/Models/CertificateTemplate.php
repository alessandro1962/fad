<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CertificateTemplate extends Model
{
    protected $fillable = [
        'name',
        'description',
        'template_type',
        'html_template',
        'settings',
        'background_image',
        'is_active',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the tracks that use this template.
     */
    public function tracks(): HasMany
    {
        return $this->hasMany(Track::class);
    }

    /**
     * Scope a query to only include active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include templates of a specific type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('template_type', $type);
    }

    /**
     * Get the default settings for the template.
     */
    public function getDefaultSettingsAttribute()
    {
        return array_merge([
            'font_family' => 'Inter',
            'font_size' => 12,
            'text_color' => '#14161A',
            'primary_color' => '#0B3B5E',
            'secondary_color' => '#00A7B7',
            'accent_color' => '#FFC857',
            'background_color' => '#F4F1EA',
            'logo_position' => 'top-left',
            'signature_position' => 'bottom-right',
        ], $this->settings ?? []);
    }
}
