<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Project extends Model
{
    protected $fillable = [
        'owner_id',
        'reference_code',
        'title',
        'project_type',
        'work_category',
        'description',
        'site_address',
        'area_locality',
        'city',
        'district',
        'state',
        'postal_code',
        'landmark',
        'budget_currency',
        'budget_min',
        'budget_max',
        'required_start_date',
        'deadline',
        'expected_duration_days',
        'labor_strength_required',
        'material_supply_mode',
        'visibility',
        'status',
        'preferred_language',
        'safety_requirements',
        'quality_expectations',
    ];

    protected function casts(): array
    {
        return [
            'budget_min' => 'decimal:2',
            'budget_max' => 'decimal:2',
            'required_start_date' => 'date',
            'deadline' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (Project $project): void {
            $filePaths = $project->projectDocuments()
                ->pluck('file_path')
                ->filter()
                ->values()
                ->all();

            if ($filePaths !== []) {
                Storage::disk('public')->delete($filePaths);
            }

            Storage::disk('public')->deleteDirectory('project-documents/'.$project->id);
        });
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * @return HasMany<Bid, $this>
     */
    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    /**
     * @return HasMany<ProjectDocument, $this>
     */
    public function projectDocuments(): HasMany
    {
        return $this->hasMany(ProjectDocument::class);
    }

    /**
     * @return HasOne<ProjectHire, $this>
     */
    public function hire(): HasOne
    {
        return $this->hasOne(ProjectHire::class);
    }
}
