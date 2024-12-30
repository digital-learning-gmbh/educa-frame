@extends('layouts.unternehmen')

@section('appContent')
    <div class="container-fluid">
        <h2>Formulare</h2>
        <div class="row">
        <div class="col col-6">
            <div class="card">
            <div class="card-body">
                <h5 class="card-title">Verfügbare Formulare</h5>
                <h6 class="card-subtitle mb-2 text-muted">Übersichtsliste über Formulare, auf welche Sie Zugriffsrechte besitzen</h6>
                <table id="table_id" class="data-table table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>Formularname</th>
                        <th>Revision</th>
                        <th>Aktion</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($kontakt->getFormulare() as $formular)
                    <tr>
                        <td>{{ $formular->name }}</td>
                        <td>{{ $formular->last_revision->number }}</td>
                        <td>
                            <a href="/unternehmen/formular/{{ $formular->id }}" class="btn btn-xs btn-primary">
                                <i class="fas fa-play"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            </div></div>
        <div class="col col-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Gesendete Formulare</h5>
                <h6 class="card-subtitle mb-2 text-muted">Übersichtsliste über bereits ausgefüllte Formulare</h6>
                <table id="table_id" class="data-table table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>Formularname</th>
                        <th>Studierende(r)</th>
                        <th>Datum</th>
                        <th>Aktion</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($geschickte as $geschickt)
                        <tr>
                            <td>{{ $geschickt->formularRevision->formular->name }}</td>
                            @if($geschickt->attr('belongsTo') != null)
                            <td>{{ $geschickt->attr('belongsTo')->displayName }}</td>
                            @else
                            <td><i>Studentendaten gelöscht</i></td>
                            @endif
                            <td>{{ $geschickt->created_at }}</td>
                            <td>
                                <a href="/unternehmen/formular/{{ $geschickt->id }}/delete" class="btn btn-xs btn-danger">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        </div>
        </div>
    </div>
@endsection
