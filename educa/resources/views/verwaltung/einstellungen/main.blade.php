@extends('layouts.loggedIn')

@section('appContent')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/verwaltung">Verwaltung</a></li>
            <li class="breadcrumb-item active" aria-current="page">Einstellungen</li>
        </ol>
    </nav>
    <div class="container-fluid subpage-main">
        <div class="row">
            <div class="col-2">
                <h3>{{ __('Einstellungen') }}</h3>
                <ul class="list-group">
                    <li class="list-group-item"><a href="/verwaltung/einstellungen/aufgaben" class="text-reset">{{ __('Aufgaben') }}</a></li>
                    <li class="list-group-item"><a href="/verwaltung/einstellungen/formulare" class="text-reset">{{ __('Formulare') }}</a></li>
                    <li class="list-group-item"><a href="/verwaltung/einstellungen/smart-dokumente" class="text-reset">{{ __('Smart-Dokumente') }}</a></li>
                    <li class="list-group-item"><a href="/verwaltung/einstellungen/nachrichten" class="text-reset">{{ __('Nachrichten') }}</a></li>
                    <li class="list-group-item"><a href="/verwaltung/einstellungen/apikeys" class="text-reset">{{ __('API-Schlüssel') }}</a></li>
                    <li class="list-group-item"><a href="/verwaltung/einstellungen/system" class="text-reset">{{ __('System-Einstellungen') }}</a></li>
                </ul>
            </div>
            <div class="col">
                @yield('siteContent')
            </div>
        </div>

@endsection
