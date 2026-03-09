@extends('contractor.layouts.app', ['pageTitle' => 'Contractor Portfolio - Nirmaan', 'activePage' => 'portfolio'])

@push('styles')
<style>
    .heading-panel,
    .stat-card,
    .portfolio-card,
    .empty-panel {
        border: 0;
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
    }

    .heading-panel {
        background: linear-gradient(135deg, #ffffff 0%, #f2f6ff 100%);
        padding: 1rem;
    }

    .stat-card {
        padding: 1rem;
    }

    .stat-label {
        color: #697393;
        font-size: 0.82rem;
        margin-bottom: 0.25rem;
    }

    .stat-value {
        font-size: 1.55rem;
        font-weight: 700;
        margin-bottom: 0;
    }

    .portfolio-card {
        overflow: hidden;
        height: 100%;
    }

    .portfolio-cover {
        aspect-ratio: 16 / 10;
        background: linear-gradient(145deg, #eff4ff 0%, #dbe7ff 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .portfolio-cover img,
    .portfolio-cover video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .empty-panel {
        padding: 3rem 1.5rem;
        text-align: center;
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
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
        <div>
            <p class="text-uppercase text-primary fw-semibold mb-1 small">Contractor Portfolio</p>
            <h1 class="fw-bold mb-2 h3 h-md-2">Work Gallery</h1>
            <p class="text-secondary mb-0">Upload project photos and videos so owners can evaluate your workmanship before hiring.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('contractor.portfolio.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>Add Work Sample
            </a>
            <a href="{{ route('contractors.portfolio.show', auth()->user()) }}" class="btn btn-outline-secondary">
                <i class="bi bi-eye me-1"></i>Public View
            </a>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card h-100">
            <p class="stat-label">Work Samples</p>
            <p class="stat-value">{{ $portfolioStats['samples'] }}</p>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card h-100">
            <p class="stat-label">Images</p>
            <p class="stat-value">{{ $portfolioStats['images'] }}</p>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card h-100">
            <p class="stat-label">Uploaded Videos</p>
            <p class="stat-value">{{ $portfolioStats['videos'] }}</p>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card h-100">
            <p class="stat-label">External Videos</p>
            <p class="stat-value">{{ $portfolioStats['external_videos'] }}</p>
        </div>
    </div>
</div>

@if ($workSamples->isEmpty())
<div class="empty-panel">
    <div class="mb-3">
        <i class="bi bi-images fs-1 text-primary"></i>
    </div>
    <h2 class="h4 fw-bold">No work samples yet</h2>
    <p class="text-secondary mb-4">Start by adding photos or videos from completed work so owners can review your portfolio.</p>
    <a href="{{ route('contractor.portfolio.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Add First Work Sample
    </a>
</div>
@else
<div class="row g-3 g-md-4">
    @foreach ($workSamples as $workSample)
    @php
        $coverMedia = $workSample->media->first();
        $imageCount = $workSample->media->where('media_type', 'image')->count();
        $videoCount = $workSample->media->whereIn('media_type', ['video', 'external_video'])->count();
    @endphp
    <div class="col-12 col-md-6 col-xl-4">
        <article class="portfolio-card">
            <div class="portfolio-cover">
                @if ($coverMedia?->media_type === 'image' && $coverMedia->file_url)
                <img src="{{ $coverMedia->file_url }}" alt="{{ $workSample->title }}">
                @elseif ($coverMedia?->media_type === 'video' && $coverMedia->file_url)
                <video controls preload="metadata">
                    <source src="{{ $coverMedia->file_url }}" type="{{ $coverMedia->mime_type ?: 'video/mp4' }}">
                </video>
                @else
                <div class="text-center p-4">
                    <i class="bi bi-play-btn fs-1 text-primary"></i>
                    <p class="mb-0 small text-secondary">{{ $coverMedia ? 'External video preview' : 'No preview uploaded' }}</p>
                </div>
                @endif
            </div>
            <div class="p-4">
                <div class="d-flex justify-content-between gap-2 mb-2">
                    <span class="badge text-bg-light border">{{ $workSample->work_category ?: 'General Work' }}</span>
                    @if ($workSample->completed_year)
                    <span class="badge text-bg-light border">{{ $workSample->completed_year }}</span>
                    @endif
                </div>
                <h2 class="h5 fw-bold mb-2">{{ $workSample->title }}</h2>
                <p class="text-secondary small mb-3">{{ \Illuminate\Support\Str::limit($workSample->description, 130) }}</p>
                <div class="d-flex flex-wrap gap-2 small text-secondary mb-3">
                    @if ($workSample->city || $workSample->state)
                    <span><i class="bi bi-geo-alt me-1"></i>{{ collect([$workSample->city, $workSample->state])->filter()->implode(', ') }}</span>
                    @endif
                    <span><i class="bi bi-image me-1"></i>{{ $imageCount }} image(s)</span>
                    <span><i class="bi bi-camera-reels me-1"></i>{{ $videoCount }} video(s)</span>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('contractor.portfolio.edit', $workSample) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                    <a href="{{ route('contractors.portfolio.show', auth()->user()) }}" class="btn btn-outline-secondary btn-sm">Public View</a>
                    <form method="POST" action="{{ route('contractor.portfolio.delete', $workSample) }}" class="js-confirm-submit" data-confirm-message="Delete this work sample and all its media?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                    </form>
                </div>
            </div>
        </article>
    </div>
    @endforeach
</div>
@endif
@endsection
