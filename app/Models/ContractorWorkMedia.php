<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ContractorWorkMedia extends Model
{
    protected $fillable = [
        'contractor_work_sample_id',
        'media_type',
        'original_name',
        'file_path',
        'external_url',
        'mime_type',
        'file_size',
        'sort_order',
    ];

    /** Cast media sizing and ordering fields to integers. */
    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Get the portfolio work sample that this media belongs to.
     *
     * @return BelongsTo<ContractorWorkSample, $this>
     */
    public function workSample(): BelongsTo
    {
        return $this->belongsTo(ContractorWorkSample::class, 'contractor_work_sample_id');
    }

    /** Build a public storage URL for uploaded image or video media. */
    public function getFileUrlAttribute(): ?string
    {
        if (! $this->file_path) {
            return null;
        }

        return Storage::url($this->file_path);
    }

    /** Convert supported YouTube or Vimeo links into embeddable player URLs. */
    public function getEmbedUrlAttribute(): ?string
    {
        $url = trim((string) $this->external_url);

        if ($url === '') {
            return null;
        }

        $parts = parse_url($url);
        $host = strtolower((string) ($parts['host'] ?? ''));
        $path = trim((string) ($parts['path'] ?? ''), '/');

        if (str_contains($host, 'youtu.be') && $path !== '') {
            return 'https://www.youtube.com/embed/'.$path;
        }

        if (str_contains($host, 'youtube.com')) {
            parse_str((string) ($parts['query'] ?? ''), $query);
            if (! empty($query['v'])) {
                return 'https://www.youtube.com/embed/'.((string) $query['v']);
            }
        }

        if (str_contains($host, 'vimeo.com') && $path !== '') {
            return 'https://player.vimeo.com/video/'.$path;
        }

        return null;
    }
}
