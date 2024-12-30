@extends('layouts.loggedIn')

@section('appContent')
    @if (session('status2'))
        <div class="alert alert-success">
            {{ session('status2') }}
        </div>
    @endif
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
            <li class="breadcrumb-item active" aria-current="page">Einstufungstest</li>
        </ol>
    </nav>
    <div class="container-fluid subpage-main">
        <div class="row">
            <div class="col-2">
                <h3>Einstufungstest <a href="/external/einstufungstest" target="_blank"><i class="fas fa-external-link-alt"></i></a></h3>
                <ul class="list-group">
                    <li class="list-group-item"><a href="/verwaltung/einstufungstest"
                                                   class="text-reset">{{ __('Tests') }}</a></li>
                </ul>
            </div>
            <div class="col">
                @yield('siteContent')
            </div>
        </div>
@endsection
