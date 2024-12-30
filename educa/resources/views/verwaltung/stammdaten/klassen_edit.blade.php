@extends('verwaltung.main')

@section('siteContent')
    <h3>{{'Bearbeite '. $school->name . ' '.\App\Providers\AppServiceProvider::schoolTranslation('klassen', 'Klassen').' '. $klasse->name}}</h3>

            @if($errors->any())
                <div class="alert alert-danger">
                    {{$errors->first()}}
                </div>
            @endif
            @if (session()->has('status'))
                <div class="alert alert-success">
                    {{ session()->get('status') }}
                </div>
            @endif
            <div class="tab">
                <button class="tablinks" onclick="openTab(event, 'allg')" id="link1">Allgemein</button>
                <button class="tablinks" onclick="openTab(event, 'schuler')" id="link2">{{ \App\Providers\AppServiceProvider::schoolTranslation('schuler', 'Schüler') }}</button>
                <button class="tablinks" onclick="openTab(event, 'schuler_ehe')" id="link4">Ausgeschiedene {{ \App\Providers\AppServiceProvider::schoolTranslation('schuler', 'Schüler') }}</button>
                <button class="tablinks" onclick="openTab(event, 'praxis')" id="link3">Praxis</button>
            </div>

    <div id="praxis" class="tabcontent">
        <form class="form" method="POST" action="{{route('klasse.storeFormular',$klasse->id)}}" enctype = "multipart/form-data">
            @csrf

            <div class="form-group row">
                <label for="staticEmail" class="col-sm-2 col-form-label">Verfügbare Formulare</label>
                <div class="col-sm-8">
                    @php $unterricht_formular = explode(",",$klasse->getMerkmal("formulare", "")); @endphp
                    <select name="revisions[]" class="form-control select2" multiple>
                        @foreach($klasse->schuljahr->schule->formulare as $formular)
                            <option value="{{ $formular->id }}" @if($unterricht_formular != null && in_array($formular->id, $unterricht_formular)) selected @endif>{{ $formular->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mb-2">Speichern</button>
        </form>
    </div>

                <div id="allg" class="tabcontent">
                    <form id ="klasseForm" class="form" method="POST" action="{{route('klasse.store',$klasse->id)}}" enctype = "multipart/form-data">
                        {{ csrf_field() }}
                        <h5>Allgemeine Daten</h5>
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{$klasse->name}}">
                            <label>Schüleranzahl</label>
                            <input type="text" class="form-control" id="anzahl" name="anzahl" value="{{count($klasse->schuler)}}" disabled readonly>
                            <label>Schule</label>
                            <input type="text" class="form-control" id="schoolname" name="schoolname" value="{{$school->name}}" disabled readonly>
                            <div class="row">
                            <div class="col-6">
                            <label>Von</label>
                            <div class="input-group date" id="datepicker1" data-target-input="nearest">
                                <input value="@isset($klasse->bis){{ date_format(new DateTime($klasse->von),'d.m.Y')}}@endisset" id="inputDate" name="von" type="text" class="form-control datetimepicker-input" data-target="#datepicker1"/>
                                <div class="input-group-append" data-target="#datepicker1" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                            </div>
                            <div class="col-6">
                            <label>Bis</label>
                            <div class="input-group date" id="datepicker2" data-target-input="nearest">
                                <input value="@isset($klasse->bis){{ date_format(new DateTime($klasse->bis),'d.m.Y')}}@endisset" id="inputDate2" name="bis" type="text" class="form-control datetimepicker-input" data-target="#datepicker2"/>
                                <div class="input-group-append" data-target="#datepicker2" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                            </div>
                            </div>
                            <label>Primärer Raum</label>
                            <select class="select2" name="klassenraum">
                                <option selected></option>
                                @foreach($raume as $raum)
                                    @if($klasse->klassenraum != null && $klasse->klassenraum->id == $raum->id)
                                        <option value="{{$raum->id}}" selected>{{$raum->name}}</option>
                                    @else
                                        <option value="{{$raum->id}}">{{$raum->name}}</option>
                                    @endif

                                @endforeach
                            </select>
                            <label>Lehrplan</label>
                            <select class="select2" name="lehrplan[]" multiple="multiple">
                                @foreach($school->lehrplane as $lehrplan)
                                    @if($klasse->getLehrplan->contains($lehrplan->id))
                                        <option value="{{$lehrplan->id}}" selected>{{$lehrplan->name}}</option>
                                    @else
                                        <option value="{{$lehrplan->id}}">{{$lehrplan->name}}</option>
                                    @endif

                                @endforeach
                            </select>
                            <label>Beschreibung</label>
                            <textarea  class="form-control" name="beschreibung"> {{$klasse->beschreibung}}</textarea>
                        </div>
                        <button type="submit" class="btn btn-success mt-1">Speichern</button>
                        <div class="float-right">
                            <a href="#" data-toggle="modal" data-target="#moveYear" class="btn btn-danger">Schuljahr ändern</a>
                        </div>
                    </form>
                </div>

            <div id="schuler" class="tabcontent">
                <div style="margin-bottom: 3px;">
                    <div class="float-right">
                        <a href="#" data-toggle="modal" data-target="#addSchuler" class="btn btn-primary">Schüler hinzufügen</a>

                    </div>
                    <div class="clearfix"></div>
                </div>
                <table id="table_id" class="data-table table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>Nachname</th>
                        <th>Vorname</th>
                        <th>von</th>
                        <th>bis</th>
                        <th>Profile</th>
                        <th>Bemerkung</th>
                        <th>Aktion</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($schulers as $schuler)
                        @if(\App\Schuler::find($schuler->id) != null && ($schuler->from == null || strtotime($schuler->from) < strtotime("now")) && ($schuler->until == null || strtotime($schuler->until) > strtotime("now")))
                        <tr>
                            <td>{{ $schuler->lastname }}</td>
                            <td>{{ $schuler->firstname }}</td>
                            @if($schuler->from == null)
                                <td><i>Keine Begrenzung</i></td>
                            @else
                            <td>{{ date("d.m.Y",strtotime($schuler->from)) }}</td>
                            @endif


                            @if($schuler->until == null)
                                <td><i>Keine Begrenzung</i></td>
                            @else
                            <td>{{ date("d.m.Y",strtotime($schuler->until)) }}</td>
                            @endif
                            <td>
                                @foreach(\App\Schuler::find($schuler->id)->getLehrplanGroups($klasse->getLehrplan->pluck("id")) as $lehrPlanGroup)
                                    <span class="badge badge-pill badge-primary" style="background-color: {{ $lehrPlanGroup->color }}">{{ $lehrPlanGroup->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                {{ $schuler->note }}
                            </td>
                            <td>
                                <a onclick="editSchuler('{{ $schuler->id }}', @if($schuler->from == null) '' @else '{{ date("d.m.Y",strtotime($schuler->from)) }}' @endif , @if($schuler->until == null) '' @else '{{ date("d.m.Y",strtotime($schuler->until)) }}' @endif, `{{ str_replace("/\r|\n/","",$schuler->note) }}`, '{{ implode(",",\App\Schuler::find($schuler->id)->getLehrplanGroups($klasse->getLehrplan->pluck("id"))->pluck("id")->toArray()) }}' )" href="#" class="btn btn-xs btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="/verwaltung/stammdaten/klassen/{{ $klasse->id }}/ausscheidenSchuler/{{$schuler->id}}" class="btn btn-xs btn-warning">
                                    <i class="fas fa-user-slash"></i>
                                </a>
                                <a href="/verwaltung/stammdaten/klassen/{{ $klasse->id }}/deleteSchuler/{{$schuler->id}}" class="btn btn-xs btn-danger">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>


            </div>


    <div id="schuler_ehe" class="tabcontent">

        <table id="table_id" class="data-table table table-striped table-bordered">
            <thead>
            <tr>
                <th>Nachname</th>
                <th>Vorname</th>
                <th>von</th>
                <th>bis</th>
                <th>Bemerkung</th>
                <th>Aktion</th>
            </tr>
            </thead>
            <tbody>
            @foreach($schulers as $schuler)
                @if((\App\Schuler::find($schuler->id) != null && ($schuler->from != null && $schuler->until != null ) && (strtotime($schuler->until) < strtotime("now") || strtotime($schuler->from) > strtotime("now"))))
                    <tr>
                        <td>{{ $schuler->lastname }}</td>
                        <td>{{ $schuler->firstname }}</td>
                        @if($schuler->from == null)
                            <td><i>Keine Begrenzung</i></td>
                        @else
                            <td>{{ date("d.m.Y",strtotime($schuler->from)) }}</td>
                        @endif


                        @if($schuler->until == null)
                            <td><i>Keine Begrenzung</i></td>
                        @else
                            <td>{{ date("d.m.Y",strtotime($schuler->until)) }}</td>
                        @endif

                        <td>
                            {{ $schuler->note }}
                        </td>
                        <td>
                            <a onclick="editSchuler('{{ $schuler->id }}',        @if($schuler->from == null)  '' @else '{{ date("d.m.Y",strtotime($schuler->from)) }}' @endif , @if($schuler->until == null) '' @else '{{ date("d.m.Y",strtotime($schuler->until)) }}' @endif , `{{ str_replace("/\r|\n/","",$schuler->note) }}`, null )" href="#" class="btn btn-xs btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="/verwaltung/stammdaten/klassen/{{ $klasse->id }}/deleteSchuler/{{$schuler->id}}" class="btn btn-xs btn-danger">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>


    <div class="modal fade" id="addSchuler" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ \App\Providers\AppServiceProvider::schoolTranslation('klassen', 'Klassen') }}zugehörigkeit bearbeiten</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form form-horizontal" method="POST" action="{{route('klasse_schuler.create',$klasse->id)}}" enctype = "multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <label>Name</label>
                        <select class="select2" name="schuler_add[]" id="schulerBox" multiple required>
                            @foreach($school->schuler()->orderBy('lastname')->orderBy('firstname')->get() as $schuler)
                                <option value="{{$schuler->id}}">{{"$schuler->lastname, $schuler->firstname "}}</option>
                            @endforeach
                        </select>
                        <label>von</label>
                        <div class="input-group date" id="datepicker3" data-target-input="nearest">
                            <input id="from" name="from" type="text" class="form-control datetimepicker-input" data-target="#datepicker3"/>
                            <div class="input-group-append" data-target="#datepicker3" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                        <label>bis</label>
                        <div class="input-group date" id="datepicker4" data-target-input="nearest">
                            <input id="until" name="until" type="text" class="form-control datetimepicker-input" data-target="#datepicker4"/>
                            <div class="input-group-append" data-target="#datepicker4" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                        <label>Profile</label>
                        <select class="select2" name="wahlBox[]" id="wahlBox" multiple>
                            @foreach($klasse->getLehrplan as $lehrplan)
                                @foreach($lehrplan->groups as $group)
                                <option value="{{$group->id}}">{{ $group->name }}</option>
                                @endforeach
                            @endforeach
                        </select>
                        <label>Bemerkung</label>
                        <div class="input-group date" id="datepicker4" data-target-input="nearest">
                            <textarea id="note" name="note" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Speichern</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="moveYear" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Schuljahr ändern</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form form-horizontal" method="POST" action="{{route('klasse.move',$klasse->id)}}" enctype = "multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-danger" role="alert">
                            Achtung: Dies kann zu Problemen führen, wenn zu dieser Klasse im bisherigen Schuljahr bereits Informationen, wie z.B. Klassenbucheinträge angelegt wurden.
                        </div>
                        <label>Schuljahr</label>
                        <select class="select2" id="schuljahr" name="schuljahr">
                            @foreach($school->schuljahre()->get() as $schuljahr)
                                @if($klasse->schuljahr_id == $schuljahr->id)
                                    <option value="{{$schuljahr->id}}" selected>{{$schuljahr->name}}</option>
                                @else
                                    <option value="{{$schuljahr->id}}">{{$schuljahr->name}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
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
            padding: 6px 12px;
            border: 1px solid #ccc;
            border-radius: 0rem 0rem 0.25rem 0.25rem;
            border-top: none;
            background-color: #fff;
        }
    </style>
@endsection

@section('additionalScript')
    <script>
        function editSchuler(idOfSchuler, start, end, note, idOfLehrplanGroups)
        {
            $('#addSchuler').modal('show');
            $('#schulerBox').val(idOfSchuler).trigger("change");
            $('#from').val(start);
            $('#until').val(end);
            $('#note').val(note);
            $('#wahlBox').val([]).trigger("change");
            if(idOfLehrplanGroups) {
                $('#wahlBox').val(idOfLehrplanGroups.split(",")).trigger("change");
            }
        }

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
            localStorage.setItem('activeTab_klassen', evt.target.id);
        }

        // Get the element with id="defaultOpen" and click on it
        if(localStorage.getItem('activeTab_klassen') != null){
            document.getElementById(localStorage.getItem('activeTab_klassen')).click();
        }
        else{
            document.getElementById("link1").click();
        }
    </script>

@endsection


