@extends('layouts.loggedIn')

@section('appContent')
    <style>
        .list-group-item2 {
            position: relative;
            display: block;
            padding: 0.75rem 1.25rem;
            margin-left: 2.25rem;
            background-color: #fff;
            border: 1px solid rgba(0, 0, 0, 0.125);
        }
    </style>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/verwaltung">Verwaltung</a></li>
            <li class="breadcrumb-item active" aria-current="page">Preiskalkulator</li>
        </ol>
    </nav>
    <div class="container-fluid subpage-main">
        <div class="row">
            <div class="col-2">
                <h3>Preiskalkulator <a href="/external/preiskalkulator" target="_blank"><i class="fas fa-external-link-alt"></i></a></h3>
                <ul class="list-group">
                    <li class="list-group-item">{{ __('Kalkulatoren') }}</li>
                    <li class="list-group-item" style="padding: 0px;border-left: 0px; background-color: #f2f3f5; border-right: none;">
                        <div class="list-group2">
                            <a href="/verwaltung/preiskalkulator/?id=1" class="list-group-item2">{{ __('Sprachkurse') }}</a>
                            <a href="/verwaltung/preiskalkulator/?id=5" class="list-group-item2">{{ __('Prüfungstermine') }}</a>
                            <a href="/verwaltung/preiskalkulator/?id=2" class="list-group-item2">{{ __('Unterkunft') }}</a>
                            <a href="/verwaltung/preiskalkulator/?id=3" class="list-group-item2">{{ __('Transfer') }}</a>
                            <a href="/verwaltung/preiskalkulator/?id=4"
                               class="list-group-item2">{{ __('Zusatzleistungen') }}</a>
                        </div>
                    </li>
                    <li class="list-group-item"><a href="/verwaltung/preiskalkulator/start"
                                                   class="text-reset">{{ __('Starttermine') }}</a></li>
                    <li class="list-group-item"><a href="/verwaltung/preiskalkulator/discount"
                                                   class="text-reset">{{ __('Rabatt-Codes') }}</a></li>
                    <li class="list-group-item"><a href="/verwaltung/preiskalkulator/settings"
                                                   class="text-reset">{{ __('Einstellungen') }}</a></li>
                </ul>
            </div>
            <div class="col">
                @yield('siteContent')
            </div>
        </div>

@endsection
