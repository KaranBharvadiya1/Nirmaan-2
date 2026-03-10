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

        $user->contractor_bio = $validated['contractor_bio'] ?? null;
        $user->years_experience = $validated['years_experience'] ?? null;
        $user->trades = $validated['trades'] ?? null;
        $user->service_areas = $validated['service_areas'] ?? null;
        $user->languages = $validated['languages'] ?? null;
        $user->team_size = $validated['team_size'] ?? null;
        $user->availability_status = $validated['availability_status'] ?? null;
        $user->hourly_rate_from = $validated['hourly_rate_from'] ?? null;
        $user->hourly_rate_to = $validated['hourly_rate_to'] ?? null;
        $user->video_intro_url = $validated['video_intro_url'] ?? null;

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()
            ->route('contractor.settings')
            ->with('success', 'Profile settings updated successfully.');
    }
}
