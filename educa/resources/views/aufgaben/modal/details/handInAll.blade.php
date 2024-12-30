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
            <a class="nav-link" href="/tasks/detail/{{ $event->id }}/files"><i class="fas fa-folder-open"></i> Dateien</a>
        </li>
        @if($event->handIn != "no")
            @if($event->cloudid != $cloud_user->id)
        <li class="nav-item">
            <a class="nav-link " href="/tasks/detail/{{ $event->id }}/handIn"><i class="fas fa-envelope-open-text"></i> Einreichung</a>
        </li>
            @else
        <li class="nav-item">
            <a class="nav-link active" href="/tasks/detail/{{ $event->id }}/handInAll"><i class="fas fa-clipboard-check"></i> Einreichung der Nutzer</a>
        </li>
            @endif
        @endif
    </ul>
    <div class="container"> <a href="/tasks/detail/{{ $event->id }}/handInAll?all=review" class="mt-2 btn btn-danger">Alles einsammeln</a>

        <div class="card" style="margin-top: 20px;">
        <div class="card-body">
           @foreach($event->gruppen as $gruppe)
                <h5 class="card-title">{{ $gruppe->name }}</h5>
                <table style="background-color: white;"  class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>E-Mail</th>
                        <th>Bearbeitungsstatus</th>
                        <th>Aktion</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($gruppe->members() as $cloud)
                        <tr>
                            <td>{{ $cloud->name }}</td>
                            <td>{{ $cloud->email }}</td>
                            <td>{!! $event->statusForUserHtml($cloud->id) !!}</td>
                            <td>@if($event->einreichungForUser($cloud->id)->stage == "review")<a href="/tasks/detail/{{ $event->id }}/handInAll/{{ $event->einreichungForUser($cloud->id)->id }}/rating" class="btn btn-xs btn-secondary">Bewerten</a> @else <a href="" class="btn btn-xs btn-secondary">Ansehen</a> @endif</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endforeach
        </div>
        </div>
    </div>
</div>
@endsection
