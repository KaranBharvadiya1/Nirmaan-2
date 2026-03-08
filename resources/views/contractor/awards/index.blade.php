@extends('contractor.layouts.app', ['pageTitle' => 'Awarded Projects - Contractor', 'activePage' => 'awards'])

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

    .award-card {
        border: 0;
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.08);
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
        'active' => 'Active',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    $statusBadgeClass = function (string $status): string {
        return match ($status) {
            'active' => 'text-bg-primary',
            'completed' => 'text-bg-success',
            'cancelled' => 'text-bg-danger',
            default => 'text-bg-secondary',
        };
    };
@endphp

<div class="heading-panel mb-4">
    <p class="text-uppercase text-primary fw-semibold mb-1 small">Contractor Awards</p>
    <h1 class="fw-bold mb-2 h3 h-md-2">Awarded Projects</h1>
    <p class="text-secondary mb-0">Manage projects where your bid has been accepted and monitor delivery status.</p>
</div>

<section class="mb-4">
    <div class="d-flex flex-wrap gap-2">
        @foreach ($statusOptions as $value => $label)
        <a
            href="{{ route('contractor.awards', ['status' => $value]) }}"
            class="filter-chip {{ $statusFilter === $value ? 'active' : '' }}"
        >
            {{ $label }}
            <span class="ms-1">({{ $hireStats[$value] ?? 0 }})</span>
        </a>
        @endforeach
    </div>
</section>

@if($hires->isEmpty())
<div class="card award-card p-4 p-md-5 text-center">
    <div class="mb-3">
        <i class="bi bi-trophy fs-1 text-primary"></i>
    </div>
    <h2 class="h4 fw-bold">No awarded projects yet</h2>
    <p class="text-secondary mb-0">Once your bid is accepted, your project contracts will appear here.</p>
</div>
@else
<section class="d-flex flex-column gap-3">
    @foreach($hires as $hire)
    <article class="card award-card p-3 p-md-4">
        <div class="row g-3 align-items-center">
            <div class="col-12 col-lg-4">
                <p class="text-uppercase text-primary small fw-semibold mb-1">{{ $hire->project->reference_code ?? 'PROJECT' }}</p>
                <h2 class="h5 fw-bold mb-1">{{ $hire->project->title ?? 'Project unavailable' }}</h2>
                <p class="mb-1 small">Owner: <strong>{{ trim(($hire->owner->first_name ?? '').' '.($hire->owner->last_name ?? '')) ?: 'Owner' }}</strong></p>
                <p class="mb-0 text-secondary small">{{ $hire->owner->email ?? '-' }}</p>
            </div>
            <div class="col-6 col-lg-2">
                <p class="small text-secondary mb-1">Agreed Amount</p>
                <p class="fw-semibold mb-0">&#8377;{{ number_format((float) $hire->agreed_amount, 2) }}</p>
            </div>
            <div class="col-6 col-lg-2">
                <p class="small text-secondary mb-1">Timeline</p>
                <p class="fw-semibold mb-0">{{ $hire->agreed_timeline_days ? $hire->agreed_timeline_days.' days' : 'Not set' }}</p>
            </div>
            <div class="col-6 col-lg-2">
                <p class="small text-secondary mb-1">Hired On</p>
                <p class="fw-semibold mb-0">{{ $hire->hired_at?->format('d M Y') }}</p>
            </div>
            <div class="col-6 col-lg-2 d-flex flex-column gap-2">
                <span class="badge {{ $statusBadgeClass($hire->status) }}">{{ ucfirst($hire->status) }}</span>
                @if($hire->project)
                <a href="{{ route('contractor.projects.show', $hire->project) }}" class="btn btn-outline-primary btn-sm">View</a>
                @endif
            </div>
            @if($hire->bid?->cover_message)
            <div class="col-12">
                <p class="mb-0 small text-secondary">{{ $hire->bid->cover_message }}</p>
            </div>
            @endif
        </div>
    </article>
    @endforeach

    <div class="mt-2">
        {{ $hires->links() }}
    </div>
</section>
@endif
@endsection
