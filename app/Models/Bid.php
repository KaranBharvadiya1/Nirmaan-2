<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Bid extends Model
{
    protected $fillable = [
        'project_id',
        'contractor_id',
        'quote_amount',
        'proposed_timeline_days',
        'cover_message',
        'status',
        'owner_viewed_at',
        'contractor_status_viewed_at',
    ];

    /** Cast price and read-tracking fields to the correct PHP types. */
    protected function casts(): array
    {
        return [
            'quote_amount' => 'decimal:2',
            'owner_viewed_at' => 'datetime',
            'contractor_status_viewed_at' => 'datetime',
        ];
    }

    /**
     * Get the project that this bid belongs to.
     *
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the contractor who submitted this bid.
     *
     * @return BelongsTo<User, $this>
     */
    public function contractor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contractor_id');
    }

    /**
     * Get the hire record created from this bid, if one exists.
     *
     * @return HasOne<ProjectHire, $this>
     */
    public function projectHire(): HasOne
    {
        return $this->hasOne(ProjectHire::class);
    }
}
