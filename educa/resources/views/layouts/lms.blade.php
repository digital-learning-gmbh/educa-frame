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
        <img src="/images/edu_launcher.png" width="30" height="30" class="d-inline-block align-top img-rounded" alt="">
        LMS
    </a>
    @include('layouts.snippets.appSwitcherTop')
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="nav navbar-nav">
            <li><a class="btn btn-primary active" style="margin-right: 5px;" href="/lms/viewer/back?c={{ $coursefile }}&chapter={{ $c_chapter  }}&page={{ $c_page }}">Zurück</a></li>
            <li><a class="btn btn-primary active" href="/lms/viewer/next?c={{ $coursefile }}&chapter={{ $c_chapter }}&page={{ $c_page }}">Weiter</a></li>
        </ul>
    </div>
</nav>
<div class="container-fluid">
    @yield('pageContent')
</div>
@endsection
