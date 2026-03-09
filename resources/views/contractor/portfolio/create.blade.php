@extends('contractor.layouts.app', ['pageTitle' => 'Add Work Sample - Nirmaan', 'activePage' => 'portfolio'])

@push('styles')
<style>
    .heading-panel,
    .panel-card {
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
    <h1 class="fw-bold mb-2 h3 h-md-2">Add Work Sample</h1>
    <p class="text-secondary mb-0">Show owners what you have built with clear visuals and project context.</p>
</div>

@include('contractor.portfolio._form', [
    'formAction' => route('contractor.portfolio.save'),
    'formMethod' => 'POST',
    'submitLabel' => 'Save Work Sample',
])
@endsection
