@extends('layouts.loggedIn')

@section('appContent')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/verwaltung">Verwaltung</a></li>
            <li class="breadcrumb-item active" aria-current="page">Stammdaten</li>
        </ol>
    </nav>
    <div class="container-fluid subpage-main">
        <div class="row">
            <div class="col-2">
                <h3>{{ __('Stammdaten') }}</h3>
                <ul class="list-group">
                    <li class="list-group-item"><a href="/verwaltung/stammdaten/dozenten" class="text-reset">{{ __('Dozenten') }}</a></li>
                    <li class="list-group-item"><a href="/verwaltung/stammdaten/klassen" class="text-reset">{{ __('Klassen') }}</a></li>
                    <li class="list-group-item"><a href="/verwaltung/stammdaten/raume" class="text-reset">{{ __('Räume') }}</a></li>
                    <li class="list-group-item"><a href="/verwaltung/stammdaten/kontakte" class="text-reset">{{ __('Kontakte & Partner') }}</a></li>
                    <li class="list-group-item"><a href="/verwaltung/stammdaten/benutzer" class="text-reset">{{ __('System-Benutzer') }}</a></li>
                </ul>
            </div>
            <div class="col">
                @yield('siteContent')
            </div>
        </div>

@endsection
