@extends('app')

@section("content")
    <style>
        body {
            padding-top: 60px;
        }
    </style>
    <nav class="navbar fixed-top navbar-light bg-light navbar-expand-lg fixed-top fixed-top">
        <a class="navbar-brand" href="#"  id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <img src="/images/klassenbuch_launcher.png" width="30" height="30" class="d-inline-block align-top img-rounded" alt="">
            Klassenbuch
        </a>
        @include('layouts.snippets.appSwitcherTop')
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-collapse collapse" id="navbar2">
            <ul class="nav navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="/dozent/klassenbuch" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-book"></i> Mein Stundenplan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/dozent/noten" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-star-half-alt"></i> {{ __('Noten & Prüfungen') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/dozent/klassen" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-list"></i> Schülerlisten
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/dozent/klassenamt" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-award"></i>  Ämter
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/dozent/abwesenheit" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-history"></i> Abwesenheit
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/dozent/praxis" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-briefcase"></i> Praxisbesuche
                    </a>
                </li>
            </ul>
            <form class="form-inline ml-auto" style="margin-bottom: 0px;">
                <div id="searchDropdown" class="dropdown">
                    <input id="searchGlobal" class="form-control mr-sm-2" type="search" placeholder="Suchbegriff..." aria-label="Search">
                    <button class="btn btn-outline-dark my-2 my-sm-0" type="submit"><i class="fas fa-search"></i></button>
                    <ul id="search_results" class="dropdown-menu" aria-labelledby="qbox" style="width: 100%;">
                        <li class="dropdown-item">Suche ...</li>
                    </ul>
                </div>
            </form>
            <ul class="nav navbar-nav">
                @include('layouts.snippets.account')
            </ul>
        </div>
    </nav>
    @yield('appContent')
@endsection
