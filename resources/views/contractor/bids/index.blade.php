@extends('contractor.layouts.app', ['pageTitle' => 'My Bids - Contractor', 'activePage' => 'bids'])

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

    .bid-card {
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
        'pending' => 'Pending',
        'shortlisted' => 'Shortlisted',
        'accepted' => 'Accepted',
        'rejected' => 'Rejected',
        'withdrawn' => 'Withdrawn',
    ];

    $statusBadgeClass = function (string $status): string {
        return match ($status) {
            'pending' => 'text-bg-secondary',
            'shortlisted' => 'text-bg-info',
            'accepted' => 'text-bg-success',
            'rejected' => 'text-bg-danger',
            'withdrawn' => 'text-bg-dark',
            default => 'text-bg-secondary',
        };
    };
@endphp

<div class="heading-panel mb-4">
    <p class="text-uppercase text-primary fw-semibold mb-1 small">Contractor Bids</p>
    <h1 class="fw-bold mb-2 h3 h-md-2">My Submitted Bids</h1>
    <p class="text-secondary mb-0">Track your bid outcomes, withdraw pending proposals, and monitor awarded opportunities.</p>
</div>

<section class="mb-4">
    <div class="d-flex flex-wrap gap-2">
        @foreach ($statusOptions as $value => $label)
        <a
            href="{{ route('contractor.bids', ['status' => $value]) }}"
            class="filter-chip {{ $statusFilter === $value ? 'active' : '' }}"
        >
            {{ $label }}
            <span class="ms-1">({{ $bidStats[$value] ?? 0 }})</span>
        </a>
        @endforeach
    </div>
</section>

@if($bids->isEmpty())
<div class="card bid-card p-4 p-md-5 text-center">
    <div class="mb-3">
        <i class="bi bi-receipt fs-1 text-primary"></i>
    </div>
    <h2 class="h4 fw-bold">No bids found</h2>
    <p class="text-secondary mb-0">Submit bids on open projects to start building your pipeline.</p>
</div>
@else
<section class="d-flex flex-column gap-3">
    @foreach($bids as $bid)
    @php
        $canWithdraw = in_array($bid->status, ['pending', 'shortlisted'], true)
            && $bid->project
            && $bid->project->status === 'open'
            && $bid->project->hire?->status !== 'active';
    @endphp
    <article class="card bid-card p-3 p-md-4">
        <div class="row g-3 align-items-center">
            <div class="col-12 col-lg-4">
                <p class="text-uppercase text-primary small fw-semibold mb-1">{{ $bid->project->reference_code ?? 'PROJECT' }}</p>
                <h2 class="h5 fw-bold mb-1">{{ $bid->project->title ?? 'Project unavailable' }}</h2>
                <p class="mb-1 small">Owner: <strong>{{ trim(($bid->project->owner->first_name ?? '').' '.($bid->project->owner->last_name ?? '')) ?: 'Owner' }}</strong></p>
                <p class="mb-0 text-secondary small">{{ $bid->project->owner->email ?? '-' }}</p>
            </div>
            <div class="col-6 col-lg-2">
                <p class="small text-secondary mb-1">Quote</p>
                <p class="fw-semibold mb-0">&#8377;{{ number_format((float) $bid->quote_amount, 2) }}</p>
            </div>
            <div class="col-6 col-lg-2">
                <p class="small text-secondary mb-1">Timeline</p>
                <p class="fw-semibold mb-0">{{ $bid->proposed_timeline_days ? $bid->proposed_timeline_days.' days' : 'Not set' }}</p>
            </div>
            <div class="col-6 col-lg-2">
                <p class="small text-secondary mb-1">Status</p>
                <span class="badge {{ $statusBadgeClass($bid->status) }}">{{ ucfirst($bid->status) }}</span>
            </div>
                <div class="col-6 col-lg-2 d-flex flex-column gap-2">
                @if($bid->project)
                <a href="{{ route('contractor.projects.show', $bid->project) }}" class="btn btn-outline-primary btn-sm">View</a>
                @endif
                @if($canWithdraw)
                <form method="POST" action="{{ route('contractor.bids.withdraw', $bid) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">Withdraw</button>
                </form>
                @endif
            </div>
            @if($bid->cover_message)
            <div class="col-12">
                <p class="mb-0 small text-secondary">{{ $bid->cover_message }}</p>
            </div>
            @endif
        </div>
    </article>
    @endforeach

    <div class="mt-2">
        {{ $bids->links() }}
    </div>
</section>
@endif
@endsection
