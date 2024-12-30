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
        <img src="/images/help.png" width="30" height="30" class="d-inline-block align-top img-rounded" alt="">
        Office
    </a>
    @include('layouts.snippets.appSwitcherTop')
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="nav navbar-nav">
        </ul>
        <form class="form-inline" style="margin-bottom: 0px;" action="/help" method="POST">
            @csrf
            <div id="searchDropdown" class="dropdown">
                <input id="searchHelp" class="form-control mr-sm-2" name="q" type="search" placeholder="Suchbegriff..." aria-label="Search" style="width: 300px;">
                <button class="btn btn-outline-dark my-2 my-sm-0" type="submit"><i class="fas fa-search"></i></button>
                <ul id="search_results" class="dropdown-menu" aria-labelledby="qbox" style="width: 100%;">
                    <li class="dropdown-item">Suche ...</li>
                </ul>
            </div>
        </form>
        <ul class="navbar-nav ml-auto">
            @include('layouts.snippets.account')
        </ul>
    </div>
</nav>
    @yield('pageContent')
@endsection
