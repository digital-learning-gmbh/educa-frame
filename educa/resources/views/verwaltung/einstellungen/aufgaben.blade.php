@extends('verwaltung.main')

@section('siteContent')
    <h3>{{ __('Aufgaben') }}</h3>
    <div class="card-deck">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Neu</h5>
                <h1 class="text-capitalize text-center">{{ $aufgabenNeu }}</h1>
            </div>
            <div class="card-footer">
                <small class="text-muted">Anzahl der Aufgaben, die neu sind, aber noch nicht bearbeitet wurden.</small>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Bearbeitung</h5>
                <h1 class="text-capitalize text-center">{{ $aufgabenBearbeitung }}</h1>
            </div>
            <div class="card-footer">
                <small class="text-muted">Anzahl der Aufgaben, die sich in Bearbeitung befinden.</small>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Abgeschlossen</h5>
                <h1 class="text-capitalize text-center">{{ $aufgabenClosed }}</h1>
            </div>
            <div class="card-footer">
                <small class="text-muted">Anzahl der Aufgaben, deren Bearbeitung abgeschlossen wurde.</small>
            </div>
        </div>
    </div>
    <div class="card mt-2">
        <div class="card-body">
            <h5 class="card-title">Controll-Panel</h5>
            <a href="/verwaltung/einstellungen/aufgaben/watch" class="btn btn-warning">Aufgaben-Watch starten</a>
            <a href="" class="btn btn-warning">Alle Aufgaben schließen</a>
        </div>
    </div>
    <div class="card mt-2">
        <div class="card-body">
            <h5 class="card-title">Aufgaben-Typen</h5>
            <h6 class="card-subtitle mb-2 text-muted">Liste von Aufgaben, die sich im System befinden.</h6>
            <table id="table_id" class="data-table table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Offene</th>
                    <th>Bearbeitung</th>
                    <th>Geschlossen</th>
                    <th>Aktion</th>
                </tr>
                </thead>
                <tbody>
                @foreach($models as $model)
                    <tr>
                        <td>{{ $model::name() }}</td>
                        <td>{{ \App\Aufgabe::where('status', 'new')->where('type', $model::name())->count() }}</td>
                        <td>{{ \App\Aufgabe::where('status', 'working')->where('type', $model::name())->count() }}</td>
                        <td>{{ \App\Aufgabe::where('status', 'closed')->where('type', $model::name())->count() }}</td>
                        <td>@if($model::hasSettings() != null)
                                <a href="/verwaltung/einstellungen/aufgaben/settings/{{ $model::name() }}" class="btn btn-xs btn-secondary">
                                    <i class="fas fa-cogs"></i>
                                </a>
                            @endif
                            <a href="/verwaltung/einstellungen/aufgaben/watch/{{ $model::name() }}" class="btn btn-xs btn-primary">
                                <i class="fas fa-play"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card mt-2">
        <div class="card-body">
            <h5 class="card-title">Alle offnen Aufgaben</h5>
            <h6 class="card-subtitle mb-2 text-muted">Liste von Aufgaben, die noch nicht abgeschlossen wurden.</h6>
            <table id="table_id" class="data-table table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Titel</th>
                    <th>Beschreibung</th>
                    <th>Type</th>
                    <th>Level</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @foreach($aufgabenOpen as $aufgabe)
                        <tr>
                            <td>{{ $aufgabe->title }}</td>
                            <td>{{ $aufgabe->description }}</td>
                            <td>{{ $aufgabe->type }}</td>
                            <td>{{ $aufgabe->level }}</td>
                            <td>{{ $aufgabe->status }}</td>
                        </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
