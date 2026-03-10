<?php

namespace App\Models;

use App\Models\Bid;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shortlist extends Model
{
    protected $table = 'shortlists';
    protected $fillable = [
        'owner_id',
        'contractor_id',
        'project_id',
        'bid_id',
        'note',
        'priority',
        'status',
    ];

    /** The owner who created this shortlist entry. */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /** The contractor that is being shortlisted. */
    public function contractor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contractor_id');
    }

    /** The project context for the shortlist, if any. */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /** The bid that triggered this shortlist entry, if any. */
    public function bid(): BelongsTo
    {
        return $this->belongsTo(Bid::class, 'bid_id');
    }
}
