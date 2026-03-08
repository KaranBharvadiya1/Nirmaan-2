@extends('owner.layouts.app', ['pageTitle' => 'Owner Projects - Nirmaan', 'activePage' => 'projects'])

@push('styles')
<style>
    .heading-panel {
        border: 0;
        border-radius: 1rem;
        background: linear-gradient(135deg, #ffffff 0%, #f2f6ff 100%);
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
        padding: 1rem;
    }

    .filter-chip {
        text-decoration: none;
        padding: 0.5rem 0.9rem;
        border-radius: 999px;
        font-size: 0.86rem;
        font-weight: 600;
        border: 1px solid #d9e2f7;
        color: #2f3b57;
        background: #fff;
    }

    .filter-chip.active {
        background: #2452e6;
        color: #fff;
        border-color: #2452e6;
    }

    .project-card {
        border: 0;
        border-radius: 1rem;
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.08);
        height: 100%;
    }

    .meta-label {
        color: #6b7390;
        font-size: 0.82rem;
        margin-bottom: 0.2rem;
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
    $statusOptions = [
        'all' => 'All',
        'open' => 'Open',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    $statusUpdateOptions = [
        'open' => 'Open (Reopen)',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    $statusBadgeClass = function (string $status): string {
        return match ($status) {
            'open' => 'text-bg-primary',
            'in_progress' => 'text-bg-warning',
            'completed' => 'text-bg-success',
            'cancelled' => 'text-bg-danger',
            default => 'text-bg-secondary',
        };
    };
@endphp

<div class="heading-panel mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3">
        <div>
            <p class="text-uppercase text-primary fw-semibold mb-1 small">Owner Projects</p>
            <h1 class="fw-bold mb-2 h3 h-md-2">Manage Your Construction Projects</h1>
            <p class="text-secondary mb-0">Built for local site realities and professional-level project control.</p>
        </div>
        <a href="{{ route('owner.projects.create') }}" class="btn btn-primary px-3">
            <i class="bi bi-plus-square me-1"></i>Post New Project
        </a>
    </div>
</div>

<section class="mb-4">
    <div class="d-flex flex-wrap gap-2">
        @foreach ($statusOptions as $value => $label)
        <a
            href="{{ route('owner.projects', ['status' => $value]) }}"
            class="filter-chip {{ $statusFilter === $value ? 'active' : '' }}"
        >
            {{ $label }}
            <span class="ms-1">({{ $projectStats[$value] ?? 0 }})</span>
        </a>
        @endforeach
    </div>
</section>

@if ($projects->isEmpty())
<div class="card project-card p-4 p-md-5 text-center">
    <div class="mb-3">
        <i class="bi bi-building-add fs-1 text-primary"></i>
    </div>
    <h2 class="h4 fw-bold">No projects yet</h2>
    <p class="text-secondary mb-4">Create your first project with location, budget, and timeline details to attract local contractors.</p>
    <div>
        <a href="{{ route('owner.projects.create') }}" class="btn btn-primary px-4">Create First Project</a>
    </div>
</div>
@else
<section>
    <div class="row g-3 g-md-4">
        @foreach ($projects as $project)
        @php
            $canModify = in_array($project->status, ['open', 'cancelled'], true);
            $canDelete = $canModify && ((int) ($project->bids_count ?? 0) === 0);
        @endphp
        <div class="col-12 col-md-6 col-xl-4">
            <article class="card project-card p-4" data-project-card="{{ $project->id }}">
                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                    <span class="badge text-bg-light border">{{ $project->reference_code }}</span>
                    <span class="badge {{ $statusBadgeClass($project->status) }}" data-project-status-badge="{{ $project->id }}">
                        {{ str_replace('_', ' ', ucfirst($project->status)) }}
                    </span>
                </div>

                <h3 class="h5 fw-bold mb-1">{{ $project->title }}</h3>
                <p class="text-secondary small mb-3">{{ $project->project_type }} &middot; {{ $project->work_category ?: 'General Construction' }}</p>

                <div class="mb-3">
                    <label class="form-label fw-semibold small mb-1" for="project-status-{{ $project->id }}">Project Status</label>
                    <select
                        id="project-status-{{ $project->id }}"
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

                <div class="mb-3">
                    <p class="meta-label">Location</p>
                    <p class="mb-0 small">
                        {{ $project->area_locality }}, {{ $project->city }}, {{ $project->state }} - {{ $project->postal_code }}
                    </p>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <p class="meta-label">Budget</p>
                        <p class="mb-0 fw-semibold small">
                            &#8377;{{ number_format((float) $project->budget_min) }}
                            @if($project->budget_max)
                            - &#8377;{{ number_format((float) $project->budget_max) }}
                            @endif
                        </p>
                    </div>
                    <div class="col-6">
                        <p class="meta-label">Deadline</p>
                        <p class="mb-0 fw-semibold small">{{ $project->deadline?->format('d M Y') }}</p>
                    </div>
                    <div class="col-6">
                        <p class="meta-label">Bids</p>
                        <p class="mb-0 fw-semibold small">{{ (int) ($project->bids_count ?? 0) }}</p>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center gap-2">
                    <span class="small text-secondary">{{ $project->visibility === 'invite_only' ? 'Invite Only' : 'Public' }}</span>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('owner.projects.details', $project) }}" class="btn btn-outline-primary btn-sm">View</a>
                        <a href="{{ route('owner.projects.edit', $project) }}" class="btn btn-outline-secondary btn-sm {{ $canModify ? '' : 'disabled' }}" data-project-edit-action="{{ $project->id }}" @if (! $canModify) aria-disabled="true" tabindex="-1" @endif>Edit</a>
                        <form method="POST" action="{{ route('owner.projects.delete', $project) }}" class="js-confirm-submit" data-confirm-message="Delete this project permanently?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm" data-project-delete-action="{{ $project->id }}" data-has-bids="{{ ((int) ($project->bids_count ?? 0) > 0) ? '1' : '0' }}" @disabled(! $canDelete) title="{{ $canDelete ? '' : 'Cannot delete project with bids.' }}">Delete</button>
                        </form>
                    </div>
                </div>
            </article>
        </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $projects->links() }}
    </div>
</section>
@endif
@endsection
