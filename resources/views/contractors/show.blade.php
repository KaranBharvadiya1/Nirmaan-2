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
