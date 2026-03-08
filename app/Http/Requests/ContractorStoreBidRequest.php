<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

class ContractorStoreBidRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $project = $this->route('project');

        if (! $user || $user->role !== 'Contractor' || ! $project instanceof Project) {
            return false;
        }

        return $project->status === 'open' && (int) $project->owner_id !== (int) $user->id;
    }

    /**
     * Get the validation rules that apply to the request.
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

    protected function prepareForValidation(): void
    {
        $this->merge([
            'cover_message' => trim((string) $this->input('cover_message')),
        ]);
    }
}
