@extends('verwaltung.einstufungstest.admin.main')


@section('siteContent')
    <h2>Alle Einstufungstests
        <a href="/verwaltung/einstufungstest/create" style="float:right; margin-left: 5px;" class="btn btn-success">Test anlegen</a>
    </h2>
    @foreach($tests as $test)
        <div class="card mt-2">
            <div class="card-body">
                <div class="card-title"><h4>{{ $test->name }} @if($test->public) <i class="fas fa-eye"></i> @else <i class="fas fa-eye-slash"></i> @endif</h4></div>
                {{ $test->beschreibung }}</div>
            <div class="card-footer">
                <a href="/verwaltung/einstufungstest/test/{{ $test->id }}" class="btn btn-info" style="float: right; margin-left: 5px;"><i class="fas fa-edit"></i> Bearbeiten</a>
               <a href="/verwaltung/einstufungstest/test/{{ $test->id }}/auswertung" class="btn btn-warning" style="float: right;"><i class="fas fa-table"></i> Auswertung</a>
                <div class="clearfix"></div>
            </div>
        </div>
    @endforeach
@endsection
