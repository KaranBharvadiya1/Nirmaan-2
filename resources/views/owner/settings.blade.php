@extends('owner.layouts.app', ['pageTitle' => 'Owner Settings - Nirmaan', 'activePage' => 'settings'])

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
    $initial = strtoupper(substr($settingsUser->first_name ?? 'O', 0, 1));
    $profileImageUrl = $settingsUser?->profile_image_url;
@endphp

<div class="heading-panel mb-4">
    <p class="text-uppercase text-primary fw-semibold mb-1 small">Owner Settings</p>
    <h1 class="fw-bold mb-2 h3 h-md-2">Profile & Security</h1>
    <p class="text-secondary mb-0">Update your profile details and account password.</p>
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

<form method="POST" action="{{ route('owner.settings.save') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row g-3 g-md-4">
        <div class="col-12 col-xl-7">
            <div class="panel-card p-4 p-md-5">
                <h2 class="h5 fw-bold mb-3">Basic Information</h2>
                <p class="helper-text mb-4">This information will be used across owner dashboard modules.</p>

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
    </div>
</form>
@endsection

