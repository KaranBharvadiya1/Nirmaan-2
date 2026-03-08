@extends('owner.layouts.app', ['pageTitle' => 'Owner Dashboard - Nirmaan', 'activePage' => 'dashboard'])

@push('styles')
<style>
    .welcome-panel {
        border: 0;
        border-radius: 1rem;
        background: linear-gradient(135deg, #ffffff 0%, #f2f6ff 100%);
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
        padding: 1rem;
    }

    .kpi-card {
        border: 0;
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.07);
        height: 100%;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
    }

    .kpi-label {
        color: var(--text-muted);
        font-weight: 600;
        margin-bottom: 0.45rem;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
    }

    .kpi-value {
        font-size: clamp(1.4rem, 2.4vw, 2.05rem);
        font-weight: 800;
        line-height: 1;
    }

    .quick-card {
        border: 0;
        border-radius: 1rem;
        background: linear-gradient(145deg, #ffffff 0%, var(--bg-soft) 100%);
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.07);
        height: 100%;
    }

    .quick-icon {
        width: 50px;
        height: 50px;
        border-radius: 0.85rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(36, 82, 230, 0.11);
        color: var(--accent);
        font-size: 1.15rem;
    }

    .quick-btn {
        border-radius: 999px;
        padding-inline: 1rem;
        font-weight: 600;
    }

    @media (min-width: 992px) {
        .welcome-panel {
            padding: 1.35rem 1.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="welcome-panel mb-4">
    <p class="text-uppercase text-primary fw-semibold mb-1 small">Owner Dashboard</p>
    <h1 class="fw-bold mb-2 h3 h-md-2">Project Control Center</h1>
    <p class="text-secondary mb-0">
        Track projects, bids, and shortlisted contractors from one place.
    </p>
</div>

<section class="mb-4 mb-lg-5">
    <div class="row g-3 g-md-4">
        <div class="col-6 col-xl-4">
            <article class="card kpi-card p-3 p-md-4">
                <span class="kpi-label"><i class="bi bi-building"></i>Total Projects</span>
                <p class="kpi-value mb-0">{{ $kpis['total_projects'] }}</p>
            </article>
        </div>
        <div class="col-6 col-xl-4">
            <article class="card kpi-card p-3 p-md-4">
                <span class="kpi-label"><i class="bi bi-folder2-open"></i>Open Projects</span>
                <p class="kpi-value mb-0">{{ $kpis['open_projects'] }}</p>
            </article>
        </div>
        <div class="col-6 col-xl-4">
            <article class="card kpi-card p-3 p-md-4">
                <span class="kpi-label"><i class="bi bi-arrow-repeat"></i>In Progress</span>
                <p class="kpi-value mb-0">{{ $kpis['in_progress_projects'] }}</p>
            </article>
        </div>
        <div class="col-6 col-xl-4">
            <article class="card kpi-card p-3 p-md-4">
                <span class="kpi-label"><i class="bi bi-check2-circle"></i>Completed</span>
                <p class="kpi-value mb-0">{{ $kpis['completed_projects'] }}</p>
            </article>
        </div>
        <div class="col-6 col-xl-4">
            <article class="card kpi-card p-3 p-md-4">
                <span class="kpi-label"><i class="bi bi-cash-stack"></i>Bids Received</span>
                <p class="kpi-value mb-0">{{ $kpis['bids_received'] }}</p>
            </article>
        </div>
        <div class="col-6 col-xl-4">
            <article class="card kpi-card p-3 p-md-4">
                <span class="kpi-label"><i class="bi bi-bookmark-star"></i>Shortlist Count</span>
                <p class="kpi-value mb-0">{{ $kpis['shortlist_count'] }}</p>
            </article>
        </div>
    </div>
</section>

<section>
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
        <h2 class="h4 fw-bold mb-0">Quick Actions</h2>
        <p class="small text-secondary mb-0">Start posting real projects to activate your pipeline.</p>
    </div>
    <div class="row g-3 g-md-4">
        <div class="col-12 col-md-6 col-xl-4">
            <article class="card quick-card p-4">
                <div class="quick-icon mb-3"><i class="bi bi-plus-square"></i></div>
                <h3 class="h5 fw-semibold">Post Project</h3>
                <p class="text-secondary mb-3">Create a new project and invite contractors to bid.</p>
                <a href="{{ route('owner.projects.create') }}" class="btn btn-primary quick-btn">Create Now</a>
            </article>
        </div>
        <div class="col-12 col-md-6 col-xl-4">
            <article class="card quick-card p-4">
                <div class="quick-icon mb-3"><i class="bi bi-clipboard2-check"></i></div>
                <h3 class="h5 fw-semibold">View Projects</h3>
                <p class="text-secondary mb-3">Track project status, budget, and location details in one place.</p>
                <a href="{{ route('owner.projects') }}" class="btn btn-primary quick-btn">Open Projects</a>
            </article>
        </div>
        <div class="col-12 col-md-6 col-xl-4">
            <article class="card quick-card p-4">
                <div class="quick-icon mb-3"><i class="bi bi-people"></i></div>
                <h3 class="h5 fw-semibold">Browse Contractors</h3>
                <p class="text-secondary mb-3">Discover contractor profiles, portfolio, and ratings.</p>
                <button type="button" class="btn btn-primary quick-btn" disabled>Coming Soon</button>
            </article>
        </div>
    </div>
</section>
@endsection
