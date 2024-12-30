@extends('layouts.unternehmen')

@section('appContent')
    <div class="container-fluid">
    <div class="jumbotron">
        <h2 class="display-5">Herzlich willkommen in Ihrem Praxisportal!</h2>
        <p class="lead">Tragen Sie hier die An- und Abwesenheiten ihrer Studierenden ein, füllen Sie einfach und bequem die Praxis-/Evaluationsbögen/Berichte digital aus und führen Sie Buch (Logbuch) über die in der Praxis erworbenen Kompetenzen in Ihrem Betrieb. So behalten Sie die Aktivitäten und Aufgaben Ihrer Studierenden jederzeit im Blick und sehen diverse Fremdpraktika bei Kooperationspartnern ein.
        </p>
    </div>
        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Aktuelle Praxisteilnehmer</h5>
                        <h6 class="card-subtitle mb-2 text-muted">Teilnehmer, die aktuell als Praxis Ihrem Account zugeordnet sind.</h6>
                        <table id="table_id" class="data-table table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>Nachname</th>
                                <th>Vorname</th>
                                <th>Bis</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($schuler as $schulers)
                                <tr>
                                    <td>{{ $schulers->lastname }}</td>
                                    <td>{{ $schulers->firstname }}</td>
                                    <td></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-6">
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
            </div>
            </div>
        </div>
    </div>
@endsection
