@extends('verwaltung.main')

@section('siteContent')
    <h3>Semester bearbeiten</h3>
    @if (session()->has('status'))
        <div class="alert alert-success">
            {{ session()->get('status') }}
        </div>
    @endif
  <!--  <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/verwaltung/schulen/{{ $schule->id }}">{{ $schule->name }}</a></li>
            <li class="breadcrumb-item"><a href="/verwaltung/schulen/{{ $schule->id }}">Schuljahr</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $schuljahr->name }}</li>
        </ol>
    </nav> -->
    <div class="card">
        <div class="tab">
            <button class="tablinks" onclick="openTab(event, 'allg')" id="link1">Allgemein</button>
            <button class="tablinks" onclick="openTab(event, 'draft')" id="link2">Entwürfe</button>
            <button class="tablinks" onclick="openTab(event, 'raster')" id="link3">Stundenraster</button>
            <button class="tablinks" onclick="openTab(event, 'formulare')" id="link6">Formulare</button>
            <button class="tablinks" onclick="openTab(event, 'technical')" id="link4">Technische Einstellungen</button>
        </div>
        <form method="POST">
            @csrf
            <div id="allg" class="tabcontent">
                <div class="form-row">
                    <div class="col-6">
                        <label>Status</label>
                        <select name="planung" class="custom-select">
                            <option value="true" @if($schuljahr->planung) selected @endif>In Planung</option>
                            <option value="false" @if(!$schuljahr->planung) selected @endif>Planung abgeschlossen</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label>Gültiger Entwurf</label>
                        <select name="entwurf" class="custom-select">
                            <option value="0" selected>Kein gültiger Entwurf</option>
                            @foreach($schuljahr->entwurfe as $entwurf)
                            <option value="{{ $entwurf->id }}" @if($schuljahr->entwurf_id == $entwurf->id) selected @endif>{{ $entwurf->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-8">
                        <label>Name</label>
                        <input name="name" type="text" class="form-control" required value="{{ $schuljahr->name }}">
                    </div>
                    <div class="col-4">
                        <label>Reihenfolge</label>
                        <input name="year" type="number" class="form-control" required value="{{ $schuljahr->year }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-6">
                        <label>Beginn</label>
                        <div class="input-group date" id="datepicker3" data-target-input="nearest">
                            <input id="from" name="from" type="text" class="form-control datetimepicker-input"
                                   data-target="#datepicker3" value="{{ date("d.m.Y",strtotime($schuljahr->start)) }}" required/>
                            <div class="input-group-append" data-target="#datepicker3" data-toggle="datetimepicker" >
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <label>Ende</label>
                        <div class="input-group date" id="datepicker4" data-target-input="nearest">
                            <input id="to" name="to" type="text" class="form-control datetimepicker-input"
                                   data-target="#datepicker4" value="{{ date("d.m.Y",strtotime($schuljahr->ende)) }}" required/>
                            <div class="input-group-append" data-target="#datepicker4" data-toggle="datetimepicker" >
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="margin: 15px;">Speichern</button>
            </div>

            <div id="formulare" class="tabcontent">
                <div class="form-row">
                    <div class="col-6">
                        <label>Formular für Unterrichtserfassung</label>
                        <select name="unterricht_formular" class="custom-select">
                            @php $unterricht_formular = $schuljahr->getEinstellungenFormular("unterricht_formular"); @endphp
                            @foreach($schule->formulare as $formular)
                                @foreach($formular->revisions as $revision)
                                <option value="{{ $revision->id }}" @if($unterricht_formular != null && $unterricht_formular->id == $revision->id) selected @endif>{{ $formular->name }} ({{ $revision->number }})</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label>Formular für Praxisbesuch</label>
                        <select name="praxis_formular" class="custom-select">
                            @php $praxis_formular = $schuljahr->getEinstellungenFormular("praxis_formular"); @endphp
                            @foreach($schule->formulare as $formular)
                                @foreach($formular->revisions as $revision)
                                    <option value="{{ $revision->id }}" @if($praxis_formular != null && $praxis_formular->id == $revision->id) selected @endif>{{ $formular->name }} ({{ $revision->number }})</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="margin: 15px;">Speichern</button>
            </div>

            <div id="technical" class="tabcontent">
                <h6>Technische Einstellungen</h6>
                <div class="form-row">
                    <div class="col-6">
                        <label>Periodenlänge (Länge für die wiederholenden Unterrichteinheiten)</label>
                        <input name="period_length" type="text" class="form-control" required
                               value="{{ $schuljahr->period_length }}">
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-6">
                        <label>Standart Wiederholungstyp für den Stundenplan</label>
                        <select name="default_period_type" class="form-control">
                            <option value="never" @if($schuljahr->getEinstellungen("default_period_type", "weekly") == "never") selected @endif>Keine</option>
                            <option value="daily" @if($schuljahr->getEinstellungen("default_period_type", "weekly") == "daily") selected @endif>Täglich</option>
                            <option value="weekly" @if($schuljahr->getEinstellungen("default_period_type", "weekly") == "weekly") selected @endif>Wöchentlich</option>
                            <option value="monthly" @if($schuljahr->getEinstellungen("default_period_type", "weekly") == "monthly") selected @endif>Monatlich</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="margin: 15px;">Speichern</button>
            </div>


            <div id="draft" class="tabcontent">
                <h5>Entwürfe</h5>
                <div style="margin-bottom: 3px;">
                    <div class="float-right">
                        <a href="/verwaltung/schuljahre/schuljahr/{{ $schuljahr->id }}/generate" class="btn btn-xs btn-warning"><i class="fas fa-cogs"></i> Stundenplan berechnen</a>
                        <a href="#" data-toggle="modal" data-target="#addEntwurf" class="btn btn-primary">Hinzufügen</a>

                    </div>
                    <div class="clearfix"></div>
                </div>
                <table id="table_id" class="data-table table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Clingo-Result</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($schuljahr->entwurfe as $entwurf)
                        <tr>
                            <td>{{ $entwurf->name }}</td>
                            <td>{{ $entwurf->clingonotes }}</td>
                            <td>
                                <a href="/verwaltung/schuljahre/schuljahr/{{ $schuljahr->id }}/entwurf/{{ $entwurf->id }}/delete" class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>


            <div id="raster" class="tabcontent">
                <h5>Unterrichtszeiten</h5>
                <div class="row">
                <div class="col-6">

                </div>
                <div class="col-6">
                </div>
                <div class="col-6">
                    <label>Wochenende anzeigen</label>
                    <div class="custom-control custom-switch">
                        <input name="showWeekend" type="checkbox" class="custom-control-input" id="customSwitch1" @if($schuljahr->getEinstellungen("showWeekend", "false") == "true") checked @endif>
                        <label class="custom-control-label" for="customSwitch1">7-Tage Woche</label>
                    </div>
                </div>

                </div>
                <button type="submit" class="btn btn-primary" style="margin: 15px;">Speichern</button>

                <h5>Pausen</h5>
                <a href="#" data-toggle="modal" data-target="#addSlot" class="btn btn-primary" style="float: right; margin-bottom: 5px;">Pause hinzufügen</a>
                <div class="clearfix"></div>
                @foreach($raster as $element)
                        <div class="card mb-1">
                            <div class="card-body">
                                <b>{{ $element->begin }} - {{ $element->end }}</b> {{ $element->displayText }}
                                <a href="#" onclick="editSlot('{{ $element->id }}', '{{ $element->begin }}','{{ $element->end }}','{{ $element->displayText }}','{{ $element->color }}')" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                <a href="/verwaltung/schuljahre/schuljahr/{{ $schuljahr->id }}/timeslot/{{ $element->id }}/delete" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                            </div>
                        </div>
                @endforeach
            </div>

            <div id="extend" class="tabcontent">
                <h5>Erweiterte Einstellungen</h5>

                <ul class="list-group">
                    <li class="list-group-item"><a href="/verwaltung/schuljahre/schuljahr/{{ $schuljahr->id }}/additional/fehlzeiten">Fehlzeiten</a></li>
                </ul>
            </div>

        </form>
    </div>

    <div class="modal fade" id="addSlot" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Pause hinzufügen
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="/verwaltung/schuljahre/schuljahr/{{ $schuljahr->id }}/addTimeslot">
                    @csrf

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Text für Stundenplan</label>
                            <input id="displayText" name="displayText" type="text" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Beginn</label>
                            <div class="input-group date" id="datepicker5" data-target-input="nearest">
                                <input id="from" name="from" type="text" class="form-control datetimepicker-input" data-target="#datepicker5" required/>
                                <div class="input-group-append" data-target="#datepicker5" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-clock"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Ende</label>
                            <div class="input-group date" id="datepicker6" data-target-input="nearest">
                                <input id="to" name="to" type="text" class="form-control datetimepicker-input" data-target="#datepicker6" required/>
                                <div class="input-group-append" data-target="#datepicker6" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-clock"></i></div>
                                </div>
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

    <div class="modal fade" id="editSlot"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Pause bearbeiten
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="/verwaltung/schuljahre/schuljahr/{{ $schuljahr->id }}/editTimeslot">
                    @csrf
                    <input id="timeslot_id" name="id" type="hidden" required>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Text für Stundenplan</label>
                            <input id="displayText_edit" name="displayText" type="text" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Beginn</label>
                            <div class="input-group date" id="datepicker12" data-target-input="nearest">
                                <input id="from_edit" name="from" type="text" class="form-control datetimepicker-input" data-target="#datepicker12" required/>
                                <div class="input-group-append" data-target="#datepicker12" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-clock"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Ende</label>
                            <div class="input-group date" id="datepicker13" data-target-input="nearest">
                                <input id="to_edit" name="to" type="text" class="form-control datetimepicker-input" data-target="#datepicker13" required/>
                                <div class="input-group-append" data-target="#datepicker13" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-clock"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                        <button type="submit" class="btn btn-primary">Speichern</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* Style the tab */
        .tab {
            overflow: hidden;
            border: 1px solid #ccc;
            border-radius: 0.25rem 0.25rem 0rem 0rem;
            background-color: #f1f1f1;
        }

        /* Style the buttons inside the tab */
        .tab button {
            background-color: inherit;
            float: left;
            border: none;
            outline: none;
            cursor: pointer;
            padding: 14px 16px;
            transition: 0.3s;

        }

        /* Change background color of buttons on hover */
        .tab button:hover {
            background-color: #ddd;
        }

        /* Create an active/current tablink class */
        .tab button.active {
            background-color: #ccc;
        }

        /* Style the tab content */
        .tabcontent {
            display: none;
            padding: 12px;
        }
    </style>

    <script>
        function openTab(evt, cityName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(cityName).style.display = "block";
            evt.target.className += " active";

            //console.log(evt.target.id);
            localStorage.setItem('activeTab_schuljahr', evt.target.id);
        }

        // Get the element with id="defaultOpen" and click on it
        if(localStorage.getItem('activeTab_schuljahr') != null){
            document.getElementById(localStorage.getItem('activeTab_schuljahr')).click();
        }
        else{
            document.getElementById("link1").click();
        }

        function editSlot(id, begin, end, displayText, color) {
            $('#timeslot_id').val(id);
            $('#displayText_edit').val(displayText);
            $('#from_edit').val(begin);
            $('#to_edit').val(end);
            $('#editSlot').modal('show');

        }
    </script>

@endsection

