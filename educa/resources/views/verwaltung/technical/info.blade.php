@extends('verwaltung.technical.main')

@section('siteContent')
    <h3>{{ __('Systeminformationen') }}</h3>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">StuPla</h5>
            <h6 class="card-subtitle mb-2 text-muted">made with ❤️ in Göttingen</h6>
            <p class="card-text">Systemversion: </p>
        </div>
    </div>
    <div class="card mt-1">
        <div class="card-body">
            <h5 class="card-title">Configuration</h5>
            <p class="text-monospace"><pre>{{ var_export($config) }}</pre></p>
        </div>
    </div>
@endsection
