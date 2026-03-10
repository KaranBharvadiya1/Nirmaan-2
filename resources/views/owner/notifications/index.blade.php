@extends('owner.layouts.app', ['pageTitle' => 'Owner Notifications - Nirmaan', 'activePage' => 'notifications'])

@push('styles')
<style>
    .hero-panel {
        border: 0;
        border-radius: 1rem;
        background: linear-gradient(135deg, #ffffff 0%, #f1f5ff 60%, #e0e7ff 100%);
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
        padding: 1.25rem;
    }

    .timeline-item {
        border: 0;
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.05);
        padding: 1rem;
    }

    .timeline-meta {
        font-size: 0.85rem;
        color: var(--text-muted);
    }

    .timeline-divider {
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(15, 23, 42, 0.2), transparent);
    }

    .stats-chip {
        font-size: 0.8rem;
        padding: 0.35rem 0.75rem;
        border-radius: 999px;
        border: 1px solid rgba(37, 99, 235, 0.4);
        color: #1d4ed8;
    }
</style>
@endpush

@section('content')
<div class="hero-panel mb-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
        <div>
            <p class="text-uppercase text-primary fw-semibold mb-1 small">Notifications</p>
            <h1 class="fw-bold mb-2 h3 h-md-2">Owner activity center</h1>
            <p class="text-secondary mb-0">Track bids, hires, and shortlisted contractors in one responsive feed.</p>
        </div>
        <form method="GET" action="{{ route('owner.notifications') }}" class="d-flex gap-2 flex-grow-1 flex-lg-auto">
            <input
                type="search"
                name="query"
                placeholder="Search by contractor, project, or note"
                value="{{ $search }}"
                class="form-control"
            >
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-xl-8">
        <section class="timeline-item">
            <div class="d-flex justify-content-between flex-wrap gap-2 mb-3">
                <div>
                    <p class="text-uppercase text-primary small mb-0">Bid alerts</p>
                    <h2 class="h5 fw-bold mb-0">New and updated quotes</h2>
                </div>
                <span class="stats-chip">Total: {{ $recentBids->count() }}</span>
            </div>
            <div class="timeline-divider mb-3"></div>
            @if ($recentBids->isEmpty())
            <p class="text-secondary mb-0">No new bid activity matches that filter.</p>
            @else
            <div class="d-flex flex-column gap-3">
                @foreach ($recentBids as $bid)
                <article class="border rounded-3 p-3">
                    <div class="d-flex justify-content-between flex-wrap gap-2">
                        <div>
                            <p class="small text-secondary mb-1">{{ $bid->project->reference_code ?? 'Project' }}</p>
                            <h3 class="h6 fw-semibold mb-1">{{ $bid->project->title ?? 'Untitled project' }}</h3>
                            <p class="mb-0 text-secondary">
                                Contractor: {{ trim(($bid->contractor->first_name ?? '').' '.($bid->contractor->last_name ?? '')) ?: 'Contractor '.$bid->contractor_id }}
                            </p>
                        </div>
                        <span class="badge text-bg-info align-self-start">{{ ucfirst($bid->status) }}</span>
                    </div>
                    <div class="timeline-meta mt-2 d-flex flex-wrap gap-3">
                        <span>Quote: &#8377;{{ number_format((float) $bid->quote_amount, 2) }}</span>
                        <span>Timeline: {{ $bid->proposed_timeline_days ? $bid->proposed_timeline_days.' days' : 'TBD' }}</span>
                        <span>{{ $bid->created_at?->diffForHumans() }}</span>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <a href="{{ route('owner.bids') }}#bid-{{ $bid->id }}" class="btn btn-outline-primary btn-sm">Open Bid</a>
                        <a href="{{ route('owner.projects.details', $bid->project) }}" class="btn btn-outline-secondary btn-sm">Open Project</a>
                    </div>
                </article>
                @endforeach
            </div>
            @endif
        </section>
    </div>

    <div class="col-12 col-xl-4">
        <section class="timeline-item mb-3">
            <div class="mb-2">
                <p class="text-uppercase text-primary small mb-0">Hire updates</p>
                <h2 class="h6 fw-bold mb-0">Active workforce</h2>
            </div>
            @if ($recentHires->isEmpty())
            <p class="text-secondary mb-0">No recent hires matched this filter.</p>
            @else
            <ul class="list-group list-group-flush">
                @foreach ($recentHires as $hire)
                <li class="list-group-item border-0 px-0 py-2">
                    <p class="small text-secondary mb-1">{{ $hire->updated_at?->diffForHumans() }}</p>
                    <p class="mb-1 fw-semibold">{{ $hire->project->title ?? 'Project' }}</p>
                    <p class="mb-0 text-secondary">Contractor: {{ trim(($hire->contractor->first_name ?? '').' '.($hire->contractor->last_name ?? '')) }}</p>
                    <span class="badge text-bg-light border mt-1">{{ ucfirst($hire->status) }}</span>
                </li>
                @endforeach
            </ul>
            @endif
        </section>

        <section class="timeline-item">
            <div class="mb-2">
                <p class="text-uppercase text-primary small mb-0">Shortlist notes</p>
                <h2 class="h6 fw-bold mb-0">Favorites guide</h2>
            </div>
            @if ($recentShortlists->isEmpty())
            <p class="text-secondary mb-0">No recent shortlist activity.</p>
            @else
            <div class="d-flex flex-column gap-3">
                @foreach ($recentShortlists as $entry)
                <div class="border rounded-3 p-3">
                    <p class="small text-secondary mb-1">{{ $entry->updated_at?->diffForHumans() }}</p>
                    <p class="fw-semibold mb-1">{{ trim(($entry->contractor->first_name ?? '').' '.($entry->contractor->last_name ?? '')) }}</p>
                    <p class="text-secondary mb-0">{{ $entry->note ?: 'No note provided.' }}</p>
                </div>
                @endforeach
            </div>
            @endif
        </section>
    </div>
</div>
@endsection
