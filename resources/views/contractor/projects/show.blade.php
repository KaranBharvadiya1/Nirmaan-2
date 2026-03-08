@extends('contractor.layouts.app', ['pageTitle' => 'Project Bid Details - Contractor', 'activePage' => 'projects'])

@push('styles')
<style>
    .hero-panel {
        border: 0;
        border-radius: 1rem;
        background: linear-gradient(145deg, #ffffff 0%, #edf3ff 60%, #d9e7ff 100%);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        padding: 1rem;
    }

    .panel-card {
        border: 0;
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.07);
    }

    .detail-label {
        color: #697393;
        font-size: 0.8rem;
        margin-bottom: 0.15rem;
    }

    .detail-value {
        font-weight: 600;
        margin-bottom: 0;
    }

    @media (min-width: 992px) {
        .hero-panel {
            padding: 1.2rem 1.3rem;
        }
    }
</style>
@endpush

@section('content')
<div class="hero-panel mb-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
        <div>
            <p class="text-uppercase text-primary fw-semibold mb-1 small">Submit Bid</p>
            <h1 class="fw-bold mb-2 h4 h-md-3">{{ $project->title }}</h1>
            <div class="d-flex flex-wrap gap-2">
                <span class="badge text-bg-light border">{{ $project->reference_code }}</span>
                <span class="badge text-bg-light border">{{ $project->project_type }}</span>
                <span class="badge text-bg-light border">Deadline: {{ $project->deadline?->format('d M Y') }}</span>
                @if($existingBid)
                <span class="badge text-bg-info">Your Current Bid: {{ ucfirst($existingBid->status) }}</span>
                @endif
            </div>
        </div>
        <div>
            <a href="{{ route('contractor.projects') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Back to Projects
            </a>
        </div>
    </div>
</div>

<div class="row g-3 g-md-4">
    <div class="col-12 col-xl-7">
        <div class="panel-card p-3 p-md-4 mb-3">
            <h2 class="h5 fw-bold mb-3">Project Overview</h2>
            <p class="text-secondary mb-3">{{ $project->description }}</p>
            <div class="row g-3">
                <div class="col-md-6">
                    <p class="detail-label">Budget Range</p>
                    <p class="detail-value">
                        &#8377;{{ number_format((float) $project->budget_min) }}
                        @if($project->budget_max)
                        - &#8377;{{ number_format((float) $project->budget_max) }}
                        @endif
                    </p>
                </div>
                <div class="col-md-6">
                    <p class="detail-label">Preferred Language</p>
                    <p class="detail-value">{{ $project->preferred_language }}</p>
                </div>
                <div class="col-md-6">
                    <p class="detail-label">Location</p>
                    <p class="detail-value">{{ $project->area_locality }}, {{ $project->city }}, {{ $project->state }}</p>
                </div>
                <div class="col-md-6">
                    <p class="detail-label">Owner Contact</p>
                    <p class="detail-value">{{ $project->owner->email ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-5">
        <div class="panel-card p-3 p-md-4">
            <h2 class="h5 fw-bold mb-3">{{ $existingBid ? 'Update Your Bid' : 'Submit Your Bid' }}</h2>

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('contractor.projects.submit_bid', $project) }}" class="d-flex flex-column gap-3">
                @csrf
                <div>
                    <label for="quote_amount" class="form-label fw-semibold">Quote Amount (INR)</label>
                    <input id="quote_amount" type="number" min="1000" step="0.01" name="quote_amount" value="{{ old('quote_amount', $existingBid?->quote_amount) }}" class="form-control" required>
                </div>
                <div>
                    <label for="proposed_timeline_days" class="form-label fw-semibold">Proposed Timeline (Days)</label>
                    <input id="proposed_timeline_days" type="number" min="1" max="3650" name="proposed_timeline_days" value="{{ old('proposed_timeline_days', $existingBid?->proposed_timeline_days) }}" class="form-control">
                </div>
                <div>
                    <label for="cover_message" class="form-label fw-semibold">Proposal Message</label>
                    <textarea id="cover_message" name="cover_message" rows="5" class="form-control" placeholder="Explain your scope understanding, workforce, and execution approach.">{{ old('cover_message', $existingBid?->cover_message) }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send me-1"></i>{{ $existingBid ? 'Update Bid' : 'Submit Bid' }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
