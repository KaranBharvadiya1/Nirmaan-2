<?php

namespace App\Http\Controllers;

use App\Http\Requests\OwnerUpdateSettingsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OwnerSettingsController extends Controller
{
    public function showProfileSettings(): View
    {
        return view('owner.settings');
    }

    public function saveProfileSettings(OwnerUpdateSettingsRequest $request): RedirectResponse
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

            $user->profile_image_path = $request->file('profile_image')->store('owner-profiles', 'public');
        }

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()
            ->route('owner.settings')
            ->with('success', 'Profile settings updated successfully.');
    }
}
