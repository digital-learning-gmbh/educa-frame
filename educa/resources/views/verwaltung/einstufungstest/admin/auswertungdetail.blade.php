@extends('verwaltung.einstufungstest.admin.main')


@section('siteContent')
    <style>
        .wrongAnswer {
            display: inline-block;
            background-color: #C21807;
            padding: 3px;
        }
        .rightAnswer {
            padding: 3px;
            display: inline-block;
            background-color: #32CD32;
        }
    </style>
    <h2>Auswertung von {{ $antwort->user->displayName }}
    </h2>

    <div class="card">
        <div class="card-body">
            <div class="card-title">Übersicht</div>
           <h4>Punkte: {{ $antwort->score }}, Bewertung: {{ $antwort->bewertung()}}</h4>
            @for($i = 0; $i < count($antwortstext); $i++)
                <h5>Aufgabe {{ $i+1 }}</h5>
          {!! $antwortstext[$i] !!}
                @endfor
        </div>
    </div>
@endsection
