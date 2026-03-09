<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OwnerStoreProjectRequest extends FormRequest
{
    /** Restrict project creation and updates to owner accounts. */
    public function authorize(): bool
    {
        return $this->user()?->role === 'Owner';
    }

    /**
     * Validate project scope, site, budget, and supporting document fields.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'project_type' => ['required', Rule::in(['Residential', 'Commercial', 'Industrial', 'Infrastructure', 'Renovation'])],
            'work_category' => ['nullable', 'string', 'max:150'],
            'description' => ['required', 'string', 'min:20', 'max:5000'],

            'site_address' => ['required', 'string', 'max:500'],
            'area_locality' => ['required', 'string', 'max:120'],
            'city' => ['required', 'string', 'max:80'],
            'district' => ['required', 'string', 'max:80'],
            'state' => ['required', 'string', 'max:80'],
            'postal_code' => ['required', 'regex:/^[1-9][0-9]{5}$/'],
            'landmark' => ['nullable', 'string', 'max:120'],

            'budget_currency' => ['required', Rule::in(['INR'])],
            'budget_min' => ['required', 'numeric', 'min:1000'],
            'budget_max' => ['nullable', 'numeric', 'gte:budget_min'],

            'required_start_date' => ['nullable', 'date', 'before_or_equal:deadline'],
            'deadline' => ['required', 'date', 'after_or_equal:today'],
            'expected_duration_days' => ['nullable', 'integer', 'min:1', 'max:3650'],

            'labor_strength_required' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'material_supply_mode' => ['required', Rule::in(['owner', 'contractor', 'shared'])],
            'visibility' => ['required', Rule::in(['public', 'invite_only'])],
            'preferred_language' => ['required', 'string', 'max:50'],
            'safety_requirements' => ['nullable', 'string', 'max:2000'],
            'quality_expectations' => ['nullable', 'string', 'max:2000'],
            'project_documents' => ['nullable', 'array', 'max:8'],
            'project_documents.*' => ['file', 'mimes:pdf,jpg,jpeg,png,webp,dwg,dxf,doc,docx,xls,xlsx', 'max:20480'],
        ];
    }

    /** Trim text-heavy project fields before rule evaluation and persistence. */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => trim((string) $this->input('title')),
            'project_type' => trim((string) $this->input('project_type')),
            'work_category' => trim((string) $this->input('work_category')),
            'description' => trim((string) $this->input('description')),
            'site_address' => trim((string) $this->input('site_address')),
            'area_locality' => trim((string) $this->input('area_locality')),
            'city' => trim((string) $this->input('city')),
            'district' => trim((string) $this->input('district')),
            'state' => trim((string) $this->input('state')),
            'postal_code' => trim((string) $this->input('postal_code')),
            'landmark' => trim((string) $this->input('landmark')),
            'budget_currency' => trim((string) $this->input('budget_currency', 'INR')),
            'preferred_language' => trim((string) $this->input('preferred_language', 'English')),
            'safety_requirements' => trim((string) $this->input('safety_requirements')),
            'quality_expectations' => trim((string) $this->input('quality_expectations')),
        ]);
    }
}
