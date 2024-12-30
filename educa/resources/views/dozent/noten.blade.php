@extends('layouts.klassenbuch')

@section('appContent')
    <div class="container-fluid">
        <div class="row" style="margin-bottom: 20px;">
            <div class="col-md-3">
                <label>Klasse auswählen</label>
            </div>
            <div class="col-md-3">
                <select id="personDropDown" class="select2 mb-2" onchange="changeKlasse(this.options[this.selectedIndex].value);" data-select2-id="personDropDown" tabindex="-1" aria-hidden="true">
                    @foreach($klassen as $klasse)
                        <option value="{{ $klasse->id }}" @if($selectedKlasse->id == $klasse->id) selected @endif>{{ $klasse->displayName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-12">
        <div class="card">
            <div class="card-header" id="dokumentHeading">
                <b>Noten und Prüfungen</b>
            </div>
                <div class="card-body" id="dokumentCard">
                    <p>Hier gibt es die Möglichkeit, Prüfungen für die gesamte Klasse anzulegen</p>
                    <div style="margin-bottom: 3px;">
                        <div class="float-right">
                            <a href="#" data-toggle="modal" data-target="#exampleModal" class="btn btn-primary">Prüfung hinzufügen</a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <table id="table_id" class="data-table table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>Bezeichnung</th>
                            <th>Prüfungsart</th>
                            <th>Modul</th>
                            <th>Datum</th>
                            <th>Aktion</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($exams as $exam)
                            <tr>
                                <td>{{ $exam->name }}</td>
                                <td>{{ $exam->getTypeTranslation() }}</td>
                                <td>{{ $exam->getModule->name }}</td>
                                <td>@if($exam->date != null) {{ date("d.m.Y",strtotime($exam->date)) }} @endif</td>
                                <td>
                                    <a href="/dozent/noten/{{ $selectedKlasse->id }}/exam/edit/{{ $exam->id }}" class="btn btn-xs btn-success"><i class="fas fa-star-half-alt"></i></a>
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
                    <h5 class="modal-title" id="exampleModalLabel">Prüfung anlegen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="/dozent/noten/{{ $selectedKlasse->id }}/exam/add" method="POST">
                    @csrf
                    <div class="modal-body">
                        <label>Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <label>Prüfungsart</label>
                        <select class="select2" name="typ">
                            <option value="oral">Mündlich</option>
                            <option value="written">Schriftlich</option>
                        </select>

                        <label>Modul</label>
                        <select class="select2" name="modul">
                            @foreach($selectedKlasse->getLehrplan as $lehrplan)
                                <optgroup label="{{ $lehrplan->name }}">
                                    @foreach($lehrplan->lehreinheiten() as $einheiten)
                                    <option value="{{ $einheiten->id }}">{{ $einheiten->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>

                        <div class="form-group">
                            <label for="exampleInputEmail1">Datum</label>
                            <div class="input-group date" id="datepicker7" data-target-input="nearest">
                                <input id="date" required name="date" type="text" class="form-control datetimepicker-input" data-target="#datepicker7" />
                                <div class="input-group-append" data-target="#datepicker7" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Anlegen</button>
                    </div>
                </form>
            </div>
        </div></div>

    @if(isset($exam_auswahl))
            <div class="card" style="margin-top: 20px;">
                <div class="card-header" id="dokumentHeading">
                    <b>Prüfung: {{ $exam_auswahl->name }}</b>
                </div>
                <form method="POST" action="/dozent/noten/{{ $exam_auswahl->klasse_id }}/exam/update/{{ $exam_auswahl->id }}">
                    @csrf
                <div class="card-body" id="dokumentCard">
                    <p>Die Prüfung fand am <b>{{ date("d.m.Y",strtotime($exam_auswahl->date)) }}</b> statt und wird im Modul <b>{{ $exam_auswahl->getModule->name  }}</b> angerechnet.</p>
                    <p>Tragen Sie hier die Noten ein:</p>
                    <div style="margin-bottom: 3px;">
                        <div class="float-right">
                            <button type="submit" class="btn btn-success">Noten speichern</button>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <table id="table_id2" class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>Nachname</th>
                            <th>Vorname</th>
                            @foreach(\App\RatingKey::all() as $ratingKey)
                                <th>{{ $ratingKey->name }}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($exam_auswahl->klasse->schulerAtDatum(date("Y/m/d",strtotime($exam_auswahl->date)))->orderBy('lastname')->get() as $schuler)
                            <tr>
                                <td>{{ $schuler->lastname }}</td>
                                <td>{{ $schuler->firstname }}</td>
                                @foreach(\App\RatingKey::all() as $ratingKey)
                                    @php
                                     $note = $schuler->getNote($exam_auswahl,$ratingKey);
                                    @endphp
                                    <td><input @if($note != null) value="{{ $note->note }}" @endif name="mark_{{ $schuler->id }}_{{ $ratingKey->id }}" pattern="[0-9]+([\.,][0-9]+)?" step="0.01"  type="number" class="form-control" /></td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                </form>
            </div>
        </div>
    @endif

    <script>
        function changeKlasse(id) {
            window.location.href = "/dozent/noten/" + id;
        }
    </script>
@endsection

