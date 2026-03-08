@extends('contractor.layouts.app', ['pageTitle' => 'Open Projects - Contractor', 'activePage' => 'projects'])

@push('styles')
<style>
    .heading-panel {
        border: 0;
        border-radius: 1rem;
        background: linear-gradient(135deg, #ffffff 0%, #f2f6ff 100%);
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
        padding: 1rem;
    }

    .project-card {
        border: 0;
        border-radius: 1rem;
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.08);
        height: 100%;
    }

    .meta-label {
        color: #697393;
        font-size: 0.8rem;
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
<div class="heading-panel mb-4">
    <p class="text-uppercase text-primary fw-semibold mb-1 small">Contractor Bidding</p>
    <h1 class="fw-bold mb-2 h3 h-md-2">Discover Open Projects</h1>
    <p class="text-secondary mb-0">Browse active owner projects and submit your quote with timeline and proposal notes.</p>
</div>

@if ($projects->isEmpty())
<div class="card project-card p-4 p-md-5 text-center">
    <div class="mb-3">
        <i class="bi bi-search fs-1 text-primary"></i>
    </div>
    <h2 class="h4 fw-bold">No open projects right now</h2>
    <p class="text-secondary mb-0">Please check again later for new opportunities.</p>
</div>
@else
<div class="row g-3 g-md-4">
    @foreach($projects as $project)
    @php
        $myBid = $project->bids->first();
    @endphp
    <div class="col-12 col-md-6 col-xl-4">
        <article class="card project-card p-4">
            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                <span class="badge text-bg-light border">{{ $project->reference_code }}</span>
                @if($myBid)
                <span class="badge text-bg-info">Your Bid: {{ ucfirst($myBid->status) }}</span>
                @endif
            </div>

            <h2 class="h5 fw-bold mb-1">{{ $project->title }}</h2>
            <p class="text-secondary small mb-3">{{ $project->project_type }} &middot; {{ $project->work_category ?: 'General Construction' }}</p>

            <div class="mb-2">
                <p class="meta-label">Budget</p>
                <p class="mb-0 fw-semibold">
                    &#8377;{{ number_format((float) $project->budget_min) }}
                    @if($project->budget_max)
                    - &#8377;{{ number_format((float) $project->budget_max) }}
                    @endif
                </p>
            </div>

            <div class="mb-2">
                <p class="meta-label">Location</p>
                <p class="mb-0 small">{{ $project->area_locality }}, {{ $project->city }}, {{ $project->state }}</p>
            </div>

            <div class="mb-3">
                <p class="meta-label">Owner</p>
                <p class="mb-0 small">{{ trim(($project->owner->first_name ?? '').' '.($project->owner->last_name ?? '')) ?: ($project->owner->name ?? 'Owner') }}</p>
            </div>

            <div class="d-flex justify-content-between align-items-center gap-2">
                @if($myBid)
                <span class="small text-secondary">Quoted: &#8377;{{ number_format((float) $myBid->quote_amount, 2) }}</span>
                @else
                <span class="small text-secondary">No bid submitted</span>
                @endif
                <a href="{{ route('contractor.projects.show', $project) }}" class="btn btn-primary btn-sm">
                    {{ $myBid ? 'Update Bid' : 'Submit Bid' }}
                </a>
            </div>
        </article>
    </div>
    @endforeach
</div>

<div class="mt-4">
    {{ $projects->links() }}
</div>
@endif
@endsection
