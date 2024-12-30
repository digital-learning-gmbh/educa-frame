@extends('verwaltung.einstufungstest.main')

@section('pageContent')
    <div class="container">
        <h4>Vielen Dank für die Teilnahme</h4>
        <div class="card mt-2">
            <div class="card-body">
                <div class="card-title"><h2>Deine Einstufung</h2></div>
               Punkte: {{ $answer->score }} <br>
            Bewertung: {{ $answer->bewertung() }}</div>
            <div class="card-footer">
                <a href="/external/einstufungstest/" class="btn btn-success"
                   style="float: right;">Zurück zur Übersicht <i class="fas fa-arrow-alt-circle-right"></i></a>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
@endsection
