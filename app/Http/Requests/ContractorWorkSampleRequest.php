<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContractorWorkSampleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'Contractor';
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        $currentYear = (int) now()->format('Y');

        return [
            'title' => ['required', 'string', 'max:150'],
            'work_category' => ['nullable', 'string', 'max:120'],
            'description' => ['required', 'string', 'min:20', 'max:3000'],
            'city' => ['nullable', 'string', 'max:80'],
            'state' => ['nullable', 'string', 'max:80'],
            'completed_year' => ['nullable', 'integer', 'min:1990', 'max:'.($currentYear + 1)],
            'media_files' => ['nullable', 'array', 'max:8'],
            'media_files.*' => ['file', 'mimes:jpg,jpeg,png,webp,mp4,mov,webm', 'max:51200'],
            'external_video_links' => ['nullable', 'array', 'max:3'],
            'external_video_links.*' => ['nullable', 'url', 'max:500'],
            'remove_media' => ['nullable', 'array'],
            'remove_media.*' => ['integer'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $externalLinks = array_map(
            static fn ($value): string => trim((string) $value),
            (array) $this->input('external_video_links', [])
        );

        $this->merge([
            'title' => trim((string) $this->input('title')),
            'work_category' => trim((string) $this->input('work_category')),
            'description' => trim((string) $this->input('description')),
            'city' => trim((string) $this->input('city')),
            'state' => trim((string) $this->input('state')),
            'external_video_links' => array_values(array_filter($externalLinks, static fn (string $value): bool => $value !== '')),
        ]);
    }
}
