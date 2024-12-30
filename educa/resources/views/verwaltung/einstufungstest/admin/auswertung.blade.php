@extends('verwaltung.einstufungstest.admin.main')


@section('siteContent')

    <h2>Auswertung
    </h2>

    <div class="card">
        <div class="card-body">
            <div class="card-title">Übersicht</div>
            <table data-toggle="data-table" class="data-table table" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>E-Mail</th>
                    <th>Datum</th>
                    <th>Aktion</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>Name</th>
                    <th>E-Mail</th>
                    <th>Datum</th>
                    <th>Aktion</th>
                </tr>
                </tfoot>
                <tbody>
                @foreach($test->antworten as $antwort)
                    <tr>
                        <td>{{ $antwort->user->name }}</td>
                        <td>{{ $antwort->user->email }}</td>
                        <td data-order="{{ strtotime($antwort->created_at) }}">{{ date("d.m.Y H:i",strtotime($antwort->created_at)) }}</td>
                        <td><a href="/verwaltung/einstufungstest/test/{{ $test->id }}/auswertung/{{ $antwort->id }}"
                               class="btn btn-primary btn-xs"><i class="fas fa-eye"></i></a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
