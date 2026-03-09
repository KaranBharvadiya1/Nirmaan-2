@extends('owner.layouts.app', ['pageTitle' => 'Owner Messages - Nirmaan', 'activePage' => 'messages'])

@section('content')
<div class="mb-4">
    <p class="text-uppercase text-primary fw-semibold mb-1 small">Owner Communication</p>
    <h1 class="fw-bold mb-2 h3 h-md-2">Project Messaging</h1>
    <p class="text-secondary mb-0">Message contractors directly from bid and hire threads.</p>
</div>

@include('shared.messaging.workspace')
@endsection

