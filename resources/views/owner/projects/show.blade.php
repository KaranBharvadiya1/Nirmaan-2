@extends('owner.layouts.app', ['pageTitle' => 'Project Details - Nirmaan', 'activePage' => 'projects'])

@push('styles')
<style>
    .hero-panel {
        border: 0;
        border-radius: 1rem;
        background: linear-gradient(145deg, #ffffff 0%, #edf3ff 60%, #d9e7ff 100%);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        padding: 1rem;
    }

    .status-select-wrap {
        min-width: 200px;
    }

    .mini-stat {
        border: 1px solid #dce5f8;
        border-radius: 0.85rem;
        background: #fff;
        padding: 0.65rem 0.8rem;
    }

    .mini-label {
        color: #6a7490;
        font-size: 0.78rem;
        margin-bottom: 0.15rem;
    }

    .mini-value {
        font-size: 0.95rem;
        font-weight: 700;
        margin: 0;
    }

    .panel-card {
        border: 0;
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.07);
    }

    .section-title {
        font-size: 1.06rem;
        font-weight: 700;
        margin-bottom: 0.85rem;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
        gap: 0.75rem;
    }

    .detail-item {
        border: 1px solid #e5ebfb;
        border-radius: 0.75rem;
        padding: 0.6rem 0.7rem;
        background: #fbfcff;
    }

    .detail-label {
        color: #6c7692;
        font-size: 0.78rem;
        margin-bottom: 0.15rem;
    }

    .detail-value {
        margin-bottom: 0;
        font-size: 0.92rem;
        font-weight: 600;
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

    @media (min-width: 1200px) {
        .sticky-info {
            position: sticky;
            top: 1.25rem;
        }
    }

    @media (min-width: 992px) {
        .hero-panel {
            padding: 1.2rem 1.3rem;
        }
    }
</style>
@endpush

@section('content')
@php
    $statusBadgeClass = match ($project->status) {
        'open' => 'text-bg-primary',
        'in_progress' => 'text-bg-warning',
        'completed' => 'text-bg-success',
        'cancelled' => 'text-bg-danger',
        default => 'text-bg-secondary',
    };

    $statusUpdateOptions = [
        'open' => 'Open (Reopen)',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];
    $bidStatusOptions = [
        'pending' => 'Pending',
        'shortlisted' => 'Shortlisted',
        'accepted' => 'Accepted',
        'rejected' => 'Rejected',
    ];
    $bidStatusBadgeClass = function (string $status): string {
        return match ($status) {
            'pending' => 'text-bg-secondary',
            'shortlisted' => 'text-bg-info',
            'accepted' => 'text-bg-success',
            'rejected' => 'text-bg-danger',
            default => 'text-bg-dark',
        };
    };

    $canModify = in_array($project->status, ['open', 'cancelled'], true);
    $canDelete = $canModify && $project->bids->isEmpty();
@endphp

<div class="hero-panel mb-3 mb-md-4" data-project-card="{{ $project->id }}">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-3">
        <div>
            <p class="text-uppercase text-primary fw-semibold mb-1 small">Project Details</p>
            <h1 class="fw-bold mb-2 h4 h-md-3">{{ $project->title }}</h1>
            <div class="d-flex flex-wrap gap-2">
                <span class="badge text-bg-light border">{{ $project->reference_code }}</span>
                <span class="badge {{ $statusBadgeClass }}" data-project-status-badge="{{ $project->id }}">{{ str_replace('_', ' ', ucfirst($project->status)) }}</span>
                <span class="badge text-bg-light border">{{ $project->project_type }}</span>
                <span class="badge text-bg-light border">{{ $project->work_category ?: 'General Construction' }}</span>
            </div>
        </div>

        <div class="d-flex flex-wrap align-items-start gap-2">
            <div class="status-select-wrap">
                <label for="project-status-detail-{{ $project->id }}" class="form-label small fw-semibold mb-1">Project Status</label>
                <select
                    id="project-status-detail-{{ $project->id }}"
                    class="form-select form-select-sm js-project-status-select"
                    data-project-id="{{ $project->id }}"
                    data-status-url="{{ route('owner.projects.change_status', $project) }}"
                    data-current-status="{{ $project->status }}"
                >
                    @foreach ($statusUpdateOptions as $statusValue => $statusLabel)
                    <option value="{{ $statusValue }}" @selected($project->status === $statusValue)>{{ $statusLabel }}</option>
                    @endforeach
                </select>
            </div>

            <a href="{{ route('owner.projects.edit', $project) }}" class="btn btn-outline-primary btn-sm {{ $canModify ? '' : 'disabled' }}" data-project-edit-action="{{ $project->id }}" @if (! $canModify) aria-disabled="true" tabindex="-1" @endif>
                <i class="bi bi-pencil-square me-1"></i>Edit
            </a>

            <form method="POST" action="{{ route('owner.projects.delete', $project) }}" class="js-confirm-submit" data-confirm-message="Delete this project permanently?">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm" data-project-delete-action="{{ $project->id }}" data-has-bids="{{ $project->bids->isNotEmpty() ? '1' : '0' }}" @disabled(! $canDelete)>
                    <i class="bi bi-trash me-1"></i>Delete
                </button>
            </form>

            <a href="{{ route('owner.projects') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <div class="row g-2 g-md-3">
        <div class="col-6 col-lg-3">
            <div class="mini-stat">
                <p class="mini-label">Budget</p>
                <p class="mini-value">
                    &#8377;{{ number_format((float) $project->budget_min) }}
                    @if($project->budget_max)
                    - &#8377;{{ number_format((float) $project->budget_max) }}
                    @endif
                </p>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="mini-stat">
                <p class="mini-label">Deadline</p>
                <p class="mini-value">{{ $project->deadline?->format('d M Y') }}</p>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="mini-stat">
                <p class="mini-label">Duration</p>
                <p class="mini-value">{{ $project->expected_duration_days ? $project->expected_duration_days.' days' : 'Not set' }}</p>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="mini-stat">
                <p class="mini-label">Docs</p>
                <p class="mini-value">{{ $project->projectDocuments->count() }} file(s)</p>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="mini-stat">
                <p class="mini-label">Bids</p>
                <p class="mini-value">{{ $project->bids->count() }}</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 g-md-4">
    <div class="col-12 col-xl-8">
        <div class="panel-card p-3 p-md-4 mb-3">
            <h2 class="section-title">Scope Description</h2>
            <p class="text-secondary mb-0">{{ $project->description }}</p>
        </div>

        <div class="panel-card p-3 p-md-4 mb-3">
            <h2 class="section-title">Site & Locality</h2>
            <div class="detail-grid">
                <div class="detail-item">
                    <p class="detail-label">Site Address</p>
                    <p class="detail-value">{{ $project->site_address }}</p>
                </div>
                <div class="detail-item">
                    <p class="detail-label">Area / Locality</p>
                    <p class="detail-value">{{ $project->area_locality }}</p>
                </div>
                <div class="detail-item">
                    <p class="detail-label">City</p>
                    <p class="detail-value">{{ $project->city }}</p>
                </div>
                <div class="detail-item">
                    <p class="detail-label">District</p>
                    <p class="detail-value">{{ $project->district }}</p>
                </div>
                <div class="detail-item">
                    <p class="detail-label">State</p>
                    <p class="detail-value">{{ $project->state }}</p>
                </div>
                <div class="detail-item">
                    <p class="detail-label">PIN Code</p>
                    <p class="detail-value">{{ $project->postal_code }}</p>
                </div>
                <div class="detail-item">
                    <p class="detail-label">Landmark</p>
                    <p class="detail-value">{{ $project->landmark ?: 'Not specified' }}</p>
                </div>
            </div>
        </div>

        <div class="panel-card p-3 p-md-4">
            <h2 class="section-title">Blueprints / Project Documents</h2>
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
        </div>

        <div class="panel-card p-3 p-md-4 mt-3">
            <h2 class="section-title">Bids Received</h2>
            @if($project->bids->isEmpty())
            <p class="text-secondary mb-0">No bids received yet.</p>
            @else
            <div class="d-flex flex-column gap-2">
                @foreach($project->bids as $bid)
                <div class="detail-item">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-2">
                        <div>
                            <p class="detail-value mb-1">
                                {{ trim(($bid->contractor->first_name ?? '').' '.($bid->contractor->last_name ?? '')) ?: 'Contractor #'.$bid->contractor_id }}
                            </p>
                            <p class="detail-label mb-1">{{ $bid->contractor->email ?? '-' }}</p>
                            <p class="mb-1 small fw-semibold">&#8377;{{ number_format((float) $bid->quote_amount, 2) }}</p>
                            <p class="small mb-1 text-secondary">
                                Timeline: {{ $bid->proposed_timeline_days ? $bid->proposed_timeline_days.' days' : 'Not specified' }}
                            </p>
                            @if($bid->cover_message)
                            <p class="small text-secondary mb-0">{{ $bid->cover_message }}</p>
                            @endif
                            <div class="mt-2">
                                <a href="{{ route('contractors.portfolio.show', $bid->contractor) }}" class="btn btn-outline-secondary btn-sm">View Portfolio</a>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-1">
                            <span class="badge {{ $bidStatusBadgeClass($bid->status) }}" data-bid-status-badge="{{ $bid->id }}">{{ ucfirst($bid->status) }}</span>
                            <select
                                class="form-select form-select-sm js-bid-status-select"
                                data-bid-id="{{ $bid->id }}"
                                data-project-id="{{ $bid->project_id }}"
                                data-status-url="{{ route('owner.bids.change_status', $bid) }}"
                                data-current-status="{{ $bid->status }}"
                                @disabled(in_array($bid->status, ['accepted', 'withdrawn'], true))
                            >
                                @foreach ($bidStatusOptions as $bidStatusValue => $bidStatusLabel)
                                <option value="{{ $bidStatusValue }}" @selected($bid->status === $bidStatusValue)>{{ $bidStatusLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="sticky-info d-flex flex-column gap-3">
            @if($project->hire)
            <div class="panel-card p-3 p-md-4">
                <h2 class="section-title">Current Hire</h2>
                <div class="detail-item mb-2">
                    <p class="detail-label">Contractor</p>
                    <p class="detail-value">
                        {{ trim(($project->hire->contractor->first_name ?? '').' '.($project->hire->contractor->last_name ?? '')) ?: 'Contractor #'.$project->hire->contractor_id }}
                    </p>
                </div>
                <div class="detail-item mb-2">
                    <p class="detail-label">Status</p>
                    <p class="detail-value text-capitalize">{{ $project->hire->status }}</p>
                </div>
                <div class="detail-item mb-2">
                    <p class="detail-label">Agreed Amount</p>
                    <p class="detail-value">&#8377;{{ number_format((float) $project->hire->agreed_amount, 2) }}</p>
                </div>
                <div class="detail-item mb-3">
                    <p class="detail-label">Agreed Timeline</p>
                    <p class="detail-value">{{ $project->hire->agreed_timeline_days ? $project->hire->agreed_timeline_days.' days' : 'Not set' }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('owner.hires') }}" class="btn btn-outline-primary btn-sm">Open Hires</a>
                    <a href="{{ route('contractors.portfolio.show', $project->hire->contractor) }}" class="btn btn-outline-secondary btn-sm">View Portfolio</a>
                </div>
            </div>
            @endif

            <div class="panel-card p-3 p-md-4">
                <h2 class="section-title">Timeline & Budget</h2>
                <div class="detail-item mb-2">
                    <p class="detail-label">Preferred Start Date</p>
                    <p class="detail-value">{{ $project->required_start_date?->format('d M Y') ?: 'Flexible' }}</p>
                </div>
                <div class="detail-item mb-2">
                    <p class="detail-label">Deadline</p>
                    <p class="detail-value">{{ $project->deadline?->format('d M Y') }}</p>
                </div>
                <div class="detail-item">
                    <p class="detail-label">Budget Currency</p>
                    <p class="detail-value">{{ $project->budget_currency }}</p>
                </div>
            </div>

            <div class="panel-card p-3 p-md-4">
                <h2 class="section-title">Execution Preferences</h2>
                <div class="detail-item mb-2">
                    <p class="detail-label">Material Supply</p>
                    <p class="detail-value text-capitalize">{{ $project->material_supply_mode }}</p>
                </div>
                <div class="detail-item mb-2">
                    <p class="detail-label">Labor Strength</p>
                    <p class="detail-value">{{ $project->labor_strength_required ?: 'Not specified' }}</p>
                </div>
                <div class="detail-item mb-2">
                    <p class="detail-label">Preferred Language</p>
                    <p class="detail-value">{{ $project->preferred_language }}</p>
                </div>
                <div class="detail-item mb-2">
                    <p class="detail-label">Safety Requirements</p>
                    <p class="detail-value">{{ $project->safety_requirements ?: 'Not specified' }}</p>
                </div>
                <div class="detail-item">
                    <p class="detail-label">Quality Expectations</p>
                    <p class="detail-value">{{ $project->quality_expectations ?: 'Not specified' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if (session('clearProjectDraft'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        localStorage.removeItem('owner_project_draft_v1');
    });
</script>
@endif
@endpush
