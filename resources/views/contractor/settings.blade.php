@extends('contractor.layouts.app', ['pageTitle' => 'Contractor Settings - Nirmaan', 'activePage' => 'settings'])

@push('styles')
<style>
    .panel-card {
        border: 0;
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
    }

    .heading-panel {
        border: 0;
        border-radius: 1rem;
        background: linear-gradient(135deg, #ffffff 0%, #f2f6ff 100%);
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
        padding: 1rem;
    }

    .helper-text {
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    .profile-preview {
        width: 84px;
        height: 84px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #d4e2ff;
    }

    .profile-fallback {
        width: 84px;
        height: 84px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(145deg, #dde8ff 0%, #c9d9ff 100%);
        color: #1b3b94;
        font-weight: 700;
        font-size: 1.9rem;
    }

    @media (min-width: 992px) {
        .heading-panel {
            padding: 1.35rem 1.5rem;
        }
    }
</style>
@endpush

@section('content')
@php
    $settingsUser = auth()->user();
    $initial = strtoupper(substr($settingsUser->first_name ?? 'C', 0, 1));
    $profileImageUrl = $settingsUser?->profile_image_url;
@endphp

<div class="heading-panel mb-4">
    <p class="text-uppercase text-primary fw-semibold mb-1 small">Contractor Settings</p>
    <h1 class="fw-bold mb-2 h3 h-md-2">Profile & Security</h1>
    <p class="text-secondary mb-0">Manage your contractor identity, contact details, and login password.</p>
</div>

@if ($errors->any())
<div class="alert alert-danger shadow-sm">
    <ul class="mb-0 ps-3">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('contractor.settings.save') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row g-3 g-md-4">
        <div class="col-12 col-xl-7">
            <div class="panel-card p-4 p-md-5">
                <h2 class="h5 fw-bold mb-3">Basic Information</h2>
                <p class="helper-text mb-4">These fields power your contractor identity, public portfolio, and bidding trust signals.</p>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Profile Image</label>
                        <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-3">
                            @if ($profileImageUrl)
                            <img src="{{ $profileImageUrl }}" alt="Current profile image" class="profile-preview">
                            @else
                            <span class="profile-fallback">{{ $initial }}</span>
                            @endif
                            <div class="w-100">
                                <input id="profile_image" name="profile_image" type="file" accept=".jpg,.jpeg,.png,.webp" class="form-control{{ $errors->has('profile_image') ? ' is-invalid' : '' }}">
                                @error('profile_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <p class="helper-text mb-0 mt-2">PNG, JPG, WEBP up to 2MB.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="first_name" class="form-label fw-semibold">First Name</label>
                        <input id="first_name" name="first_name" type="text" value="{{ old('first_name', $settingsUser->first_name) }}" class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}" required>
                        @error('first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="last_name" class="form-label fw-semibold">Last Name</label>
                        <input id="last_name" name="last_name" type="text" value="{{ old('last_name', $settingsUser->last_name) }}" class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}" required>
                        @error('last_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label for="email" class="form-label fw-semibold">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $settingsUser->email) }}" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" required>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-5">
            <div class="panel-card p-4 p-md-5">
                <h2 class="h5 fw-bold mb-3">Change Password</h2>
                <p class="helper-text mb-4">Leave password fields empty if you do not want to change it.</p>

                <div class="mb-3">
                    <label for="current_password" class="form-label fw-semibold">Current Password</label>
                    <input id="current_password" name="current_password" type="password" class="form-control{{ $errors->has('current_password') ? ' is-invalid' : '' }}">
                    @error('current_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">New Password</label>
                    <input id="password" name="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}">
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-0">
                    <label for="password_confirmation" class="form-label fw-semibold">Confirm New Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-control">
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="panel-card p-4 p-md-5">
                <h2 class="h5 fw-bold mb-3">Profile Overview</h2>
                <p class="helper-text mb-4">Share your value proposition, expertise, and footprint so owners can evaluate you instantly.</p>
                <div class="row g-3">
                    <div class="col-12">
                        <label for="contractor_bio" class="form-label fw-semibold">Contractor Bio</label>
                        <textarea id="contractor_bio" name="contractor_bio" rows="3" class="form-control{{ $errors->has('contractor_bio') ? ' is-invalid' : '' }}">{{ old('contractor_bio', $settingsUser->contractor_bio) }}</textarea>
                        @error('contractor_bio')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="years_experience" class="form-label fw-semibold">Years of Experience</label>
                        <input id="years_experience" name="years_experience" type="number" min="0" value="{{ old('years_experience', $settingsUser->years_experience) }}" class="form-control{{ $errors->has('years_experience') ? ' is-invalid' : '' }}">
                        @error('years_experience')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="team_size" class="form-label fw-semibold">Team Size</label>
                        <input id="team_size" name="team_size" type="number" min="1" value="{{ old('team_size', $settingsUser->team_size) }}" class="form-control{{ $errors->has('team_size') ? ' is-invalid' : '' }}">
                        @error('team_size')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="trades" class="form-label fw-semibold">Primary Trades</label>
                        <input id="trades" name="trades" type="text" value="{{ old('trades', $settingsUser->trades) }}" class="form-control{{ $errors->has('trades') ? ' is-invalid' : '' }}">
                        @error('trades')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="service_areas" class="form-label fw-semibold">Service Areas</label>
                        <input id="service_areas" name="service_areas" type="text" value="{{ old('service_areas', $settingsUser->service_areas) }}" class="form-control{{ $errors->has('service_areas') ? ' is-invalid' : '' }}">
                        @error('service_areas')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="languages" class="form-label fw-semibold">Languages</label>
                        <input id="languages" name="languages" type="text" value="{{ old('languages', $settingsUser->languages) }}" class="form-control{{ $errors->has('languages') ? ' is-invalid' : '' }}">
                        @error('languages')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="panel-card p-4 p-md-5">
                <h2 class="h5 fw-bold mb-3">Availability & Rates</h2>
                <p class="helper-text mb-4">Share when you can start, your working capacity, and typical rates.</p>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="availability_status" class="form-label fw-semibold">Availability</label>
                        <select id="availability_status" name="availability_status" class="form-select{{ $errors->has('availability_status') ? ' is-invalid' : '' }}">
                            @foreach ([
                                '' => 'Select availability',
                                'available' => 'Available now',
                                'limited' => 'Limited availability',
                                'booked' => 'Booked',
                                'consultation' => 'Consultation',
                            ] as $value => $label)
                            <option value="{{ $value }}" @selected(old('availability_status', $settingsUser->availability_status) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('availability_status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="hourly_rate_from" class="form-label fw-semibold">Hourly Rate From (₹)</label>
                        <input id="hourly_rate_from" name="hourly_rate_from" type="number" step="0.01" min="0" value="{{ old('hourly_rate_from', $settingsUser->hourly_rate_from) }}" class="form-control{{ $errors->has('hourly_rate_from') ? ' is-invalid' : '' }}">
                        @error('hourly_rate_from')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="hourly_rate_to" class="form-label fw-semibold">Hourly Rate To (₹)</label>
                        <input id="hourly_rate_to" name="hourly_rate_to" type="number" step="0.01" min="0" value="{{ old('hourly_rate_to', $settingsUser->hourly_rate_to) }}" class="form-control{{ $errors->has('hourly_rate_to') ? ' is-invalid' : '' }}">
                        @error('hourly_rate_to')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label for="video_intro_url" class="form-label fw-semibold">Video Intro (YouTube/Vimeo)</label>
                        <input id="video_intro_url" name="video_intro_url" type="url" value="{{ old('video_intro_url', $settingsUser->video_intro_url) }}" class="form-control{{ $errors->has('video_intro_url') ? ' is-invalid' : '' }}">
                        @error('video_intro_url')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <p class="form-text text-muted small mb-0">Share a short walkthrough so owners can assess your vibe faster.</p>
                    </div>
                </div>
                <div class="mt-3 text-end">
                    <button type="submit" class="btn btn-primary">Save profile & availability</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
