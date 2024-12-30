@extends('layouts.loggedIn')

@section('appContent')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/verwaltung">Verwaltung</a></li>
            <li class="breadcrumb-item active" aria-current="page"><a href="/verwaltung/schulerlisten">Schülerliste</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $schuler->displayName }}</li>
        </ol>
    </nav>
    <div class="container-fluid subpage-main">
        <div class="row">
            <div class="col-2">
                <div class="text-center mb-2">
                <img src="/api/image/schuler/?user_id={{ $schuler->id }}&size=100" class="rounded-circle">
                </div>
                <h3 class="text-center">{{ $schuler->displayName }}</h3>

                <ul class="list-group">
                    <li class="list-group-item"><a href="/verwaltung/schulerlisten/{{ $schuler->id }}" class="text-reset">{{ __('Stammdaten') }}</a></li>
                    <li class="list-group-item"><a href="/verwaltung/schulerlisten/{{ $schuler->id }}/klassen" class="text-reset">{{ __('Klassen') }}</a></li>
                    <li class="list-group-item"><a href="/verwaltung/schulerlisten/{{ $schuler->id }}/kenntnisse" class="text-reset">{{ __('Kenntnisse') }}</a></li>
                    <li class="list-group-item"><a href="/verwaltung/schulerlisten/{{ $schuler->id }}/noten" class="text-reset">{{ __('Noten') }}</a></li>
                    <li class="list-group-item"><a href="/verwaltung/schulerlisten/{{ $schuler->id }}/fehlzeiten" class="text-reset">{{ __('Fehlzeiten') }}</a></li>
                    <li class="list-group-item"><a href="/verwaltung/schulerlisten/{{ $schuler->id }}/progress" class="text-reset">{{ __('Lernfortschritt') }}</a></li>
                    <li class="list-group-item"><a href="/verwaltung/schulerlisten/{{ $schuler->id }}/partners" class="text-reset">{{ __('Praxispartner') }}</a></li>
                    <li class="list-group-item"><a href="/verwaltung/schulerlisten/{{ $schuler->id }}/practice" class="text-reset">{{ __('Praxiseinsätze') }}</a></li>
                    <li class="list-group-item"><a href="/verwaltung/schulerlisten/{{ $schuler->id }}/dokumente" class="text-reset">{{ __('Dokumente') }}</a></li>
                </ul>
            </div>
            <div class="col">
                @yield('siteContent')
            </div>
        </div>

@endsection


@section('title')
            {{ $schuler->displayName }} - {{ join(",",$schuler->klassenAktuell()->pluck("name")->toArray()) }} @yield('titleDetail')
@endsection
