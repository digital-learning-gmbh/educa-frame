@extends('verwaltung.technical.main')

@section('siteContent')
    <h3>{{ __('Background-Jobs') }}</h3>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Aktive Jobs</h5>
            <h6 class="card-subtitle mb-2 text-muted">{{ $count }} jobs in der Warteschlange</h6>
        </div>
    </div>
    <div class="card mt-1">
        <div class="card-body">
            <h5 class="card-title">Fehlgeschlagene Jobs</h5>
            <h6 class="card-subtitle mb-2 text-muted">{{ $failed }} Jobs sind fehlgeschlagen!</h6>
        </div>
    </div>
@endsection
