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
            'password' => 'hashed',
        ];
    }

    public function getProfileImageUrlAttribute(): ?string
    {
        if (! $this->profile_image_path) {
            return null;
        }

        return Storage::url($this->profile_image_path);
    }

    /**
     * @return HasMany<Project, $this>
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'owner_id');
    }

    /**
     * @return HasMany<Bid, $this>
     */
    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class, 'contractor_id');
    }

    /**
     * @return HasMany<ProjectHire, $this>
     */
    public function ownerProjectHires(): HasMany
    {
        return $this->hasMany(ProjectHire::class, 'owner_id');
    }

    /**
     * @return HasMany<ProjectHire, $this>
     */
    public function contractorProjectHires(): HasMany
    {
        return $this->hasMany(ProjectHire::class, 'contractor_id');
    }

    /**
     * @return HasMany<ContractorWorkSample, $this>
     */
    public function contractorWorkSamples(): HasMany
    {
        return $this->hasMany(ContractorWorkSample::class, 'contractor_id');
    }
}
