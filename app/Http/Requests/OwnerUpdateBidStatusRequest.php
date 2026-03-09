<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OwnerUpdateBidStatusRequest extends FormRequest
{
    /** Restrict bid status changes to owner accounts. */
    public function authorize(): bool
    {
        return $this->user()?->role === 'Owner';
    }

    /**
     * Validate the allowed owner-side bid status transitions.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['pending', 'shortlisted', 'accepted', 'rejected'])],
        ];
    }
}
