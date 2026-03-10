<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContractorUpdateSettingsRequest extends FormRequest
{
    /** Limit this settings form to authenticated contractor accounts. */
    public function authorize(): bool
    {
        return $this->user()?->role === 'Contractor';
    }

    /**
     * Validate contractor profile fields, profile image uploads, and password changes.
     *
     * @return array<string, array<int, \Illuminate\Contracts\Validation\ValidationRule|string>|string>
     */
    public function rules(): array
    {
        $user = $this->user();

        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique(User::class, 'email')->ignore($user?->id),
            ],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'max:255', 'confirmed'],
            'contractor_bio' => ['nullable', 'string', 'max:1000'],
            'years_experience' => ['nullable', 'integer', 'between:0,60'],
            'trades' => ['nullable', 'string', 'max:255'],
            'service_areas' => ['nullable', 'string', 'max:255'],
            'languages' => ['nullable', 'string', 'max:255'],
            'team_size' => ['nullable', 'integer', 'between:1,500'],
            'availability_status' => ['nullable', 'string', 'max:32'],
            'hourly_rate_from' => ['nullable', 'numeric', 'min:0'],
            'hourly_rate_to' => ['nullable', 'numeric', 'min:0'],
            'video_intro_url' => ['nullable', 'url', 'max:1000'],
        ];
    }

    /** Normalize the basic profile fields before uniqueness and length checks run. */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'first_name' => trim((string) $this->input('first_name')),
            'last_name' => trim((string) $this->input('last_name')),
            'email' => trim((string) $this->input('email')),
            'trades' => trim((string) $this->input('trades')),
            'service_areas' => trim((string) $this->input('service_areas')),
            'languages' => trim((string) $this->input('languages')),
            'availability_status' => trim((string) $this->input('availability_status')),
            'video_intro_url' => trim((string) $this->input('video_intro_url')),
        ]);
    }
}
