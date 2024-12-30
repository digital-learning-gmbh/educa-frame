@extends('verwaltung.main')

@section('siteContent')
    <h3>{{ __('Vorlagen') }}</h3>
    <div class="card mt-2">
        <div class="card-body">
            <h5 class="card-title">Vorlage: {{ $formular->name }}</h5>
            <h6 class="card-subtitle mb-2 text-muted">Bearbeitungsmodus Vorlage</h6>
            <div style="margin-bottom: 3px;">
                <div class="clearfix"></div>
            </div>
            <div id="react-administration-templates-hook" jwt="{{ \Illuminate\Support\Facades\Session::get("jwt_token") }}" school_id="{{ $global_school->id }}" year_id="{{ $global_year->id }}" draft_id="{{ $global_entwurf->id }}"></div>
        </div>
    </div>

@endsection
