<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Certificate extends Model
{
    protected $fillable = [
        'user_id',
        'kind',
        'ref_id',
        'title',
        'description',
        'issued_at',
        'hours_total',
        'public_uid',
        'pdf_path',
        'metadata',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'hours_total' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Get the user that owns the certificate.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course or track this certificate is for.
     */
    public function reference()
    {
        if ($this->kind === 'course') {
            return $this->belongsTo(Course::class, 'ref_id');
        } elseif ($this->kind === 'track') {
            return $this->belongsTo(Track::class, 'ref_id');
        }
        
        return null;
    }

    /**
     * Scope a query to only include course certificates.
     */
    public function scopeCourse($query)
    {
        return $query->where('kind', 'course');
    }

    /**
     * Scope a query to only include track certificates.
     */
    public function scopeTrack($query)
    {
        return $query->where('kind', 'track');
    }

    /**
     * Scope a query to only include certificates for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Generate a unique public UID for the certificate.
     */
    public static function generatePublicUid()
    {
        do {
            $publicUid = 'CERT-' . strtoupper(Str::random(12));
        } while (static::where('public_uid', $publicUid)->exists());

        return $publicUid;
    }

    /**
     * Get the public URL for the certificate.
     */
    public function getPublicUrlAttribute()
    {
        return route('certificates.public', $this->public_uid);
    }

    /**
     * Check if the certificate has a PDF file.
     */
    public function hasPdf()
    {
        return !empty($this->pdf_path) && file_exists(storage_path('app/' . $this->pdf_path));
    }
}
