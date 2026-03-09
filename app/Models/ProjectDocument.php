<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProjectDocument extends Model
{
    protected $fillable = [
        'project_id',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
    ];

    /**
     * Get the project that owns this uploaded document.
     *
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /** Build the public storage URL for the uploaded project document. */
    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }
}
