@extends('owner.layouts.app', ['pageTitle' => 'Edit Project - Nirmaan', 'activePage' => 'projects'])

@push('styles')
<style>
    .heading-panel {
        border: 0;
        border-radius: 1rem;
        background: linear-gradient(135deg, #ffffff 0%, #f2f6ff 100%);
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
        padding: 1rem;
    }

    .section-card {
        border: 0;
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
    }

    .section-title {
        font-size: 1.04rem;
        font-weight: 700;
        margin-bottom: 0.8rem;
    }

    .doc-item {
        border: 1px solid #e0e8fc;
        border-radius: 0.8rem;
        padding: 0.65rem 0.75rem;
        background: #fbfdff;
    }

    .doc-name {
        margin-bottom: 0.2rem;
        font-weight: 600;
        font-size: 0.92rem;
        word-break: break-word;
    }

    .doc-meta {
        color: #7883a1;
        font-size: 0.78rem;
        margin-bottom: 0;
    }

    @media (min-width: 992px) {
        .heading-panel {
            padding: 1.35rem 1.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="heading-panel mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3">
        <div>
            <p class="text-uppercase text-primary fw-semibold mb-1 small">Edit Project</p>
            <h1 class="fw-bold mb-2 h3 h-md-2">Update Project Details</h1>
            <p class="text-secondary mb-0">Make changes before contractor engagement progresses.</p>
        </div>
        <a href="{{ route('owner.projects.details', $project) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Details
        </a>
    </div>
</div>

@if ($errors->any())
<div class="alert alert-danger shadow-sm mb-4">
    <ul class="mb-0 ps-3">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('owner.projects.save_changes', $project) }}" enctype="multipart/form-data" class="d-flex flex-column gap-4">
    @csrf
    @method('PUT')

    <section class="section-card p-4 p-md-5">
        <h2 class="section-title">Project Basics</h2>
        <div class="row g-3">
            <div class="col-12 col-lg-8">
                <label for="title" class="form-label fw-semibold">Project Title</label>
                <input id="title" name="title" type="text" value="{{ old('title', $project->title) }}" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" required>
                @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12 col-lg-4">
                <label for="project_type" class="form-label fw-semibold">Project Type</label>
                <select id="project_type" name="project_type" class="form-select{{ $errors->has('project_type') ? ' is-invalid' : '' }}" required>
                    <option value="">Select Type</option>
                    @foreach (['Residential', 'Commercial', 'Industrial', 'Infrastructure', 'Renovation'] as $type)
                    <option value="{{ $type }}" @selected(old('project_type', $project->project_type) === $type)>{{ $type }}</option>
                    @endforeach
                </select>
                @error('project_type')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12">
                <label for="work_category" class="form-label fw-semibold">Work Category</label>
                <input id="work_category" name="work_category" type="text" value="{{ old('work_category', $project->work_category) }}" class="form-control{{ $errors->has('work_category') ? ' is-invalid' : '' }}">
                @error('work_category')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12">
                <label for="description" class="form-label fw-semibold">Project Description</label>
                <textarea id="description" name="description" rows="5" class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}" required>{{ old('description', $project->description) }}</textarea>
                @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </section>

    <section class="section-card p-4 p-md-5">
        <h2 class="section-title">Location</h2>
        <div class="row g-3">
            <div class="col-12">
                <label for="site_address" class="form-label fw-semibold">Site Address</label>
                <input id="site_address" name="site_address" type="text" value="{{ old('site_address', $project->site_address) }}" class="form-control{{ $errors->has('site_address') ? ' is-invalid' : '' }}" required>
                @error('site_address')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="area_locality" class="form-label fw-semibold">Area / Locality</label>
                <input id="area_locality" name="area_locality" type="text" value="{{ old('area_locality', $project->area_locality) }}" class="form-control{{ $errors->has('area_locality') ? ' is-invalid' : '' }}" required>
                @error('area_locality')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="landmark" class="form-label fw-semibold">Landmark</label>
                <input id="landmark" name="landmark" type="text" value="{{ old('landmark', $project->landmark) }}" class="form-control{{ $errors->has('landmark') ? ' is-invalid' : '' }}">
                @error('landmark')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="city" class="form-label fw-semibold">City</label>
                <input id="city" name="city" type="text" value="{{ old('city', $project->city) }}" class="form-control{{ $errors->has('city') ? ' is-invalid' : '' }}" required>
                @error('city')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="district" class="form-label fw-semibold">District</label>
                <input id="district" name="district" type="text" value="{{ old('district', $project->district) }}" class="form-control{{ $errors->has('district') ? ' is-invalid' : '' }}" required>
                @error('district')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="state" class="form-label fw-semibold">State</label>
                <input id="state" name="state" type="text" value="{{ old('state', $project->state) }}" class="form-control{{ $errors->has('state') ? ' is-invalid' : '' }}" required>
                @error('state')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="postal_code" class="form-label fw-semibold">PIN Code</label>
                <input id="postal_code" name="postal_code" type="text" value="{{ old('postal_code', $project->postal_code) }}" maxlength="6" class="form-control{{ $errors->has('postal_code') ? ' is-invalid' : '' }}" required>
                @error('postal_code')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </section>

    <section class="section-card p-4 p-md-5">
        <h2 class="section-title">Budget & Timeline</h2>
        <div class="row g-3">
            <div class="col-md-4">
                <label for="budget_currency" class="form-label fw-semibold">Currency</label>
                <input id="budget_currency" name="budget_currency" type="text" value="{{ old('budget_currency', $project->budget_currency) }}" class="form-control" readonly>
            </div>
            <div class="col-md-4">
                <label for="budget_min" class="form-label fw-semibold">Min Budget (&#8377;)</label>
                <input id="budget_min" name="budget_min" type="number" step="0.01" min="1000" value="{{ old('budget_min', $project->budget_min) }}" class="form-control{{ $errors->has('budget_min') ? ' is-invalid' : '' }}" required>
                @error('budget_min')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-4">
                <label for="budget_max" class="form-label fw-semibold">Max Budget (&#8377;)</label>
                <input id="budget_max" name="budget_max" type="number" step="0.01" min="1000" value="{{ old('budget_max', $project->budget_max) }}" class="form-control{{ $errors->has('budget_max') ? ' is-invalid' : '' }}">
                @error('budget_max')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="required_start_date" class="form-label fw-semibold">Preferred Start Date</label>
                <input id="required_start_date" name="required_start_date" type="date" value="{{ old('required_start_date', optional($project->required_start_date)->format('Y-m-d')) }}" class="form-control{{ $errors->has('required_start_date') ? ' is-invalid' : '' }}">
                @error('required_start_date')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="deadline" class="form-label fw-semibold">Deadline</label>
                <input id="deadline" name="deadline" type="date" value="{{ old('deadline', optional($project->deadline)->format('Y-m-d')) }}" class="form-control{{ $errors->has('deadline') ? ' is-invalid' : '' }}" required>
                @error('deadline')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="expected_duration_days" class="form-label fw-semibold">Expected Duration (Days)</label>
                <input id="expected_duration_days" name="expected_duration_days" type="number" min="1" value="{{ old('expected_duration_days', $project->expected_duration_days) }}" class="form-control{{ $errors->has('expected_duration_days') ? ' is-invalid' : '' }}">
                @error('expected_duration_days')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </section>

    <section class="section-card p-4 p-md-5">
        <h2 class="section-title">Execution Preferences</h2>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="labor_strength_required" class="form-label fw-semibold">Labor Strength Required</label>
                <input id="labor_strength_required" name="labor_strength_required" type="number" min="1" value="{{ old('labor_strength_required', $project->labor_strength_required) }}" class="form-control{{ $errors->has('labor_strength_required') ? ' is-invalid' : '' }}">
                @error('labor_strength_required')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="material_supply_mode" class="form-label fw-semibold">Material Supply</label>
                <select id="material_supply_mode" name="material_supply_mode" class="form-select{{ $errors->has('material_supply_mode') ? ' is-invalid' : '' }}" required>
                    <option value="shared" @selected(old('material_supply_mode', $project->material_supply_mode) === 'shared')>Shared (Owner + Contractor)</option>
                    <option value="owner" @selected(old('material_supply_mode', $project->material_supply_mode) === 'owner')>Owner Managed</option>
                    <option value="contractor" @selected(old('material_supply_mode', $project->material_supply_mode) === 'contractor')>Contractor Managed</option>
                </select>
                @error('material_supply_mode')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="visibility" class="form-label fw-semibold">Project Visibility</label>
                <select id="visibility" name="visibility" class="form-select{{ $errors->has('visibility') ? ' is-invalid' : '' }}" required>
                    <option value="public" @selected(old('visibility', $project->visibility) === 'public')>Public (All Contractors)</option>
                    <option value="invite_only" @selected(old('visibility', $project->visibility) === 'invite_only')>Invite Only</option>
                </select>
                @error('visibility')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="preferred_language" class="form-label fw-semibold">Preferred Language</label>
                <select id="preferred_language" name="preferred_language" class="form-select{{ $errors->has('preferred_language') ? ' is-invalid' : '' }}" required>
                    @foreach (['English', 'Hindi', 'Gujarati', 'Marathi', 'Tamil', 'Telugu', 'Kannada', 'Malayalam', 'Bengali'] as $lang)
                    <option value="{{ $lang }}" @selected(old('preferred_language', $project->preferred_language) === $lang)>{{ $lang }}</option>
                    @endforeach
                </select>
                @error('preferred_language')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12">
                <label for="quality_expectations" class="form-label fw-semibold">Quality Expectations</label>
                <textarea id="quality_expectations" name="quality_expectations" rows="3" class="form-control{{ $errors->has('quality_expectations') ? ' is-invalid' : '' }}">{{ old('quality_expectations', $project->quality_expectations) }}</textarea>
                @error('quality_expectations')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12">
                <label for="safety_requirements" class="form-label fw-semibold">Safety Requirements</label>
                <textarea id="safety_requirements" name="safety_requirements" rows="3" class="form-control{{ $errors->has('safety_requirements') ? ' is-invalid' : '' }}">{{ old('safety_requirements', $project->safety_requirements) }}</textarea>
                @error('safety_requirements')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12">
                <label for="project_documents" class="form-label fw-semibold">Add More Documents (Optional)</label>
                <input id="project_documents" name="project_documents[]" type="file" multiple class="form-control{{ $errors->has('project_documents') || $errors->has('project_documents.*') ? ' is-invalid' : '' }}" accept=".pdf,.jpg,.jpeg,.png,.webp,.dwg,.dxf,.doc,.docx,.xls,.xlsx">
                @if($errors->has('project_documents') || $errors->has('project_documents.*'))
                <div class="invalid-feedback d-block">
                    {{ $errors->first('project_documents') ?: $errors->first('project_documents.*') }}
                </div>
                @endif
                <p class="text-secondary small mb-0 mt-2">Already uploaded files stay attached. New files will be added.</p>
            </div>
        </div>
    </section>

    <section class="section-card p-4 p-md-5">
        <h2 class="section-title">Current Documents</h2>
        @if($project->projectDocuments->isEmpty())
        <p class="text-secondary mb-0">No documents uploaded yet.</p>
        @else
        <div class="row g-2">
            @foreach($project->projectDocuments as $doc)
            <div class="col-12">
                <div class="doc-item d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                    <div>
                        <p class="doc-name">{{ $doc->original_name }}</p>
                        <p class="doc-meta">
                            {{ strtoupper((string) pathinfo($doc->original_name, PATHINFO_EXTENSION)) ?: 'FILE' }}
                            &middot; {{ $doc->file_size ? number_format($doc->file_size / 1024, 1).' KB' : 'Size unavailable' }}
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ $doc->file_url }}" target="_blank" class="btn btn-outline-primary btn-sm">Open</a>
                        <a href="{{ $doc->file_url }}" download class="btn btn-outline-secondary btn-sm">Download</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </section>

    <div class="d-flex flex-wrap gap-2 justify-content-between">
        <a href="{{ route('owner.projects.details', $project) }}" class="btn btn-outline-secondary px-4">Cancel</a>
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-check2-circle me-1"></i>Save Project Changes
        </button>
    </div>
</form>
@endsection
