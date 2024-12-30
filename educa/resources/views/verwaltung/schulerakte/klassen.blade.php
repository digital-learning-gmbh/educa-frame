@extends('verwaltung.schulerakte.main')

@section('siteContent')
    @if (session()->has('status'))
        <div class="alert alert-success">
            {{ session()->get('status') }}
        </div>
    @endif
    <div class="alert alert-info" role="alert">
        In dieser Liste befinden sich alle Klassen, die der Schüler besucht hat.
    </div>
    <div class="card">

        <div class="card-header" id="fehlzeitenHeading">
            <b>Klassen</b>
        </div>
        <div class="card-body">
            <table id="table_id" class="data-table table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Klassen</th>
                    <th>Datum (von)</th>
                    <th>Datum (bis)</th>
                    <th>Profile</th>
                    <th>Bemerkung</th>
                    <th>Aktion</th>
                </tr>
                </thead>
                <tbody>
                @foreach($schuler->klassenRelationObjects() as $klassenObjects)
                    <tr>
                        <td>
                            {{ \App\Klasse::find($klassenObjects->klasse_id)->name }}
                        </td>
                        @if($klassenObjects->from == null)
                            <td><i>Keine Begrenzung</i></td>
                        @else
                            <td>{{ date("d.m.Y",strtotime($klassenObjects->from)) }}</td>
                        @endif


                        @if($klassenObjects->until == null)
                            <td><i>Keine Begrenzung</i></td>
                        @else
                            <td>{{ date("d.m.Y",strtotime($klassenObjects->until)) }}</td>
                        @endif
                        <td>
                            @foreach($schuler->getLehrplanGroups(\App\Klasse::find($klassenObjects->klasse_id)->getLehrplan->pluck("id")) as $lehrPlanGroup)
                                <span class="badge badge-pill badge-primary" style="background-color: {{ $lehrPlanGroup->color }}">{{ $lehrPlanGroup->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            {{ $klassenObjects->note }}
                        </td>
                        <td>
                            <a href="/verwaltung/stammdaten/klassen/{{ $klassenObjects->klasse_id }}/ausscheidenSchuler/{{$schuler->id}}" class="btn btn-xs btn-warning">
                                <i class="fas fa-user-slash"></i>
                            </a>
                            <a href="/verwaltung/stammdaten/klassen/{{ $klassenObjects->klasse_id }}/edit" class="btn btn-xs btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
