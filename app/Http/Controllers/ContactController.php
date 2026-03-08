<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactStoreRequest;
use App\Models\ContactInquiry;
use Illuminate\Http\RedirectResponse;

class ContactController extends Controller
{
    public function store(ContactStoreRequest $request): RedirectResponse
    {
        ContactInquiry::create($request->validated());

        return redirect()
            ->back()
            ->with('success', 'Thanks for reaching out! We received your message.');
    }
}
