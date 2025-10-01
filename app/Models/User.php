<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'company',
        'profession',
        'organization_id',
        'provider',
        'provider_id',
        'marketing_consent',
        'privacy_consent',
        'last_login_at',
        'email_verified_at',
        'source',
        'is_admin',
        'is_company_manager',
        'is_reseller',
        'created_by_reseller_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'marketing_consent' => 'boolean',
            'privacy_consent' => 'boolean',
            'is_admin' => 'boolean',
            'is_company_manager' => 'boolean',
        ];
    }

    /**
     * Get the organization that the user belongs to.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the enrollments for the user.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the progress records for the user.
     */
    public function progress(): HasMany
    {
        return $this->hasMany(Progress::class);
    }

    /**
     * Get the orders for the user.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the certificates for the user.
     */
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Get the badges for the user.
     */
    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
            ->withPivot('awarded_at', 'reason', 'metadata')
            ->withTimestamps();
    }

    /**
     * Get the user track assignments.
     */
    public function userTracks(): HasMany
    {
        return $this->hasMany(UserTrack::class);
    }

    /**
     * Get the quiz attempts for the user.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(Attempt::class);
    }

    /**
     * Get the events for the user.
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Get the full vision assignment for the user.
     */
    public function fullVisionAssignment(): HasMany
    {
        return $this->hasMany(PlanFullVisionAssignment::class);
    }

    /**
     * Check if the user has full vision access.
     */
    public function hasFullVisionAccess()
    {
        return $this->fullVisionAssignment()
            ->currentlyValid()
            ->exists();
    }

    /**
     * Get the user's learning level based on completed courses and badges.
     */
    public function getLearningLevelAttribute()
    {
        $completedCourses = $this->enrollments()->completed()->count();
        $badges = $this->badges()->count();
        
        if ($completedCourses >= 10 || $badges >= 5) {
            return 'expert';
        } elseif ($completedCourses >= 5 || $badges >= 3) {
            return 'intermediate';
        } else {
            return 'beginner';
        }
    }

    /**
     * Get the user's total learning hours.
     */
    public function getTotalLearningHoursAttribute()
    {
        return $this->certificates()->sum('hours_total');
    }

    /**
     * Scope a query to only include users with marketing consent.
     */
    public function scopeMarketingConsent($query)
    {
        return $query->where('marketing_consent', true);
    }

    /**
     * Scope a query to only include users from a specific organization.
     */
    public function scopeForOrganization($query, $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    /**
     * Scope a query to only include active users (logged in recently).
     */
    public function scopeActive($query, $days = 30)
    {
        return $query->where('last_login_at', '>=', now()->subDays($days));
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->is_admin === true;
    }

    /**
     * Check if user is reseller.
     */
    public function isReseller(): bool
    {
        return $this->is_reseller === true;
    }

    /**
     * Get the reseller profile for this user.
     */
    public function reseller(): HasOne
    {
        return $this->hasOne(Reseller::class);
    }

    /**
     * Get the reseller who created this user.
     */
    public function createdByReseller(): BelongsTo
    {
        return $this->belongsTo(Reseller::class, 'created_by_reseller_id');
    }

    /**
     * Get the reseller client record for this user.
     */
    public function resellerClient(): HasOne
    {
        return $this->hasOne(ResellerClient::class);
    }

    /**
     * Scope a query to only include admin users.
     */
    public function scopeAdmin($query)
    {
        return $query->where('is_admin', true);
    }

    /**
     * Scope a query to only include reseller users.
     */
    public function scopeReseller($query)
    {
        return $query->where('is_reseller', true);
    }
}
