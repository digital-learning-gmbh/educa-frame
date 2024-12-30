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
        <img src="/images/geraete_launcher.png" width="30" height="30" class="d-inline-block align-top img-rounded" alt="">
        Geräte-Manager
    </a>
    @include('layouts.snippets.appSwitcherTop')
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="nav navbar-nav">
            <li><a class="nav-link active" href="/devices">Übersicht</a></li>
            <li><a class="nav-link active" href="/devices/ausleihe1">Neue Ausleihe</a></li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle active" href="/verwaltung" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Geräte
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                    @foreach($ressourcen as $resource)
                        <li><a class="dropdown-item" href="/devices/ressource/view/{{ $resource->id }}">{{ $resource->name }}</a></li>
                    @endforeach
                </ul>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            @if($canManage)
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle active" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Verwalten <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="/devices/ressource/all" class="dropdown-item">Geräte verwalten</a></li>
                            <li><a href="/devices/ressource/add" class="dropdown-item">Gerät hinzufügen</a></li>
                        </ul>
                    </li>
            @endif
            @include('layouts.snippets.account')
        </ul>
    </div>
</nav>
<div class="container-fluid">
    @if (session('status2'))
        <div class="alert alert-success">
            {{ session('status2') }}
        </div>
    @endif
    @yield('pageContent')
</div>
@endsection
