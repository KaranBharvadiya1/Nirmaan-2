<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

class ContractorStoreBidRequest extends FormRequest
{
    /** Ensure only contractors can bid and never on their own projects. */
    public function authorize(): bool
    {
        $user = $this->user();
        $project = $this->route('project');

        if (! $user || $user->role !== 'Contractor' || ! $project instanceof Project) {
            return false;
        }

        return (int) $project->owner_id !== (int) $user->id;
    }

    /**
     * Validate the commercial and delivery fields required for a bid submission.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'quote_amount' => ['required', 'numeric', 'min:1000'],
            'proposed_timeline_days' => ['nullable', 'integer', 'min:1', 'max:3650'],
            'cover_message' => ['nullable', 'string', 'max:3000'],
        ];
    }

    /** Trim free-text input before the bid rules are evaluated. */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'cover_message' => trim((string) $this->input('cover_message')),
        ]);
    }
}
