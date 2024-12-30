@extends('verwaltung.main')

@section('siteContent')
    <style>
        .tab-pane {
            padding-top: 5px;
        }
    </style>
    <h3>{{ \App\Providers\AppServiceProvider::schoolTranslation('lehrplan', 'Lehrplan') }}: {{ $lehrplan->name }}</h3>
            <div class="tab">
                <button class="tablinks" onclick="openTab(event, 'allg')" id="link1">Allgemein</button>
                <button class="tablinks" onclick="openTab(event, 'pers')" id="link2">{{ \App\Providers\AppServiceProvider::schoolTranslation('module', 'Module') }}</button>
                <button class="tablinks" onclick="openTab(event, 'groups')" id="link2323">Profile</button>
                <button class="tablinks" onclick="openTab(event, 'dokumente')" id="link3">Dokumente</button>
            </div>
            <!-- Tab panes -->
            <div id="allg" class="tabcontent">
                <h5>Allgemeine Daten</h5>
                    <form method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="exampleInputEmail1">Name</label>
                            <input type="text" name="name" class="form-control" id="exampleInputEmail1" placeholder="Name des Lehrplans" value="{{ $lehrplan->name }}" required>
                        </div>
                        <div class="form-group">
                            <label for="dauer_UE">Anzahl in Minuten für eine UE</label>
                            <input type="number" name="dauer_UE" class="form-control" id="dauer_UE" placeholder="Minutenanzahl einer UE" value="{{ $lehrplan->dauer_UE }}" required>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Klassen, die nach dem Curriculum unterrichtet werden sollen:</label>
                            <select name="klassen[]" id="personDropDown" class="select2" multiple>
                                @foreach($klassen as $klasse)
                                    <option value="{{ $klasse->id }}" @if(in_array($klasse->id, $class_ids)) selected @endif>{{ $klasse->displayName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Speichern</button>
                    </form>

            </div>
            <div id="pers" class="tabcontent">
                    <h5>{{ \App\Providers\AppServiceProvider::schoolTranslation('module', 'Module') }}</h5>
                    <div style="margin-bottom: 3px;">
                        <div class="float-right">
                                <a href="#" data-toggle="modal" data-target="#exampleModal" class="btn btn-primary">Modul hinzufügen</a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <table class="tree table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Typ</th>
                            <th>Credits / Wertungsfaktor</th>
                            <th>davon anerkannt</th>
                            <th>SOLL (UE)</th>
                            <th>{{ \App\Providers\AppServiceProvider::schoolTranslation('facher', 'Fächer') }}</th>
                            <th>Profil</th>
                            <th>Aktion</th>
                        </tr>
                        </thead>
                        <tbody>
                        @component('verwaltung.lehrplan.childern_list',[ "einheiten" => $lehrplan->lehreinheiten("12"), "lehrplan" => $lehrplan])
                        @endcomponent
                        </tbody>
                    </table>
                    <div class="modal fade" id="exampleModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">{{ \App\Providers\AppServiceProvider::schoolTranslation('modul', 'Modul') }} hinzufügen</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form class="form" method="post" action="{{route('modul.create',$lehrplan->id)}}">
                                    {{ csrf_field() }}
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input type="text" class="form-control" id="name" name="name" required>
                                            <label>Anzahl an notwendiger Unterrichtseinheiten / Stunden</label>
                                            <input type="number" class="form-control" id="anzahl" name="anzahl" value="0" required disabled="true">
                                        </div>

                                        <div class="form-group">
                                            <label>Fach</label>
                                            <select class="select2" name="fach_id" id="fach_id" required onchange="fachAuswahl()">
                                                    <option value="-1" selected>Kein Fach / Überbegriff </option>
                                                @foreach($facher as $fach)
                                                    <option value="{{$fach->id}}">{{$fach->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <div class="form-check-inline">
                                                <label class="form-check-label mr-2" for="newSubject">Neues Fach entsprechend dem Modulnamen anlegen?</label>
                                                <input type="checkbox" class="form-check-input" id="newSubject" name="newSubject" onclick="disableFach()">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Erstellen</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

    <div id="dokumente" class="tabcontent">
                    @component('documents.list',[ "model" => $lehrplan, "type" => "curriculum", "text" => "Hier gibt es die Möglichkeit, Dokumente an den Lehrplan zu hängen.", "collapse"=>false])
                    @endcomponent
                </div>

    <div id="groups" class="tabcontent">
        <h5>Gruppen / Fächerschwerpunkte / Wahlmodule</h5>
        <p>Hier gibt es die Möglichkeit Gruppen zu erstellen, die dann den Schüler zugeordnet werden.</p>
        <div style="margin-bottom: 3px;">
            <div class="float-right">
                <a href="#" data-toggle="modal" data-target="#excelImportModal" class="btn btn-primary">Wahlmodule anlegen</a>
            </div>
            <div class="clearfix"></div>
        </div>
        <table id="table_id" class="data-table table table-striped table-bordered">
            <thead>
            <tr>
                <th>Name</th>
                <th>Farbe</th>
                <th>Aktion</th>
            </tr>
            </thead>
            <tbody>
            @foreach($lehrplan->groups as $group)
                <tr>
                    <td>{{ $group->name }}</td>
                    <td><div style="width: 20px; height: 20px; background-color: {{ $group->color }}"></div></td>
                    <td>
                        <a href="#" onclick="editWahlModul('{{ $group->id }}','{{ $group->name }}','{{ $group->color }}')" class="btn btn-xs btn-primary"><i class="fas fa-edit"></i></a>
                        <a href="/verwaltung/lehrplan/1/group/{{ $group->id }}/delete" class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
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
            border: 1px solid #ccc;
            border-radius: 0rem 0rem 0.25rem 0.25rem;
            border-top: none;
            background-color: #fff;
        }
    </style>
    <script>
       function disableFach(){
           var checkBox = document.getElementById("newSubject");
           if (checkBox.checked == true){
               document.getElementById("fach_id").disabled = true;
           } else {
               document.getElementById("fach_id").disabled = false;

           }
       }
       function fachAuswahl() {
           var fach_id = $('#fach_id').val();

           $('#anzahl').attr("disabled", false);
           if(fach_id == "-1")
           {
               $('#anzahl').val(0);
               $('#anzahl').attr("disabled", true);
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
           localStorage.setItem('activeTab_lehrplan', evt.target.id);
       }

       // Get the element with id="defaultOpen" and click on it
       if(localStorage.getItem('activeTab_lehrplan') != null){
           document.getElementById(localStorage.getItem('activeTab_lehrplan')).click();
           //localStorage.removeItem('activeTab');
       }
       else{
           document.getElementById("link1").click();
       }

       function editWahlModul(id, name, color)
       {
           $('#wahlModulEdit').modal("show");
           $('#grouplehrplan_color').val(color);
           $('#grouplehrplan_id').val(id);
           $('#grouplehrplan_name').val(name);
           $('#colorpicker-bl').colorpicker('setValue', color);
       }
    </script>

    <div class="modal fade" id="excelImportModal"  role="dialog" aria-labelledby="excelImportModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="excelImportModalLabel">Wahlmodul anlegen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form" method="post" action="{{route('groupLehrplan.create', $lehrplan->id)}}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Name *</label>
                            <input type="text" class="form-control" id="name" name="name" value="Neues Wahlmodul" required>
                        </div>
                        <div class="form-group">
                            <label>Farbe</label>
                            <div class="input-group  colorpicker-bl" title="Using input value">
                                <input type="text" name="color" class="form-control input-lg" value="#82968c"/>
                                <span class="input-group-append">
    <span class="input-group-text colorpicker-input-addon"><i></i></span>
  </span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Anlegen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="wahlModulEdit"  role="dialog" aria-labelledby="excelImportModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="excelImportModalLabel">Wahlmodul bearbeiten</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form" method="post" action="{{route('groupLehrplan.update', $lehrplan->id)}}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <input type="hidden" class="form-control" id="grouplehrplan_id" name="id">
                        <div class="form-group">
                            <label>Name *</label>
                            <input type="text" class="form-control" id="grouplehrplan_name" name="name" value="Neues Wahlmodul" required>
                        </div>
                        <div class="form-group">
                            <label>Farbe</label>
                            <div class="input-group  colorpicker-bl"  id="colorpicker-bl" title="Using input value">
                                <input id="grouplehrplan_color" type="text" name="color" class="form-control input-lg" value="#82968c"/>
                                <span class="input-group-append">
    <span class="input-group-text colorpicker-input-addon"><i></i></span>
  </span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Speichern</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
