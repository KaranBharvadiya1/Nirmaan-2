<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class ContractorWorkSample extends Model
{
    protected $fillable = [
        'contractor_id',
        'title',
        'work_category',
        'description',
        'city',
        'state',
        'completed_year',
    ];

    protected function casts(): array
    {
        return [
            'completed_year' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (ContractorWorkSample $workSample): void {
            $filePaths = $workSample->media()
                ->pluck('file_path')
                ->filter()
                ->values()
                ->all();

            if ($filePaths !== []) {
                Storage::disk('public')->delete($filePaths);
            }

            Storage::disk('public')->deleteDirectory('contractor-portfolio/'.$workSample->id);
        });
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function contractor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contractor_id');
    }

    /**
     * @return HasMany<ContractorWorkMedia, $this>
     */
    public function media(): HasMany
    {
        return $this->hasMany(ContractorWorkMedia::class)->orderBy('sort_order')->orderBy('id');
    }
}
