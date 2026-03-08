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

    protected function casts(): array
    {
        return [
            'agreed_amount' => 'decimal:2',
            'hired_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function contractor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contractor_id');
    }

    /**
     * @return BelongsTo<Bid, $this>
     */
    public function bid(): BelongsTo
    {
        return $this->belongsTo(Bid::class);
    }
}
