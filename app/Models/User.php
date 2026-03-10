<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'role',
        'profile_image_path',
        'email',
        'password',
        'contractor_bio',
        'years_experience',
        'trades',
        'service_areas',
        'languages',
        'team_size',
        'availability_status',
        'hourly_rate_from',
        'hourly_rate_to',
        'video_intro_url',
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
     * Cast persisted auth fields to their runtime PHP representations.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** Build a public storage URL for the user's optional profile image. */
    public function getProfileImageUrlAttribute(): ?string
    {
        if (! $this->profile_image_path) {
            return null;
        }

        return Storage::url($this->profile_image_path);
    }

    /** Provide a human-readable availability label for contractor profiles. */
    public function getAvailabilityLabelAttribute(): string
    {
        return match ($this->availability_status) {
            'available' => 'Available now',
            'booked' => 'Currently booked',
            'limited' => 'Limited availability',
            'consultation' => 'Consultation only',
            default => 'Contact for availability',
        };
    }

    /**
     * Get the projects created by this owner account.
     *
     * @return HasMany<Project, $this>
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'owner_id');
    }

    /**
     * Get the bids submitted by this contractor account.
     *
     * @return HasMany<Bid, $this>
     */
    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class, 'contractor_id');
    }

    /**
     * Get the hire records owned by this owner account.
     *
     * @return HasMany<ProjectHire, $this>
     */
    public function ownerProjectHires(): HasMany
    {
        return $this->hasMany(ProjectHire::class, 'owner_id');
    }

    /**
     * Get the hire records awarded to this contractor account.
     *
     * @return HasMany<ProjectHire, $this>
     */
    public function contractorProjectHires(): HasMany
    {
        return $this->hasMany(ProjectHire::class, 'contractor_id');
    }

    /**
     * Get the portfolio work samples created by this contractor account.
     *
     * @return HasMany<ContractorWorkSample, $this>
     */
    public function contractorWorkSamples(): HasMany
    {
        return $this->hasMany(ContractorWorkSample::class, 'contractor_id');
    }

    /**
     * Get shortlist entries created by this owner account.
     *
     * @return HasMany<Shortlist, $this>
     */
    public function createdShortlists(): HasMany
    {
        return $this->hasMany(Shortlist::class, 'owner_id');
    }

    /**
     * Get shortlist records that refer to this contractor.
     *
     * @return HasMany<Shortlist, $this>
     */
    public function shortlistCandidates(): HasMany
    {
        return $this->hasMany(Shortlist::class, 'contractor_id');
    }
}
