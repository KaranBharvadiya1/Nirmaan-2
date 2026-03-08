@extends('owner.layouts.app', ['pageTitle' => 'Create Project - Nirmaan', 'activePage' => 'projects'])

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

    .hint-text {
        color: #68708a;
        font-size: 0.88rem;
    }

    .wizard-step {
        display: none;
    }

    .wizard-step.active {
        display: block;
    }

    .step-pill {
        border: 1px solid #dce4f8;
        color: #5d6681;
        background: #fff;
        border-radius: 999px;
        padding: 0.4rem 0.75rem;
        font-size: 0.82rem;
        font-weight: 600;
    }

    .step-pill.active {
        background: #2452e6;
        color: #fff;
        border-color: #2452e6;
    }

    .step-pill.done {
        background: #e8f0ff;
        color: #1b3b94;
        border-color: #b8ccff;
    }

    .review-box {
        border: 1px solid #e0e6f6;
        border-radius: 0.9rem;
        padding: 0.8rem;
        height: 100%;
        background: #fbfcff;
    }

    .review-label {
        color: #6d7691;
        font-size: 0.79rem;
        margin-bottom: 0.2rem;
    }

    .review-value {
        font-weight: 600;
        margin-bottom: 0;
    }

    .draft-indicator {
        font-size: 0.82rem;
        color: #617096;
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
    <p class="text-uppercase text-primary fw-semibold mb-1 small">Post Project</p>
    <h1 class="fw-bold mb-2 h3 h-md-2">Step-wise Project Creation</h1>
    <p class="text-secondary mb-0">Fill details in 5 steps with auto-saved draft support.</p>
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

<div class="section-card p-4 p-md-5 mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
        <div class="d-flex flex-wrap gap-2" id="wizardStepPills">
            <span class="step-pill active" data-step-pill="1">1. Project Basics</span>
            <span class="step-pill" data-step-pill="2">2. Location</span>
            <span class="step-pill" data-step-pill="3">3. Budget & Timeline</span>
            <span class="step-pill" data-step-pill="4">4. Execution Preferences</span>
            <span class="step-pill" data-step-pill="5">5. Review & Publish</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span id="draftStatus" class="draft-indicator">Draft not saved yet</span>
            <button id="clearDraftBtn" type="button" class="btn btn-outline-secondary btn-sm">Clear Draft</button>
        </div>
    </div>

    <div class="progress mb-4" style="height: 0.55rem;">
        <div id="wizardProgressBar" class="progress-bar bg-primary" role="progressbar" style="width: 20%;"></div>
    </div>

    <form id="projectWizardForm" method="POST" action="{{ route('owner.projects.save') }}" enctype="multipart/form-data">
        @csrf

        <section class="wizard-step active" data-step="1">
            <h2 class="h5 fw-bold mb-3">Project Basics</h2>
            <p class="hint-text mb-4">Set clear scope so both local and professional contractors understand your requirements.</p>
            <div class="row g-3">
                <div class="col-12 col-lg-8">
                    <label for="title" class="form-label fw-semibold">Project Title</label>
                    <input id="title" name="title" type="text" value="{{ old('title') }}" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" placeholder="Example: G+2 Residential Building Construction" required>
                    @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 col-lg-4">
                    <label for="project_type" class="form-label fw-semibold">Project Type</label>
                    <select id="project_type" name="project_type" class="form-select{{ $errors->has('project_type') ? ' is-invalid' : '' }}" required>
                        <option value="">Select Type</option>
                        @foreach (['Residential', 'Commercial', 'Industrial', 'Infrastructure', 'Renovation'] as $type)
                        <option value="{{ $type }}" @selected(old('project_type') === $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                    @error('project_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12">
                    <label for="work_category" class="form-label fw-semibold">Work Category</label>
                    <input id="work_category" name="work_category" type="text" value="{{ old('work_category') }}" class="form-control{{ $errors->has('work_category') ? ' is-invalid' : '' }}" placeholder="Concrete, Tiles, Plumbing, Electrical, Interior">
                    @error('work_category')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12">
                    <label for="description" class="form-label fw-semibold">Project Description</label>
                    <textarea id="description" name="description" rows="5" class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}" placeholder="Describe project scope, materials, quality expectations, and local site conditions." required>{{ old('description') }}</textarea>
                    @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </section>

        <section class="wizard-step" data-step="2">
            <h2 class="h5 fw-bold mb-3">Location</h2>
            <p class="hint-text mb-4">Accurate local address and PIN details improve nearby contractor reach.</p>
            <div class="row g-3">
                <div class="col-12">
                    <label for="site_address" class="form-label fw-semibold">Site Address</label>
                    <input id="site_address" name="site_address" type="text" value="{{ old('site_address') }}" class="form-control{{ $errors->has('site_address') ? ' is-invalid' : '' }}" required>
                    @error('site_address')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="area_locality" class="form-label fw-semibold">Area / Locality</label>
                    <input id="area_locality" name="area_locality" type="text" value="{{ old('area_locality') }}" class="form-control{{ $errors->has('area_locality') ? ' is-invalid' : '' }}" required>
                    @error('area_locality')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="landmark" class="form-label fw-semibold">Landmark</label>
                    <input id="landmark" name="landmark" type="text" value="{{ old('landmark') }}" class="form-control{{ $errors->has('landmark') ? ' is-invalid' : '' }}">
                    @error('landmark')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="city" class="form-label fw-semibold">City</label>
                    <input id="city" name="city" type="text" value="{{ old('city') }}" class="form-control{{ $errors->has('city') ? ' is-invalid' : '' }}" required>
                    @error('city')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="district" class="form-label fw-semibold">District</label>
                    <input id="district" name="district" type="text" value="{{ old('district') }}" class="form-control{{ $errors->has('district') ? ' is-invalid' : '' }}" required>
                    @error('district')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="state" class="form-label fw-semibold">State</label>
                    <input id="state" name="state" type="text" value="{{ old('state') }}" class="form-control{{ $errors->has('state') ? ' is-invalid' : '' }}" required>
                    @error('state')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="postal_code" class="form-label fw-semibold">PIN Code</label>
                    <input id="postal_code" name="postal_code" type="text" value="{{ old('postal_code') }}" class="form-control{{ $errors->has('postal_code') ? ' is-invalid' : '' }}" maxlength="6" required>
                    @error('postal_code')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </section>

        <section class="wizard-step" data-step="3">
            <h2 class="h5 fw-bold mb-3">Budget & Timeline</h2>
            <p class="hint-text mb-4">Professional bids improve when budget and deadlines are transparent.</p>
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="budget_currency" class="form-label fw-semibold">Currency</label>
                    <input id="budget_currency" name="budget_currency" type="text" value="{{ old('budget_currency', 'INR') }}" class="form-control" readonly>
                </div>
                <div class="col-md-4">
                    <label for="budget_min" class="form-label fw-semibold">Min Budget (&#8377;)</label>
                    <input id="budget_min" name="budget_min" type="number" step="0.01" min="1000" value="{{ old('budget_min') }}" class="form-control{{ $errors->has('budget_min') ? ' is-invalid' : '' }}" required>
                    @error('budget_min')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="budget_max" class="form-label fw-semibold">Max Budget (&#8377;)</label>
                    <input id="budget_max" name="budget_max" type="number" step="0.01" min="1000" value="{{ old('budget_max') }}" class="form-control{{ $errors->has('budget_max') ? ' is-invalid' : '' }}">
                    @error('budget_max')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="required_start_date" class="form-label fw-semibold">Preferred Start Date</label>
                    <input id="required_start_date" name="required_start_date" type="date" value="{{ old('required_start_date') }}" class="form-control{{ $errors->has('required_start_date') ? ' is-invalid' : '' }}">
                    @error('required_start_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="deadline" class="form-label fw-semibold">Deadline</label>
                    <input id="deadline" name="deadline" type="date" value="{{ old('deadline') }}" class="form-control{{ $errors->has('deadline') ? ' is-invalid' : '' }}" required>
                    @error('deadline')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="expected_duration_days" class="form-label fw-semibold">Expected Duration (Days)</label>
                    <input id="expected_duration_days" name="expected_duration_days" type="number" min="1" value="{{ old('expected_duration_days') }}" class="form-control{{ $errors->has('expected_duration_days') ? ' is-invalid' : '' }}">
                    @error('expected_duration_days')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </section>

        <section class="wizard-step" data-step="4">
            <h2 class="h5 fw-bold mb-3">Execution Preferences</h2>
            <p class="hint-text mb-4">Set site execution style to attract contractors matching your standards.</p>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="labor_strength_required" class="form-label fw-semibold">Labor Strength Required</label>
                    <input id="labor_strength_required" name="labor_strength_required" type="number" min="1" value="{{ old('labor_strength_required') }}" class="form-control{{ $errors->has('labor_strength_required') ? ' is-invalid' : '' }}">
                    @error('labor_strength_required')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="material_supply_mode" class="form-label fw-semibold">Material Supply</label>
                    <select id="material_supply_mode" name="material_supply_mode" class="form-select{{ $errors->has('material_supply_mode') ? ' is-invalid' : '' }}" required>
                        <option value="shared" @selected(old('material_supply_mode', 'shared') === 'shared')>Shared (Owner + Contractor)</option>
                        <option value="owner" @selected(old('material_supply_mode') === 'owner')>Owner Managed</option>
                        <option value="contractor" @selected(old('material_supply_mode') === 'contractor')>Contractor Managed</option>
                    </select>
                    @error('material_supply_mode')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="visibility" class="form-label fw-semibold">Project Visibility</label>
                    <select id="visibility" name="visibility" class="form-select{{ $errors->has('visibility') ? ' is-invalid' : '' }}" required>
                        <option value="public" @selected(old('visibility', 'public') === 'public')>Public (All Contractors)</option>
                        <option value="invite_only" @selected(old('visibility') === 'invite_only')>Invite Only</option>
                    </select>
                    @error('visibility')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="preferred_language" class="form-label fw-semibold">Preferred Language</label>
                    <select id="preferred_language" name="preferred_language" class="form-select{{ $errors->has('preferred_language') ? ' is-invalid' : '' }}" required>
                        @foreach (['English', 'Hindi', 'Gujarati', 'Marathi', 'Tamil', 'Telugu', 'Kannada', 'Malayalam', 'Bengali'] as $lang)
                        <option value="{{ $lang }}" @selected(old('preferred_language', 'English') === $lang)>{{ $lang }}</option>
                        @endforeach
                    </select>
                    @error('preferred_language')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12">
                    <label for="quality_expectations" class="form-label fw-semibold">Quality Expectations</label>
                    <textarea id="quality_expectations" name="quality_expectations" rows="3" class="form-control{{ $errors->has('quality_expectations') ? ' is-invalid' : '' }}" placeholder="Example: IS code compliance, finish quality, material grade standards">{{ old('quality_expectations') }}</textarea>
                    @error('quality_expectations')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12">
                    <label for="safety_requirements" class="form-label fw-semibold">Safety Requirements</label>
                    <textarea id="safety_requirements" name="safety_requirements" rows="3" class="form-control{{ $errors->has('safety_requirements') ? ' is-invalid' : '' }}" placeholder="Example: PPE mandatory, site induction, helmet & harness compliance">{{ old('safety_requirements') }}</textarea>
                    @error('safety_requirements')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12">
                    <label for="project_documents" class="form-label fw-semibold">Blueprints / Plans / BOQ Documents</label>
                    <input id="project_documents" name="project_documents[]" type="file" multiple class="form-control{{ $errors->has('project_documents') || $errors->has('project_documents.*') ? ' is-invalid' : '' }}" accept=".pdf,.jpg,.jpeg,.png,.webp,.dwg,.dxf,.doc,.docx,.xls,.xlsx">
                    @if($errors->has('project_documents') || $errors->has('project_documents.*'))
                    <div class="invalid-feedback d-block">
                        {{ $errors->first('project_documents') ?: $errors->first('project_documents.*') }}
                    </div>
                    @endif
                    <p class="hint-text mb-0 mt-2">Upload up to 8 files. Allowed: PDF, image, CAD, DOC/DOCX, XLS/XLSX (max 20MB each).</p>
                </div>
            </div>
        </section>

        <section class="wizard-step" data-step="5">
            <h2 class="h5 fw-bold mb-3">Review & Publish</h2>
            <p class="hint-text mb-4">Review all details before publishing your project.</p>

            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <div class="review-box">
                        <p class="review-label">Project Title</p>
                        <p class="review-value" data-review="title">-</p>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="review-box">
                        <p class="review-label">Project Type / Category</p>
                        <p class="review-value" data-review="projectTypeCategory">-</p>
                    </div>
                </div>
                <div class="col-12">
                    <div class="review-box">
                        <p class="review-label">Location Summary</p>
                        <p class="review-value" data-review="location">-</p>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="review-box">
                        <p class="review-label">Budget</p>
                        <p class="review-value" data-review="budget">-</p>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="review-box">
                        <p class="review-label">Timeline</p>
                        <p class="review-value" data-review="timeline">-</p>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="review-box">
                        <p class="review-label">Execution</p>
                        <p class="review-value" data-review="execution">-</p>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="review-box">
                        <p class="review-label">Language / Visibility</p>
                        <p class="review-value" data-review="langVisibility">-</p>
                    </div>
                </div>
                <div class="col-12">
                    <div class="review-box">
                        <p class="review-label">Uploaded Documents</p>
                        <p class="review-value" data-review="documents">No files selected</p>
                    </div>
                </div>
            </div>
        </section>

        <div class="d-flex flex-wrap gap-2 justify-content-between mt-4">
            <button id="prevStepBtn" type="button" class="btn btn-outline-secondary px-4" disabled>Previous</button>
            <div class="d-flex gap-2">
                <a href="{{ route('owner.projects') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                <button id="nextStepBtn" type="button" class="btn btn-primary px-4">Next</button>
                <button id="publishBtn" type="submit" class="btn btn-success px-4 d-none">Publish Project</button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('projectWizardForm');
        const steps = Array.from(document.querySelectorAll('.wizard-step'));
        const stepPills = Array.from(document.querySelectorAll('[data-step-pill]'));
        const progressBar = document.getElementById('wizardProgressBar');
        const prevBtn = document.getElementById('prevStepBtn');
        const nextBtn = document.getElementById('nextStepBtn');
        const publishBtn = document.getElementById('publishBtn');
        const draftStatus = document.getElementById('draftStatus');
        const clearDraftBtn = document.getElementById('clearDraftBtn');
        const draftKey = 'owner_project_draft_v1';
        let currentStep = 1;

        const fields = Array.from(form.querySelectorAll('input[name], select[name], textarea[name]'))
            .filter(el => !['_token', '_method'].includes(el.name))
            .filter(el => el.type !== 'file');
        const documentInput = form.querySelector('#project_documents');

        function getStepFields(stepNumber) {
            const stepEl = document.querySelector('.wizard-step[data-step="' + stepNumber + '"]');
            if (!stepEl) return [];
            return Array.from(stepEl.querySelectorAll('input, select, textarea'));
        }

        function validateCurrentStep() {
            const currentFields = getStepFields(currentStep);
            for (const field of currentFields) {
                if (typeof field.checkValidity === 'function' && !field.checkValidity()) {
                    field.reportValidity();
                    return false;
                }
            }
            return true;
        }

        function updateReview() {
            const val = (name) => {
                const el = form.querySelector('[name="' + name + '"]');
                return el ? el.value.trim() : '';
            };

            const reviewMap = {
                title: val('title') || '-',
                projectTypeCategory: [val('project_type'), val('work_category')].filter(Boolean).join(' / ') || '-',
                location: [val('site_address'), val('area_locality'), val('city'), val('district'), val('state'), val('postal_code'), val('landmark')].filter(Boolean).join(', ') || '-',
                budget: 'INR ' + (val('budget_min') || '0') + (val('budget_max') ? ' - INR ' + val('budget_max') : ''),
                timeline: [val('required_start_date') ? ('Start: ' + val('required_start_date')) : '', val('deadline') ? ('Deadline: ' + val('deadline')) : '', val('expected_duration_days') ? ('Duration: ' + val('expected_duration_days') + ' days') : ''].filter(Boolean).join(' | ') || '-',
                execution: [val('material_supply_mode'), val('labor_strength_required') ? ('Labor: ' + val('labor_strength_required')) : '', val('quality_expectations') ? 'Quality noted' : '', val('safety_requirements') ? 'Safety noted' : ''].filter(Boolean).join(' | ') || '-',
                langVisibility: [val('preferred_language'), val('visibility')].filter(Boolean).join(' / ') || '-',
                documents: (documentInput && documentInput.files.length > 0)
                    ? (documentInput.files.length + ' file(s) selected')
                    : 'No files selected'
            };

            Object.keys(reviewMap).forEach(function (key) {
                const el = document.querySelector('[data-review="' + key + '"]');
                if (el) el.textContent = reviewMap[key];
            });
        }

        function showStep(stepNumber) {
            currentStep = Math.max(1, Math.min(5, stepNumber));

            steps.forEach(function (stepEl) {
                const step = Number(stepEl.dataset.step);
                stepEl.classList.toggle('active', step === currentStep);
            });

            stepPills.forEach(function (pill) {
                const step = Number(pill.dataset.stepPill);
                pill.classList.toggle('active', step === currentStep);
                pill.classList.toggle('done', step < currentStep);
            });

            progressBar.style.width = (currentStep * 20) + '%';
            prevBtn.disabled = currentStep === 1;

            if (currentStep === 5) {
                nextBtn.classList.add('d-none');
                publishBtn.classList.remove('d-none');
                updateReview();
            } else {
                nextBtn.classList.remove('d-none');
                publishBtn.classList.add('d-none');
            }
        }

        function saveDraft() {
            const data = {};
            fields.forEach(function (field) {
                if (field.type === 'radio' || field.type === 'checkbox') {
                    if (field.checked) data[field.name] = field.value;
                } else {
                    data[field.name] = field.value;
                }
            });

            localStorage.setItem(draftKey, JSON.stringify(data));
            draftStatus.textContent = 'Draft auto-saved at ' + new Date().toLocaleTimeString();
        }

        function loadDraft() {
            const raw = localStorage.getItem(draftKey);
            if (!raw) return;

            let data = null;
            try {
                data = JSON.parse(raw);
            } catch (e) {
                return;
            }

            fields.forEach(function (field) {
                if (!data || typeof data[field.name] === 'undefined') return;
                if (field.value !== '') return;

                if (field.type === 'radio' || field.type === 'checkbox') {
                    field.checked = field.value === data[field.name];
                } else {
                    field.value = data[field.name];
                }
            });

            draftStatus.textContent = 'Draft loaded from local browser storage';
        }

        let saveTimer = null;
        fields.forEach(function (field) {
            field.addEventListener('input', function () {
                window.clearTimeout(saveTimer);
                saveTimer = window.setTimeout(saveDraft, 350);
            });
            field.addEventListener('change', saveDraft);
        });

        if (documentInput) {
            documentInput.addEventListener('change', function () {
                if (currentStep === 5) {
                    updateReview();
                }
            });
        }

        nextBtn.addEventListener('click', function () {
            if (!validateCurrentStep()) return;
            showStep(currentStep + 1);
        });

        prevBtn.addEventListener('click', function () {
            showStep(currentStep - 1);
        });

        clearDraftBtn.addEventListener('click', function () {
            localStorage.removeItem(draftKey);
            draftStatus.textContent = 'Draft cleared';
        });

        form.addEventListener('submit', function () {
            saveDraft();
        });

        loadDraft();
        showStep(1);
    });
</script>
@endpush
