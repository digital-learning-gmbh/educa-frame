@extends('app')
@section('content')
    <style type="text/css">
        body {
            background: url(/images/backgrounds/3.jpg) no-repeat center center fixed;
            background-size: cover;
        }
    </style>
    <nav class="navbar navbar-expand-lg navbar-light">
        <a class="navbar-brand" href="#">educa</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto">
            @include('layouts.snippets.account')
            <!--
            <li class="nav-item">
                <a class="nav-link" href="#" tabindex="-1" aria-disabled="true"><i class="far fa-envelope" style="font-size: 1.25rem"></i></a>
            </li>  -->
                <li class="nav-item">
                    <a class="nav-link" href="/help" tabindex="-1" aria-disabled="true"><i class="far fa-question-circle" style="font-size: 1.25rem"></i></a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Dein Account für den Zugriff auf die App wurde nicht gefunden</h5>
                <p class="card-text">Bitte wende dich an den Support.</p>
            </div>
        </div>
    </div>
@endsection
