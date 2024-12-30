@extends('layouts.aufgaben')

@section('pageContent')
    <style>
        .card {
            background-color: #f7f7f7;
        }
        table {
            background-color: white;
        }
    </style>
<div class="container-fluid" style="margin-top: 10px;">
    @include('aufgaben.modal.details.header')
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link " href="/tasks/detail/{{ $event->id }}"><i class="fas fa-calendar-week"></i>  Aufgabenstellung</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="/tasks/detail/{{ $event->id }}/files"><i class="fas fa-folder-open"></i> Dateien</a>
        </li>
        @if($event->handIn != "no")
            @if($event->cloudid != $cloud_user->id)
        <li class="nav-item">
            <a class="nav-link" href="/tasks/detail/{{ $event->id }}/handIn"><i class="fas fa-envelope-open-text"></i> Einreichung</a>
        </li>
            @else
        <li class="nav-item">
            <a class="nav-link" href="/tasks/detail/{{ $event->id }}/handInAll"><i class="fas fa-clipboard-check"></i> Einreichung der Nutzer</a>
        </li>
            @endif
        @endif
    </ul>
    <div class="card" style="margin-top: 20px;">
        <div class="card-body">
            <h5 class="card-title">Dateien</h5>
            <h6 class="card-subtitle mb-2 text-muted">Hier können weitere Unterlagen für die Aufgabe bereitgestellt werden</h6>
            @component('documents.list',[ "model" => $event, "type" => "hausaufgabe"])
            @endcomponent
        </div>
    </div>
</div>
@endsection
