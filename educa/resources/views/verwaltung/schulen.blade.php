@extends('verwaltung.main')

@section('siteContent')
    <h3>{{ __('Studienorte') }}</h3>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Übersicht der Studienorte</h5>
            <h6 class="card-subtitle mb-2 text-muted">Übersichtsliste von allen Studienorte, wo Sie Zugriffsrechte besitzen</h6>
            <div style="margin-bottom: 3px;">
                <div class="float-right">
                    <a href="/verwaltung/schulen?show=all" class="btn btn-primary">Alle anzeigen</a>
                </div>
                <div class="clearfix"></div>
            </div>
            <table id="table_id" class="data-table table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Lizenz</th>
                    <th>Aktion</th>
                </tr>
                </thead>
                <tbody>
                @foreach($schulen as $schule)
                <tr>
                    <td>{{ $schule->name }}</td>
                    <td>{{ $schule->licence }} @if($schule->valid) <i class="far fa-check-circle"></i> @else <i class="far fa-times-circle"></i> @endif</td>
                    <td>
                        <a href="/verwaltung/schulen/{{ $schule->id }}" class="btn btn-xs btn-primary"><i class="fas fa-edit"></i></a>
                        <a href="/switchSchool?id={{ $schule->id }}" class="btn btn-xs btn-secondary"><i class="fas fa-random"></i></a>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
