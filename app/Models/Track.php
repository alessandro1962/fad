<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Track extends Model
{
    protected $fillable = [
        'title',
        'description',
        'rules',
        'target_audience',
        'estimated_duration_hours',
        'certificate_template_id',
        'is_active',
        'is_auto_assign',
    ];

    protected $casts = [
        'rules' => 'array',
        'target_audience' => 'array',
        'is_active' => 'boolean',
        'is_auto_assign' => 'boolean',
        'estimated_duration_hours' => 'integer',
    ];

    /**
     * Get the certificate template for the track.
     */
    public function certificateTemplate(): BelongsTo
    {
        return $this->belongsTo(CertificateTemplate::class);
    }

    /**
     * Get the courses in this track.
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_track')->withPivot('order')->orderBy('course_track.order');
    }

    /**
     * Get the user track assignments.
     */
    public function userTracks(): HasMany
    {
        return $this->hasMany(UserTrack::class);
    }

    /**
     * Get the certificates for this track.
     */
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class, 'ref_id')->where('kind', 'track');
    }

    /**
     * Scope a query to only include active tracks.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include auto-assign tracks.
     */
    public function scopeAutoAssign($query)
    {
        return $query->where('is_auto_assign', true);
    }
}
