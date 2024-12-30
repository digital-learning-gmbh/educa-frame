@extends('app')

@section("content")
    <style>
        body
        {
            margin: 0px !important;
        }
    </style>
    <nav class="navbar navbar-expand-lg bg-light navbar-light">
        <a class="navbar-brand" href="#"  id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <img src="/images/analytics_launcher.png" width="30" height="30" class="d-inline-block align-top img-rounded" alt="">
            Analytics
        </a>
        @include('layouts.snippets.appSwitcherTop')
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="nav navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="/analytics/report" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-receipt"></i> Reports
                    </a>
                </li>
                <!-- <li class="nav-item">
                    <a class="nav-link" href="/analytics/route" aria-haspopup="true" aria-expanded="false">
                       <i class="fas fa-route"></i> Lernerfahrung
                    </a>
                </li> -->
            </ul>
            <ul class="navbar-nav ml-auto">
                @include('layouts.snippets.account')
            </ul>
        </div>
    </nav>
    @yield('pageContent')
@endsection
