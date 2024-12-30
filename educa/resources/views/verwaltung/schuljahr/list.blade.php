@extends('verwaltung.main')

@section('siteContent')
    <h3>Semester</h3>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Semester</h5>
            <h6 class="card-subtitle mb-2 text-muted">Verwalten Sie die Semester des Studienortes.</h6>
            <div style="margin-bottom: 3px;">
                <div class="float-right">
                    <a href="#" data-toggle="modal" data-target="#addSchuljahr" class="btn btn-primary">Hinzufügen</a>

                </div>
                <div class="clearfix"></div>
            </div>
            <table id="table_id" class="data-table table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Reihenfolge</th>
                    <th>Planung</th>
                    <th>Start</th>
                    <th>Ende</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($schule->schuljahre as $schuljahr)
                    <tr>
                        <td>{{ $schuljahr->name }}</td>
                        <td>{{ $schuljahr->year }}</td>
                        <td>@if($schuljahr->planung) <i class="fas fa-screwdriver"></i> @else <i class="fas fa-check"></i> @endif</td>
                        <td>{{ \Carbon\Carbon::parse($schuljahr->start)->format("d.m.Y") }}</td>
                        <td>{{ \Carbon\Carbon::parse($schuljahr->ende)->format("d.m.Y") }}</td>
                        <td>
                            <a href="/verwaltung/schuljahre/schuljahr/{{ $schuljahr->id }}" class="btn btn-xs btn-primary"><i class="fas fa-edit"></i></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="addSchuljahr" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Schuljahr anlegen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="/verwaltung/schuljahre/addSchuljahr">
                    @csrf

                    <div class="modal-body">
                        <p><b>Hinweis:</b> Das Schuljahr wird immer erst als Entwurf angelegt. Sobald die Planung abgeschlossen ist, muss die Einstellung angepasst werden.</p>
                        <div class="form-group">
                            <label>Name</label>
                            <input name="name" type="text" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Jahr</label>
                            <input name="year" type="number" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Beginn</label>
                            <div class="input-group date" id="datepicker3" data-target-input="nearest">
                                <input id="from" name="from" type="text" class="form-control datetimepicker-input" data-target="#datepicker3" required/>
                                <div class="input-group-append" data-target="#datepicker3" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Ende</label>
                            <div class="input-group date" id="datepicker4" data-target-input="nearest">
                                <input id="to" name="to" type="text" class="form-control datetimepicker-input" data-target="#datepicker4" required/>
                                <div class="input-group-append" data-target="#datepicker4" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <h3>Informationen übernehmen</h3>
                            <select class="select2" id="useYear" name="useYear" onchange="toggleCheckboxes()">
                                <option value="-1" selected>Nichts übernehmen</option>
                                @foreach($schule->schuljahre()->get() as $schuljahr)
                                    <option value="{{$schuljahr->id}}">{{$schuljahr->name}}</option>
                                @endforeach
                            </select>

                            <div class="form-check">
                                <input name="useKlassen" id="useKlassen" type="checkbox" class="form-check-input usethings" disabled="disabled">
                                <label class="form-check-label" for="useKlassen">Klassen übernehmen</label>
                            </div>

                            <div class="form-check">
                                <input name="useTeilnehmerZuordnung" id="useTeilnehmerZuordnung" type="checkbox" class="form-check-input usethings" disabled="disabled">
                                <label class="form-check-label" for="useTeilnehmerZuordnung">Teilnehmerzuordnung übernehmen</label>
                            </div>

                            <div class="form-check">
                                <input name="useLehrplanZuordnung" id="useLehrplanZuordnung" type="checkbox" class="form-check-input usethings" disabled="disabled">
                                <label class="form-check-label" for="useLehrplanZuordnung">Lehrplanzuordnung übernehmen</label>
                            </div>

                            <div class="form-check">
                                <input name="useFehlzeiten" id="useFehlzeiten" type="checkbox" class="form-check-input usethings" disabled="disabled">
                                <label class="form-check-label" for="useFehlzeiten">Fehlzeit-Typen übernehmen</label>
                            </div>

                            <div class="form-check">
                                <input name="useBreaks" id="useBreaks" type="checkbox" class="form-check-input usethings" disabled="disabled">
                                <label class="form-check-label" for="useBreaks">Pausen / Zeitraster übernehmen</label>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                        <button type="submit" class="btn btn-primary">Erstellen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        function toggleCheckboxes()
        {
            if($("#useYear").val() == -1)
            {
                $(".usethings").attr('disabled', 'disabled');
            } else {
                $(".usethings").removeAttr('disabled');
            }
        }
    </script>

@endsection
