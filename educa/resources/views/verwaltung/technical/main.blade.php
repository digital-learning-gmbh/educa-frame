@extends('layouts.loggedIn')

@section('appContent')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/verwaltung">Verwaltung</a></li>
            <li class="breadcrumb-item active" aria-current="page">Technische Verwaltung</li>
        </ol>
    </nav>
    <div class="container-fluid subpage-main">
        <div class="row">
            <div class="col-2">
                <h3>{{ __('Technische Verwaltung') }}</h3>
                <ul class="list-group">
                    <li class="list-group-item"><a href="/verwaltung/technical/info" class="text-reset">{{ __('Systeminformationen') }}</a></li>
                    <li class="list-group-item"><a href="/verwaltung/technical/logs" class="text-reset">{{ __('Log-Dateien') }}</a></li>
                    <li class="list-group-item"><a href="/verwaltung/technical/jobs" class="text-reset">{{ __('Background-Jobs') }}</a></li>
                    <li class="list-group-item"><a href="/verwaltung/technical/searchIndex" class="text-reset">{{ __('Such-Index') }}</a></li>
                    <li class="list-group-item"><a href="/verwaltung/technical/clingo" class="text-reset">{{ __('Clingo-Tool') }}</a></li>
                </ul>
            </div>
            <div class="col">
                @yield('siteContent')
            </div>
        </div>

@endsection
