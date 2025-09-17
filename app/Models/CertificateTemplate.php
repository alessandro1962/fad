<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificateTemplate extends Model
{
    protected $fillable = [
        'name',
        'description',
        'template_type',
        'course_id', // Corso associato
        'background_image',
        'settings',
        'is_active',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the course associated with this template.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

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
     * Scope a query to only include default templates.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Get the default styling for the template.
     */
    public function getDefaultStylingAttribute()
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
        ], $this->styling ?? []);
    }

    /**
     * Get the default placeholder positions.
     */
    public function getDefaultPlaceholderPositionsAttribute()
    {
        return array_merge([
            'user_name' => ['x' => 50, 'y' => 40, 'align' => 'center'],
            'course_title' => ['x' => 50, 'y' => 50, 'align' => 'center'],
            'certificate_date' => ['x' => 20, 'y' => 80, 'align' => 'left'],
            'signature' => ['x' => 80, 'y' => 80, 'align' => 'right'],
            'certificate_id' => ['x' => 50, 'y' => 90, 'align' => 'center'],
        ], $this->placeholder_positions ?? []);
    }
}
