<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'name',
        'subject',
        'body',
    ];

    /** Always sort templates alphabetically by name when retrieving lists. */
    protected static function booted(): void
    {
        static::addGlobalScope('orderByName', function ($query): void {
            $query->orderBy('name');
        });
    }

    /**
     * Render the template body or subject with the provided replacements.
     *
     * @param  array<string, mixed>  $replacements
     */
    public function render(string $content, array $replacements): string
    {
        $rendered = $content;

        foreach ($replacements as $key => $value) {
            $placeholder = sprintf('{{%s}}', $key);
            $rendered = str_replace($placeholder, (string) $value, $rendered);
        }

        return $rendered;
    }
}
