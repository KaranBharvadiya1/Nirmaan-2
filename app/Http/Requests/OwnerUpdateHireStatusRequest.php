<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OwnerUpdateHireStatusRequest extends FormRequest
{
    /** Restrict hire status updates to owner accounts. */
    public function authorize(): bool
    {
        return $this->user()?->role === 'Owner';
    }

    /**
     * Validate the owner-managed lifecycle states for a project hire.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['active', 'completed', 'cancelled'])],
        ];
    }
}
