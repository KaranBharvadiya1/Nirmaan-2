<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContractorUpdateSettingsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ContractorSettingsController extends Controller
{
    /** Render the contractor profile settings screen. */
    public function showProfileSettings(): View
    {
        return view('contractor.settings');
    }

    /** Save contractor profile details, profile image, and optional password changes. */
    public function saveProfileSettings(ContractorUpdateSettingsRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->name = trim($validated['first_name'].' '.$validated['last_name']);
        $user->email = $validated['email'];

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image_path) {
                Storage::disk('public')->delete($user->profile_image_path);
            }

            $user->profile_image_path = $request->file('profile_image')->store('contractor-profiles', 'public');
        }

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()
            ->route('contractor.settings')
            ->with('success', 'Profile settings updated successfully.');
    }
}
