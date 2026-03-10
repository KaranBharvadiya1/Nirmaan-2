<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OwnerShortlistRequest extends FormRequest
{
    /** Only owners can create or update shortlist entries. */
    public function authorize(): bool
    {
        return $this->user()?->role === 'Owner';
    }

    /**
     * Validate the contractor, project/bid context, and optional prioritization data.
     *
     * @return array<string, array<int, \Illuminate\Contracts\Validation\ValidationRule|string>|string>
     */
    public function rules(): array
    {
        $base = [
            'project_id' => ['sometimes', 'nullable', 'integer', 'exists:projects,id'],
            'bid_id' => ['sometimes', 'nullable', 'integer', 'exists:bids,id'],
            'note' => ['nullable', 'string', 'max:1000'],
            'priority' => ['nullable', 'integer', 'between:1,5'],
        ];

        if ($this->isMethod('post')) {
            $base['contractor_id'] = ['required', 'integer', 'exists:users,id'];
        } else {
            $base['contractor_id'] = ['sometimes', 'nullable', 'integer', 'exists:users,id'];
        }

        return $base;
    }

    /** Clean up the submitted note and priority values before validation. */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'note' => trim((string) $this->input('note')),
            'priority' => $this->input('priority') !== null ? (int) $this->input('priority') : null,
        ]);
    }
}
