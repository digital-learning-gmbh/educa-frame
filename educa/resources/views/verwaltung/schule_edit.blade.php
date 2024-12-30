@extends('verwaltung.main')

@section('siteContent')
    <h3>{{'IBA Studienort: ' . $schule->name}}</h3>
        @if (session()->has('status'))
            <div class="alert alert-success">
                {{ session()->get('status') }}
            </div>
        @endif
        <div class="tab">
            <button class="tablinks" onclick="openTab(event, 'allg')" id="link1">Allgemein</button>
       <!--     <button class="tablinks" onclick="openTab(event, 'bezeichnungen')" id="link4">Bezeichnungen</button> -->
            <button class="tablinks" onclick="openTab(event, 'raume')" id="link4">Räume</button>
            <button class="tablinks" onclick="openTab(event, 'einstellungen')" id="link6">Einstellungen</button>
         <!--   <button class="tablinks" onclick="openTab(event, 'cloud')" id="link3">StuPla Cloud</button> -->
        </div>
        <div id="allg" class="tabcontent">
            <form id ="raumForm" class="form" method="POST" action="{{route('schule.store',$schule->id)}}" enctype = "multipart/form-data">

                @csrf
            <h5>Allgemeine Daten</h5>
            <div class="form-group">
                <label>Name des IBA Studienortes</label>
                <input type="text" class="form-control" id="name" name="name" value="{{$schule->name}}">
                <label>Abkürzung des Studienortes (z.B. HH für Hamburg)</label>
                <input type="text" class="form-control" id="abk" name="abk" value="{{$schule->abk}}">
                <label>Lizenz</label>
                <input type="text" class="form-control" id="name" name="lizenz" value="{{$schule->licence}}">
            </div>
            <h5>Anschrift</h5>
            <div class="form-group">
                <label>Straße</label>
                <input type="text" class="form-control" name="street" value="{{$addInfo->street}}">
                <label>PLZ</label>
                <input type="text" class="form-control" name="plz" value="{{$addInfo->plz}}">
                <label>Ort</label>
                <input type="text" class="form-control" name="city" value="{{$addInfo->city}}">
            </div>
                <h5>Layout & Design</h5>
                <label>Akzent-Farbe</label>
                <div class="input-group  colorpicker-bl" title="Using input value">
                    <input type="text" name="accentColor" class="form-control input-lg" value="{{$schule->accentColor}}"/>
                    <span class="input-group-append">
<span class="input-group-text colorpicker-input-addon"><i></i></span>
</span>
                </div>
            <h5>Kontaktdaten</h5>
            <div class="form-group">
                <label>Telefon</label>
                <input type="text" class="form-control" name="tel" value="{{$addInfo->tel_business}}">
                <label>Handy, Mobil</label>
                <input type="text" class="form-control" name="mobile" value="{{$addInfo->mobile}}">
                <label>Fax</label>
                <input type="text" class="form-control" name="fax" value="{{$addInfo->fax}}">
                <label>Email</label>
                <input type="email" class="form-control" name="email" value="{{$addInfo->email}}">
                <label>Homepage</label>
                <input type="text" class="form-control" name="homepage" value="{{$addInfo->homepage}}">
            </div>

            <button type="submit" class="btn btn-success mt-1">Speichern</button>
            </form>
        </div>

        <div id="cloud" class="tabcontent">
            <h5>StuPla Cloud</h5>
            <h6>Bald verfügbar.</h6>
        </div>

        <div id="raume" class="tabcontent">
            @include('verwaltung.stammdaten.raume')
        </div>

        <div id="einstellungen" class="tabcontent">
            <form id ="einstellungenForm" class="form" method="POST" action="{{route('schule.einstellungen',$schule->id)}}" enctype = "multipart/form-data">
                @csrf
                <h5>Allgemeine Einstellungen</h5>
             <!--   <div class="form-group">
                    <label>Typ der Organisation. Dadurch werden optimierte Einstellungen geladen</label>

                </div> -->
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Schuljahre nutzen?</label>
                    <div class=" col-sm-10">
                    <div class="custom-control custom-checkbox">
                    <input @if($schule->getEinstellungen("schuljahre", "true") == "true") checked @endif type="checkbox" class="custom-control-input" id="customCheck1" name="schuljahre">
                    <label class="custom-control-label" for="customCheck1">Schuljahre aktivieren</label>
                    </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="staticEmail" class="col-sm-2 col-form-label">Zusätzliche Stammdatenblätter (Student*in)</label>
                    <div class="col-sm-10">
                        @php $unterricht_formular = explode(",",$schule->getEinstellungen("formulare", "")); @endphp
                        <select name="revisions[]" class="form-control select2" multiple>
                            @foreach(\StuPla\CloudSDK\formular\models\Formular::all() as $formular)
                                <option value="{{ $formular->id }}" @if($unterricht_formular != null && in_array($formular->id, $unterricht_formular)) selected @endif>{{ $formular->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="staticEmail" class="col-sm-2 col-form-label">Zusätzliche Stammdatenblätter (Dozent*in)</label>
                    <div class="col-sm-10">
                        @php $unterricht_formular = explode(",",$schule->getEinstellungen("formulare_dozent", "")); @endphp
                        <select name="revisions_dozent[]" class="form-control select2" multiple>
                            @foreach(\StuPla\CloudSDK\formular\models\Formular::all() as $formular)
                                <option value="{{ $formular->id }}" @if($unterricht_formular != null && in_array($formular->id, $unterricht_formular)) selected @endif>{{ $formular->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="staticEmail" class="col-sm-2 col-form-label">Klassen-Ämter Formular</label>
                    <div class="col-sm-10">
                        @php $klassen_formular = $schule->getEinstellungen("form_course_functions_id", ""); @endphp
                        <select name="form_course_functions_id" class="form-control select2">
                            @foreach(\StuPla\CloudSDK\formular\models\Formular::all() as $formular)
                                <option value="{{ $formular->id }}" @if($klassen_formular != null && $formular->id == $klassen_formular) selected @endif>{{ $formular->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="staticEmail" class="col-sm-2 col-form-label">Kalender (z.B. für Ferien oder Feiertage)</label>
                    <div class="col-sm-10">
                        @php $calendar_id = $schule->getEinstellungen("calendar_id", ""); @endphp
                        <select name="calendar_id" class="form-control select2">
                            <option value="" selected>Kein Kalender</option>
                            @foreach(\App\Ferienkalender::all() as $kalendar)
                                <option value="{{ $kalendar->id }}" @if($calendar_id != null && $kalendar->id == $calendar_id) selected @endif>{{ $kalendar->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="staticEmail" class="col-sm-2 col-form-label">1 UE sind wie viele min?</label>
                    <div class="col-sm-6">
                        @php $ue_min = $schule->getEinstellungen("ue_min", 60); @endphp
                        <input name="ue_min" class="form-control" value="{{ $ue_min }}" pattern="[0-9]+" >
                    </div>
                    <div class="col-sm-4">
                        <b>Minuten</b>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="staticEmail" class="col-sm-2 col-form-label">1 Tag hat wie viele UE?</label>
                    <div class="col-sm-6">
                        @php $ue_day = $schule->getEinstellungen("ue_day", 8); @endphp
                        <input name="ue_day" class="form-control" value="{{ $ue_day }}" pattern="[0-9]+([\.,][0-9]+)?" >
                    </div>
                    <div class="col-sm-4">
                        <b>UE</b>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="staticEmail" class="col-sm-2 col-form-label">Vorlage für die E-Mail Benachrichtigung (de)</label>
                    <div class="col-sm-6">
                        @php $form_template_de_id = $schule->getEinstellungen("form_template_de_id", ""); @endphp
                        <select name="form_template_de_id" class="form-control select2">
                            <option value="" selected>Keine Benachrichtigung</option>
                            @foreach(\App\FormularTemplate::all() as $templates)
                                <option value="{{ $templates->id }}" @if($form_template_de_id != null && $templates->id == $form_template_de_id) selected @endif>{{ $templates->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="staticEmail" class="col-sm-2 col-form-label">Vorlage für die E-Mail Benachrichtigung (en)</label>
                    <div class="col-sm-6">
                        @php $form_template_en_id = $schule->getEinstellungen("form_template_en_id", ""); @endphp
                        <select name="form_template_en_id" class="form-control select2">
                            <option value="" selected>Keine Benachrichtigung</option>
                            @foreach(\App\FormularTemplate::all() as $templates)
                                <option value="{{ $templates->id }}" @if($form_template_en_id != null && $templates->id == $form_template_de_id) selected @endif>{{ $templates->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                    <div class="form-group row">
                        <label for="staticEmail" class="col-sm-2 col-form-label">Beginn des Jahreskalenders</label>
                        <div class="col-sm-6">
                            <select class="select2" name="yearcalendar_start_month">
                                <option value="1" @if($schule->getEinstellungen("yearcalendar_start_month",1) == 1) selected @endif>Januar</option>
                                <option value="2" @if($schule->getEinstellungen("yearcalendar_start_month",1) == 2) selected @endif>Februar</option>
                                <option value="3" @if($schule->getEinstellungen("yearcalendar_start_month",1) == 3) selected @endif>März</option>
                                <option value="4" @if($schule->getEinstellungen("yearcalendar_start_month",1) == 4) selected @endif>April</option>
                                <option value="5" @if($schule->getEinstellungen("yearcalendar_start_month",1) == 5) selected @endif>Mai</option>
                                <option value="6" @if($schule->getEinstellungen("yearcalendar_start_month",1) == 6) selected @endif>Juni</option>
                                <option value="7" @if($schule->getEinstellungen("yearcalendar_start_month",1) == 7) selected @endif>Juli</option>
                                <option value="8" @if($schule->getEinstellungen("yearcalendar_start_month",1) == 8) selected @endif>August</option>
                                <option value="9" @if($schule->getEinstellungen("yearcalendar_start_month",1) == 9) selected @endif>September</option>
                                <option value="10" @if($schule->getEinstellungen("yearcalendar_start_month",1) == 10) selected @endif>Oktober</option>
                                <option value="11" @if($schule->getEinstellungen("yearcalendar_start_month",1) == 11) selected @endif>November</option>
                                <option value="12" @if($schule->getEinstellungen("yearcalendar_start_month",1) == 12) selected @endif>Dezember</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Jahreskalender Bearbeitung erlauben?</label>
                        <div class=" col-sm-10">
                            <div class="custom-control custom-checkbox">
                                <input @if($schule->getEinstellungen("yearplaner_blocked", false)) checked @endif type="checkbox" class="custom-control-input" id="customCheck123222" name="yearplaner_blocked">
                                <label class="custom-control-label" for="customCheck123222">Bearbeitung sperren</label>
                            </div>
                        </div>
                    </div>

                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-2 col-form-label">Vorlage für die E-Mail Benachrichtigung (de)</label>
                            <div class="col-sm-6">
                                @php $form_template_de_id = $schule->getEinstellungen("form_template_de_id", ""); @endphp
                                <select name="form_template_de_id" class="form-control select2">
                                    <option value="" selected>Keine Benachrichtigung</option>
                                    @foreach(\App\FormularTemplate::all() as $templates)
                                        <option value="{{ $templates->id }}" @if($form_template_de_id != null && $templates->id == $form_template_de_id) selected @endif>{{ $templates->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

        <div id="bezeichnungen" class="tabcontent">
            <form id ="raumForm" class="form" method="POST" action="{{route('schule.bezeichnungen.store',$schule->id)}}" >
                @csrf
            <h5>Bezeichnung / Benennung</h5>
            <table id="table_id" class="data-table table table-striped table-bordered">
            <thead>
            <tr>
                <th>Schlüssel / Bezeichnung</th>
                <th>Name</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Fächer</td>
                <td><input class="form-control" name="facher" value="{{ \App\Providers\AppServiceProvider::schoolTranslation('facher', 'Fächer') }}"></td>
            </tr>
            <tr>
                <td>Fach</td>
                <td><input class="form-control" name="fach" value="{{ \App\Providers\AppServiceProvider::schoolTranslation('fach', 'Fach') }}"></td>
            </tr>
            <tr>
                <td>Lehrplan</td>
                <td><input class="form-control" name="lehrplan" value="{{ \App\Providers\AppServiceProvider::schoolTranslation('lehrplan', 'Lehrplan') }}"></td>
            </tr>
            <tr>
                <td>Lehrpläne</td>
                <td><input class="form-control" name="lehrplane" value="{{ \App\Providers\AppServiceProvider::schoolTranslation('lehrplane', 'Lehrpläne') }}"></td>
            </tr>
            <tr>
                <td>Modul</td>
                <td><input class="form-control" name="modul" value="{{ \App\Providers\AppServiceProvider::schoolTranslation('modul', 'Modul') }}"></td>
            </tr>
            <tr>
                <td>Module</td>
                <td><input class="form-control" name="module" value="{{ \App\Providers\AppServiceProvider::schoolTranslation('module', 'Module') }}"></td>
            </tr>
            <tr>
                <td>Dozent</td>
                <td><input class="form-control" name="dozent" value="{{ \App\Providers\AppServiceProvider::schoolTranslation('dozent', 'Dozent') }}"></td>
            </tr>
            <tr>
                <td>Dozenten</td>
                <td><input class="form-control" name="dozenten" value="{{ \App\Providers\AppServiceProvider::schoolTranslation('dozenten', 'Dozenten') }}"></td>
            </tr>
            <tr>
                <td>Schüler</td>
                <td><input class="form-control" name="schuler" value="{{ \App\Providers\AppServiceProvider::schoolTranslation('schuler', 'Schüler') }}"></td>
            </tr>
            <tr>
                <td>Klasse</td>
                <td><input class="form-control" name="klasse" value="{{ \App\Providers\AppServiceProvider::schoolTranslation('klasse', 'Klasse') }}"></td>
            </tr>
            <tr>
                <td>Klassen</td>
                <td><input class="form-control" name="klassen" value="{{ \App\Providers\AppServiceProvider::schoolTranslation('klassen', 'Klassen') }}"></td>
            </tr>
            </tbody>
            </table>
                <button type="submit" class="btn btn-success mt-1">Speichern</button>
            </form>
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
            localStorage.setItem('activeTab_schule', evt.target.id);
        }

        // Get the element with id="defaultOpen" and click on it
        if(localStorage.getItem('activeTab_schule') != null){
            document.getElementById(localStorage.getItem('activeTab_schule')).click();
        }
        else{
            document.getElementById("link1").click();
        }
    </script>

@endsection
