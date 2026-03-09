<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectHire extends Model
{
    protected $fillable = [
        'project_id',
        'owner_id',
        'contractor_id',
        'bid_id',
        'agreed_amount',
        'agreed_timeline_days',
        'hired_at',
        'status',
    ];

    /** Cast hire amount and hire timestamp fields to their runtime types. */
    protected function casts(): array
    {
        return [
            'agreed_amount' => 'decimal:2',
            'hired_at' => 'datetime',
        ];
    }

    /**
     * Get the project that this hire belongs to.
     *
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the owner who created this hire.
     *
     * @return BelongsTo<User, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the contractor awarded by this hire.
     *
     * @return BelongsTo<User, $this>
     */
    public function contractor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contractor_id');
    }

    /**
     * Get the bid record that produced this hire.
     *
     * @return BelongsTo<Bid, $this>
     */
    public function bid(): BelongsTo
    {
        return $this->belongsTo(Bid::class);
    }
}
