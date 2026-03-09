@extends('contractor.layouts.app', ['pageTitle' => 'Contractor Messages - Nirmaan', 'activePage' => 'messages'])

@section('content')
<div class="mb-4">
    <p class="text-uppercase text-primary fw-semibold mb-1 small">Contractor Communication</p>
    <h1 class="fw-bold mb-2 h3 h-md-2">Owner Messaging</h1>
    <p class="text-secondary mb-0">Coordinate directly with owners on bid and awarded project threads.</p>
</div>

@include('shared.messaging.workspace')
@endsection

