@extends('layouts.loggedIn')

@section('appContent')
    <style>
        body {
            padding-top: 100px;
        }
    </style>
<div class="container">
    <div class="row">
        <div class="col-12">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">{{ $geschickt->formularRevision->formular->name }}</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">{{ $geschickt->attr('belongsTo')->displayName  }}</a>
                </li>
            </ul>
        </div>
    </nav>
        </div>
        <div class="col-9 p-3">
            @foreach($html as $d)
                {!! $d !!}
            @endforeach
        </div>
        <div class="col-3">
            <form class="bg-light p-3">
                    <label for="staticEmail"><b>Ersteller</b></label>
                    <input type="text" readonly class="form-control-plaintext" id="staticEmail" value="{{ $geschickt->attr('creator')->name  }}">
                <label for="staticEmail"><b>Gehört zu</b></label>
                <input type="text" readonly class="form-control-plaintext" id="staticEmail" value="{{ $geschickt->attr('belongsTo')->displayName  }}">
                <label for="staticEmail"><b>Erstellt</b></label>
                <input type="text" readonly class="form-control-plaintext" id="staticEmail" value="{{ $geschickt->created_at  }}">

            </form>
        </div>
    </div>
</div>

@endsection
