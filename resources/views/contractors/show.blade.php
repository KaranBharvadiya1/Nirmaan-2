@extends(auth()->user()?->role === 'Owner' ? 'owner.layouts.app' : 'contractor.layouts.app', ['pageTitle' => 'Contractor Portfolio - Nirmaan', 'activePage' => auth()->user()?->role === 'Contractor' ? 'portfolio' : null])

@push('styles')
<style>
    .hero-panel,
    .stat-card,
    .sample-card {
        border: 0;
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
    }

    .hero-panel {
        background: linear-gradient(145deg, #ffffff 0%, #edf3ff 60%, #d9e7ff 100%);
        padding: 1rem;
    }

    .profile-avatar,
    .profile-fallback {
        width: 88px;
        height: 88px;
        border-radius: 50%;
    }

    .profile-avatar {
        object-fit: cover;
        border: 4px solid rgba(255, 255, 255, 0.75);
    }

    .profile-fallback {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.8);
        color: #1b3b94;
        font-size: 2rem;
        font-weight: 700;
    }

    .stat-card {
        padding: 1rem;
    }

    .sample-card {
        padding: 1.1rem;
    }

    .sample-media-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 0.85rem;
        margin-top: 1rem;
    }

    .media-shell {
        border-radius: 0.95rem;
        overflow: hidden;
        background: linear-gradient(145deg, #eff4ff 0%, #dbe7ff 100%);
        min-height: 180px;
    }

    .media-shell img,
    .media-shell video,
    .media-shell iframe {
        width: 100%;
        height: 100%;
        min-height: 180px;
        object-fit: cover;
        border: 0;
    }

    .media-link-fallback {
        min-height: 180px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 1rem;
    }

    @media (min-width: 992px) {
        .hero-panel {
            padding: 1.35rem 1.5rem;
        }
    }
</style>
@endpush

@section('content')
@php
    $fullName = trim(($contractor->first_name ?? '').' '.($contractor->last_name ?? ''));
    $initial = strtoupper(substr($contractor->first_name ?? 'C', 0, 1));
    $viewerIsOwner = $viewerIsOwner ?? false;
@endphp

    <div class="hero-panel mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
        <div class="d-flex align-items-start gap-3">
            @if ($contractor->profile_image_url)
            <img src="{{ $contractor->profile_image_url }}" alt="Contractor profile image" class="profile-avatar">
            @else
            <span class="profile-fallback">{{ $initial }}</span>
            @endif
            <div>
                <p class="text-uppercase text-primary fw-semibold mb-1 small">Contractor Portfolio</p>
                <h1 class="fw-bold mb-2 h3 h-md-2">{{ $fullName ?: 'Contractor' }}</h1>
                <p class="text-secondary mb-2">{{ $contractor->email }}</p>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge text-bg-light border">{{ $portfolioStats['samples'] }} work sample(s)</span>
                    <span class="badge text-bg-light border">{{ $portfolioStats['images'] }} image(s)</span>
                    <span class="badge text-bg-light border">{{ $portfolioStats['videos'] }} video(s)</span>
                </div>
            </div>
        </div>
        <div>
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-3">
            @if ($contractor->availability_label)
            <span class="badge text-bg-light border"><i class="bi bi-calendar-check me-1"></i>{{ $contractor->availability_label }}</span>
            @endif
            @if ($contractor->languages)
            <span class="badge text-bg-light border"><i class="bi bi-translate me-1"></i>{{ $contractor->languages }}</span>
            @endif
            @if ($contractor->service_areas)
            <span class="badge text-bg-light border"><i class="bi bi-geo-alt me-1"></i>{{ $contractor->service_areas }}</span>
            @endif
            @if ($contractor->team_size)
            <span class="badge text-bg-light border"><i class="bi bi-people me-1"></i>Team of {{ $contractor->team_size }}</span>
            @endif
            @if ($contractor->hourly_rate_from || $contractor->hourly_rate_to)
            <span class="badge text-bg-light border">
                <i class="bi bi-currency-rupee me-1"></i>
                {{ $contractor->hourly_rate_from ? '₹'.number_format($contractor->hourly_rate_from, 2) : '—' }}
                –
                {{ $contractor->hourly_rate_to ? '₹'.number_format($contractor->hourly_rate_to, 2) : 'Negotiable' }}
            </span>
            @endif
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-4">
        <div class="stat-card h-100">
            <p class="text-secondary small mb-1">Work Samples</p>
            <p class="fw-bold fs-4 mb-0">{{ $portfolioStats['samples'] }}</p>
        </div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="stat-card h-100">
            <p class="text-secondary small mb-1">Images</p>
            <p class="fw-bold fs-4 mb-0">{{ $portfolioStats['images'] }}</p>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="stat-card h-100">
            <p class="text-secondary small mb-1">Videos</p>
            <p class="fw-bold fs-4 mb-0">{{ $portfolioStats['videos'] }}</p>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="sample-card">
            <h2 class="h5 fw-bold">Profile snapshot</h2>
            <p class="text-secondary mb-3">
                {{ $contractor->contractor_bio ?: 'This contractor has not completed their profile yet.' }}
            </p>
            <div class="d-flex flex-wrap gap-3 mb-3">
                @if ($contractor->trades)
                <span class="badge text-bg-light border"><i class="bi bi-hammer me-1"></i>{{ $contractor->trades }}</span>
                @endif
                @if ($contractor->years_experience)
                <span class="badge text-bg-light border"><i class="bi bi-calendar-workweek me-1"></i>{{ $contractor->years_experience }} yrs</span>
                @endif
            </div>
            @if ($contractor->video_intro_url)
            <a href="{{ $contractor->video_intro_url }}" target="_blank" rel="noreferrer" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-play-circle me-1"></i>Watch introduction
            </a>
            @endif
        </div>
    </div>
</div>

@if ($viewerIsOwner ?? false)
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="sample-card">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-3 align-items-start">
                <div>
                    <p class="text-uppercase text-primary small mb-1">Decision Intelligence</p>
                    <h2 class="h5 fw-bold mb-2">Owner-only insights</h2>
                </div>
                <form method="POST" action="{{ route('owner.shortlist.store') }}">
                    @csrf
                    <input type="hidden" name="contractor_id" value="{{ $contractor->id }}">
                    @if ($ownerBids->first()?->project_id)
                    <input type="hidden" name="project_id" value="{{ $ownerBids->first()->project_id }}">
                    @endif
                    @if ($ownerBids->first()?->id)
                    <input type="hidden" name="bid_id" value="{{ $ownerBids->first()->id }}">
                    @endif
                    <button type="submit" class="btn btn-warning btn-sm text-white">
                        <i class="bi bi-star me-1"></i>Add to shortlist
                    </button>
                </form>
            </div>
            <div class="d-flex flex-wrap gap-2 mb-3">
                @foreach (['pending', 'shortlisted', 'accepted', 'rejected', 'withdrawn'] as $statusKey)
                <span class="badge text-bg-light border">
                    {{ ucfirst($statusKey) }}: {{ $ownerBidStats[$statusKey] ?? 0 }}
                </span>
                @endforeach
                <span class="badge bg-primary">Total: {{ $ownerBidStats['all'] ?? 0 }}</span>
            </div>
            <div class="list-group list-group-flush">
                @forelse ($ownerBids as $bid)
                <div class="list-group-item px-0 border-0">
                    <div class="d-flex flex-column flex-md-row justify-content-between">
                        <div>
                            <p class="small text-secondary mb-1">{{ $bid->project->reference_code ?? 'Project' }}</p>
                            <h3 class="h6 fw-bold mb-1">{{ $bid->project->title ?? 'Untitled project' }}</h3>
                            <p class="text-secondary mb-1">Quote: &#8377;{{ number_format((float) $bid->quote_amount, 2) }} · Timeline: {{ $bid->proposed_timeline_days ? $bid->proposed_timeline_days.' days' : 'TBD' }}</p>
                        </div>
                        <span class="badge text-bg-info align-self-start">{{ ucfirst($bid->status) }}</span>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <a href="{{ route('owner.projects.details', $bid->project) }}" class="btn btn-outline-secondary btn-sm">Open project</a>
                        <a href="{{ route('owner.bids') }}#bid-{{ $bid->id }}" class="btn btn-outline-primary btn-sm">Open bid</a>
                    </div>
                </div>
                @empty
                <div class="list-group-item px-0 border-0">
                    <p class="text-secondary mb-0">No bids recorded yet between you and this contractor.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endif

@if ($workSamples->isEmpty())
<div class="sample-card text-center py-5">
    <div class="mb-3">
        <i class="bi bi-images fs-1 text-primary"></i>
    </div>
    <h2 class="h4 fw-bold">No work uploaded yet</h2>
    <p class="text-secondary mb-0">This contractor has not added any work samples yet.</p>
</div>
@else
<div class="d-flex flex-column gap-3">
    @foreach ($workSamples as $workSample)
    <article class="sample-card">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
            <div>
                <div class="d-flex flex-wrap gap-2 mb-2">
                    <span class="badge text-bg-light border">{{ $workSample->work_category ?: 'General Work' }}</span>
                    @if ($workSample->completed_year)
                    <span class="badge text-bg-light border">{{ $workSample->completed_year }}</span>
                    @endif
                    @if ($workSample->city || $workSample->state)
                    <span class="badge text-bg-light border">{{ collect([$workSample->city, $workSample->state])->filter()->implode(', ') }}</span>
                    @endif
                </div>
                <h2 class="h4 fw-bold mb-2">{{ $workSample->title }}</h2>
                <p class="text-secondary mb-0">{{ $workSample->description }}</p>
            </div>
            <div class="text-lg-end">
                <p class="small text-secondary mb-1">Media Count</p>
                <p class="fw-semibold mb-0">{{ $workSample->media->count() }} item(s)</p>
            </div>
        </div>

        @if ($workSample->media->isNotEmpty())
        <div class="sample-media-grid">
            @foreach ($workSample->media as $media)
            <div class="media-shell">
                @if ($media->media_type === 'image' && $media->file_url)
                <img src="{{ $media->file_url }}" alt="{{ $media->original_name ?: $workSample->title }}">
                @elseif ($media->media_type === 'video' && $media->file_url)
                <video controls preload="metadata">
                    <source src="{{ $media->file_url }}" type="{{ $media->mime_type ?: 'video/mp4' }}">
                </video>
                @elseif ($media->embed_url)
                <iframe src="{{ $media->embed_url }}" allowfullscreen loading="lazy"></iframe>
                @else
                <div class="media-link-fallback">
                    <i class="bi bi-play-btn fs-1 text-primary mb-2"></i>
                    <a href="{{ $media->external_url }}" target="_blank" rel="noreferrer" class="btn btn-outline-primary btn-sm">Open Video Link</a>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </article>
    @endforeach
</div>
@endif
@endsection
