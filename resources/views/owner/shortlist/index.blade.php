@extends('owner.layouts.app', ['pageTitle' => 'Owner Shortlist - Nirmaan', 'activePage' => 'shortlist'])

@push('styles')
<style>
    .hero-panel {
        border: 0;
        border-radius: 1rem;
        background: linear-gradient(135deg, #f7f9ff 0%, #e3edff 60%, #d6e5ff 100%);
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
        padding: 1.25rem;
    }

    .shortlist-card {
        border: 0;
        border-radius: 1.1rem;
        background: #fff;
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.08);
    }

    .profile-circle {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid rgba(37, 99, 235, 0.15);
    }

    .profile-fallback {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: rgba(37, 99, 235, 0.1);
        color: #1d4ed8;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.5rem;
    }

    .shortlist-meta span,
    .shortlist-meta i {
        font-size: 0.85rem;
    }

    .small-input {
        font-size: 0.85rem;
    }

    @media (max-width: 767px) {
        .shortlist-card {
            padding: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="hero-panel mb-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
        <div>
            <p class="text-uppercase text-primary fw-semibold mb-1 small">Owner Shortlist</p>
            <h1 class="fw-bold mb-2 h3 h-md-2">Favorites for hiring</h1>
            <p class="text-secondary mb-0">Compare shortlisted contractors, capture direct notes, and move from shortlist to hire with clarity.</p>
        </div>
        <form method="GET" action="{{ route('owner.shortlist.index') }}" class="d-flex gap-2 w-100 w-lg-auto">
            <input
                type="search"
                name="query"
                placeholder="Search by contractor, project, or code"
                value="{{ $search }}"
                class="form-control"
            >
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>
</div>

@if ($shortlists->isEmpty())
<div class="card shortlist-card p-4 text-center">
    <div class="mb-3">
        <i class="bi bi-star fs-1 text-primary"></i>
    </div>
    <h2 class="h4 fw-bold">Shortlist empty</h2>
    <p class="text-secondary mb-3">Mark contractors as favorites from the bid inbox to see them here.</p>
    <a href="{{ route('owner.bids') }}" class="btn btn-outline-primary">Return to bids</a>
</div>
@else
<div class="row g-3">
    @foreach ($shortlists as $shortlist)
    @php
        $contractor = $shortlist->contractor;
        $fullName = trim(($contractor->first_name ?? '').' '.($contractor->last_name ?? ''));
        $initial = strtoupper(substr($contractor->first_name ?? 'C', 0, 1));
    @endphp
    <div class="col-12">
        <article class="shortlist-card p-4">
            <div class="row g-3 align-items-center">
                <div class="col-12 col-md-8">
                    <div class="d-flex align-items-center gap-3">
                        @if ($contractor->profile_image_url)
                        <img src="{{ $contractor->profile_image_url }}" alt="Contractor image" class="profile-circle">
                        @else
                        <span class="profile-fallback">{{ $initial }}</span>
                        @endif
                        <div>
                            <p class="small text-uppercase text-primary mb-1">Contractor</p>
                            <h2 class="h4 fw-bold mb-1">{{ $fullName ?: $contractor->email }}</h2>
                            <p class="text-secondary mb-0 small">
                                {!! $contractor->contractor_bio ? \Illuminate\Support\Str::limit(e($contractor->contractor_bio), 160) : '<span class="text-muted">No bio provided yet.</span>' !!}
                            </p>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 shortlist-meta mt-3">
                        @if ($contractor->years_experience)
                        <span class="badge text-bg-light border"><i class="bi bi-briefcase me-1"></i>{{ $contractor->years_experience }} yrs exp</span>
                        @endif
                        @if ($contractor->trades)
                        <span class="badge text-bg-light border"><i class="bi bi-tools me-1"></i>{{ $contractor->trades }}</span>
                        @endif
                        @if ($contractor->service_areas)
                        <span class="badge text-bg-light border"><i class="bi bi-geo-alt me-1"></i>{{ $contractor->service_areas }}</span>
                        @endif
                        @if ($contractor->availability_label)
                        <span class="badge text-bg-light border"><i class="bi bi-calendar-check me-1"></i>{{ $contractor->availability_label }}</span>
                        @endif
                    </div>
                </div>
                <div class="col-12 col-md-4 text-md-end">
                    <span class="badge text-bg-info">{{ ucfirst($shortlist->status) }}</span>
                    <span class="badge bg-primary">{{ $shortlist->priority }}/5 priority</span>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 mt-4">
                <a href="{{ route('contractors.portfolio.show', $contractor) }}" class="btn btn-outline-primary btn-sm">View Profile</a>
                @if ($shortlist->project)
                <a href="{{ route('owner.projects.details', $shortlist->project) }}" class="btn btn-outline-secondary btn-sm">Open Project</a>
                @endif
                @if ($shortlist->bid)
                <a href="{{ route('owner.bids') }}#bid-{{ $shortlist->bid_id }}" class="btn btn-outline-info btn-sm">Open Bid</a>
                @endif
                <a href="{{ route('owner.messages') }}" class="btn btn-outline-success btn-sm">Message</a>
            </div>

                <div class="row g-3 mt-3">
                    <div class="col-12 col-lg-8">
                        <form method="POST" action="{{ route('owner.shortlist.update', $shortlist) }}">
                            @csrf
                            @method('PATCH')
                            <label class="form-label small fw-semibold">Notes for this contractor</label>
                            <textarea
                                name="note"
                                class="form-control small-input{{ $errors->has('note') ? ' is-invalid' : '' }}"
                                rows="2"
                            >{{ old('note', $shortlist->note) }}</textarea>
                            <div class="form-text text-muted small">Capture budget cues, references, or follow-up actions.</div>
                            <div class="mt-2 d-flex flex-wrap gap-2">
                                <div>
                                    <label class="form-label small fw-semibold mb-1">Priority</label>
                                    <select name="priority" class="form-select form-select-sm small-input">
                                        @foreach (range(1, 5) as $value)
                                        <option value="{{ $value }}" @selected((int) ($shortlist->priority ?? 3) === $value)>{{ $value }} / 5</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm align-self-end">Save note</button>
                            </div>
                        </form>
                        <form method="POST" action="{{ route('owner.shortlist.destroy', $shortlist) }}" class="mt-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">Remove from shortlist</button>
                        </form>
                    </div>
                </div>
            </article>
        </div>
    @endforeach
</div>

<div class="mt-4">
    {{ $shortlists->links() }}
</div>
@endif
@endsection
