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

    protected function casts(): array
    {
        return [
            'quote_amount' => 'decimal:2',
            'owner_viewed_at' => 'datetime',
            'contractor_status_viewed_at' => 'datetime',
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
    public function contractor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contractor_id');
    }

    /**
     * @return HasOne<ProjectHire, $this>
     */
    public function projectHire(): HasOne
    {
        return $this->hasOne(ProjectHire::class);
    }
}
