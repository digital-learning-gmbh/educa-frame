@extends('layouts.aufgaben')

@section('pageContent')
    <div class="container">
        <div id="task-educa">

        </div>
        <ul class="nav nav-tabs mt-3">
            <li class="nav-item">
                <a class="nav-link active" href="/tasks"><i class="fas fa-envelope-open-text"></i>  Offene Aufgaben</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/tasks/wait"><i class="fas fa-hourglass-start"></i> Wartet auf Rückmeldung</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/tasks/close"><i class="fas fa-clipboard-check"></i>  Abgeschlossen Aufgaben</a>
            </li>
        </ul>
        <div>
            @foreach($cloud_user->gruppen() as $gruppe)
                <div class="mt-2">
                <h4>{{ $gruppe->name }}</h4>
                    @foreach($gruppe->aufgabe as $aufgabe)
                        @if($aufgabe->einreichungForUser($cloud_user->id)->stage == "draft")
                        <a href="/tasks/detail/{{ $aufgabe->id }}" class="text-decoration-none">
            <div class="card mt-2">
                <div class="card-body">
                    <h5 class="card-title">{{ $aufgabe->title }}</h5>
                    <h6 class="card-subtitle mb-2 text-muted"><i class="fas fa-clock"></i> Abgabe {{ \Carbon\Carbon::parse($aufgabe->endDate)->diffForHumans()}}, von {{ $aufgabe->ersteller->name }}</h6>
                    <p class="card-text">@if($aufgabe->handIn == "no") <i><i class="fas fa-info"></i> Keine Einreichung erforderlich</i> @else <b><i class="fas fa-exclamation-triangle"></i> Einreichung erforderlich</b> @endif</p>
                </div>
            </div></a>
                        @endif
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
@endsection
