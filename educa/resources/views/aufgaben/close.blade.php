@extends('layouts.aufgaben')

@section('pageContent')
    <div class="container">
        <ul class="nav nav-tabs mt-3">
            <li class="nav-item">
                <a class="nav-link" href="/tasks"><i class="fas fa-envelope-open-text"></i>  Offene Aufgaben</a>
            </li>
            <li class="nav-item ">
                <a class="nav-link " href="/tasks/wait"><i class="fas fa-hourglass-start"></i> Wartet auf Rückmeldung</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="/tasks/close"><i class="fas fa-clipboard-check"></i>  Abgeschlossen Aufgaben</a>
            </li>
        </ul>
        <div>
                    @foreach($einreichungen as $einreichung)
                        <a href="/tasks/detail/{{ $einreichung->aufgabe->id }}/handIn" class="text-decoration-none">
            <div class="card mt-2">
                <div class="card-body">
                    <h5 class="card-title">{{ $einreichung->aufgabe->title }}</h5>
                    <h6 class="card-subtitle mb-2 text-muted"><i class="fas fa-check"></i> Bewertet {{ \Carbon\Carbon::parse($einreichung->updated_at)->diffForHumans()}} von {{ $einreichung->aufgabe->ersteller->name }}</h6>
                    <p class="card-text"></p>
                </div>
            </div></a>
                    @endforeach
        </div>
    </div>
@endsection
