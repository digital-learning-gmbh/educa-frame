@extends('verwaltung.main')

@section('siteContent')
    <h3>{{ \App\Providers\AppServiceProvider::schoolTranslation('studiengange', 'Studiengänge') }}</h3>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ \App\Providers\AppServiceProvider::schoolTranslation('studiengange', 'Studiengänge') }}</h5>
            <h6 class="card-subtitle mb-2 text-muted">Übersichtsliste von {{ \App\Providers\AppServiceProvider::schoolTranslation('studiengange', 'Studiengänge') }} im System</h6>
            <div style="margin-bottom: 3px;">
                <div class="float-right">
                    <a href="#" data-toggle="modal" data-target="#exampleModal" class="btn btn-primary">{{ \App\Providers\AppServiceProvider::schoolTranslation('studiengang', 'Studiengang') }} anlegen</a>
                </div>
                <div class="clearfix"></div>
            </div>
            <table id="table_id" class="data-table table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Bezeichnung</th>
                    <th>Bereich</th>
                    <th>Abkürzung</th>
                    <th>Abschluss</th>
                    <th>Regelstudienzeit</th>
                    <th>Studienorte</th>
                    <th>Semesterstart</th>
                    <th>Aktion</th>
                </tr>
                </thead>
                <tbody>
                @foreach(\App\Studium::all() as $studiengang)
                <tr>
                    <td>{{ $studiengang->name }}</td>
                    <td>{{ $studiengang->subjectDirection->name }}</td>
                    <td>{{ $studiengang->name_short }}</td>
                    <td>{{ $studiengang->graduation }}</td>
                    <td>{{ $studiengang->normal_period }}</td>
                    <td>{{ join(", " , $studiengang->schulen->pluck('name')->toArray()) }}</td>
                    <td>{{ $studiengang->start_period }}</td>
                    <td>
                        <a href="/verwaltung/studiengange/{{ $studiengang->id }}" class="btn btn-xs btn-primary"><i class="fas fa-edit"></i></a>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal to add a new teilnehmer -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ \App\Providers\AppServiceProvider::schoolTranslation('studiengang', 'Studiengang') }} hinzufügen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                    <form class="form" method="post" action="{{route('studiengang.create')}}">
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Bezeichnung</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                                <label>Bereich</label>
                                <select name="subject_direction_id" class="form-control select2" required>
                                    @foreach(\App\SubjectDirection::all() as $direction)
                                        <option value="{{ $direction->id }}">{{ $direction->name }}</option>
                                    @endforeach
                                </select>
                                <label>Abschluss</label>
                                <input type="text" class="form-control" id="graduation" name="graduation" required>
                                <label>Abschluss (Abkürzung)</label>
                                <input type="text" class="form-control" id="graduation_short" name="graduation_short" required>
                                <label>Regelstudienzeit</label>
                                <input type="number" class="form-control" id="normal_period" name="normal_period" value="6" required>
                            </div>
                        </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Erstellen</button>
                </div>
                        </form>
            </div>
        </div>
    </div>

@endsection

