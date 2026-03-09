@extends('contractor.layouts.app', ['pageTitle' => 'Edit Work Sample - Nirmaan', 'activePage' => 'portfolio'])

@push('styles')
<style>
    .heading-panel,
    .panel-card,
    .media-tile {
        border: 0;
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
    }

    .heading-panel {
        background: linear-gradient(135deg, #ffffff 0%, #f2f6ff 100%);
        padding: 1rem;
    }

    .helper-text {
        color: #697393;
        font-size: 0.9rem;
    }

    .media-tile {
        padding: 0.9rem;
    }

    .media-preview {
        aspect-ratio: 16 / 10;
        border-radius: 0.85rem;
        overflow: hidden;
        background: linear-gradient(145deg, #eff4ff 0%, #dbe7ff 100%);
        margin-bottom: 0.9rem;
    }

    .media-preview-image,
    .media-preview-video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .external-video-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1rem;
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
    <p class="text-uppercase text-primary fw-semibold mb-1 small">Contractor Portfolio</p>
    <h1 class="fw-bold mb-2 h3 h-md-2">Edit Work Sample</h1>
    <p class="text-secondary mb-0">Update the visuals, context, or media links for this work sample.</p>
</div>

@include('contractor.portfolio._form', [
    'workSample' => $workSample,
    'formAction' => route('contractor.portfolio.update', $workSample),
    'formMethod' => 'PUT',
    'submitLabel' => 'Update Work Sample',
])
@endsection
