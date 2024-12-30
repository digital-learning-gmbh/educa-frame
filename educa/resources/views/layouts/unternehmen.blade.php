@extends('app')

@section("content")
    <style>
        body {
            padding-top: 60px;
        }
    </style>
    <nav class="navbar fixed-top navbar-light bg-light navbar-expand-lg fixed-top fixed-top">
        <a class="navbar-brand" href="#"  id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <img src="/images/unternehmen_launcher.png" width="30" height="30" class="d-inline-block align-top img-rounded" alt="">
            Praxisportal
        </a>
        @include('layouts.snippets.appSwitcherTop')
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-collapse collapse" id="navbar2">
            <ul class="nav navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="/unternehmen" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-home"></i> Startseite
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/unternehmen/teilnehmer" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-user-friends"></i> Teilnehmer
                    </a>
                </li>
                <!-- <li class="nav-item">
                    <a class="nav-link" href="/unternehmen/plan" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-calendar-alt"></i> Praxis-Pläne
                    </a>
                </li> -->
                <li class="nav-item">
                    <a class="nav-link" href="/unternehmen/formular" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-folder"></i> Formulare / Berichte
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/unternehmen/dokumente" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-folder"></i> Dokumente
                    </a>
                </li>
            </ul>
            <ul class="nav navbar-nav ml-auto">
                @include('layouts.snippets.account')
            </ul>
        </div>
    </nav>
    @yield('appContent')
@endsection
