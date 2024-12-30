@extends('verwaltung.main')

@section('siteContent')
    <h3>{{'Bearbeite ' . $kontakt->name .'\'s Profil'}} ({{ $kontakt->typeDisplay }})</h3>
    @if (session()->has('status'))
        <div class="alert alert-success">
            {{ session()->get('status') }}
        </div>
    @endif
    <div class="tab">
        <button class="tablinks" onclick="openTab(event, 'allg')" id="link1">Allgemein</button>
        <button class="tablinks" onclick="openTab(event, 'pers')" id="link2">Persönliches</button>
        <button class="tablinks" onclick="openTab(event, 'dokumente')" id="link6">Dokumente</button>
        <button class="tablinks" onclick="openTab(event, 'klassenbuch')" id="link3">@if($kontakt->type == "unternehmen") Unternehmenszugang @else Zugang @endif</button>
        <button class="tablinks" onclick="openTab(event, 'beziehungen')" id="link4">Beziehungen</button>
        @if($kontakt->type != "person")
        <button class="tablinks" onclick="openTab(event, 'kapazitaet')" id="link5">Praxis-Kapazitäten</button>
        @endif
    </div>
    <form class="form" method="POST" action="{{route('kontakt.store',$kontakt->id)}}" enctype = "multipart/form-data" style="margin-bottom: 0px;">
        @csrf
        <div id="klassenbuch" class="tabcontent">
            <h5>Zugang zum Praxisportal</h5>
            @if(\Illuminate\Support\Facades\Session::has("error"))
                <p class="alert alert-danger"> {{ \Illuminate\Support\Facades\Session::get("error") }}</p>
            @endif
            <div class="form-group">
                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="exampleCheck1" name="allowedToLogin" @if($kontakt->allowedToLogin) checked @endif>
                    <label class="form-check-label" for="exampleCheck1">Diese Person darf sich in StuPla anmelden</label>
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1">Benutzername</label>
                    <input type="text" name="username" class="form-control" id="exampleInputPassword1" value="{{ $kontakt->email }}">
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1">Passwort neu setzen</label>
                    <input type="text" class="form-control" id="exampleInputPassword1" name="password">
                </div>
            </div>


            <a href="/verwaltung/stammdaten/kontakte/{{ $kontakt->id }}/createZugang" class="btn btn-warning mt-1">@if($kontakt->type == "unternehmen") Unternehmenszugang @else Zugang @endif automatisch erstellen & Zugang zusenden</a>
            @if($cloud_user->hasPermissionTo('verwaltung.kontakte.edit'))
                <button type="submit" class="btn btn-success mt-1 ">Speichern</button>
            @endif
            @if($kontakt->type == "unternehmen")

                <h5 class="mt-3">Mitarbeiterzugänge zum Praxisportal</h5>
                <table id="table_id" class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>Mitarbeiter</th>
                        <th>E-Mail</th>
                        <th>Zugang aktiv?</th>
                        <th>Aktion</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($beziehungen as $beziehung)
                        @if($beziehung->type == "arbeitet_bei")
                        <tr>
                            <td><a href="/verwaltung/stammdaten/kontakte/{{ $beziehung->kontakt1 }}/edit">{{ $beziehung->kontakt1name }}</a></td>
                            <td>{{ \App\Kontakt::find($beziehung->kontakt1)->email }}</td>
                            <td>
                                {{ \App\Kontakt::find($beziehung->kontakt1)->allowedToLogin == 0 ? "Nein" : "Ja" }}
                            </td>
                            <td>
                                <a href="/verwaltung/stammdaten/kontakte/{{ $beziehung->kontakt1 }}/createZugang" class="btn btn-xs btn-warning">
                                    Zugang zurücksetzen
                                </a>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>




        <div id="allg" class="tabcontent">
            <h5>Allgemeine Daten</h5>
            <div class="form-group">
                <label>Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{$kontakt->name}}">
                <div class="form-group">
                    <label for="inputTyp">Kontakt-Typ</label>
                    <select id="inputTyp" name="type" class="select2" readonly disabled>
                        <option value="person" @if($kontakt->type==="person") selected @endif>Person</option>
                        <option value="unternehmen"@if($kontakt->type==="unternehmen") selected @endif>Unternehmen</option>
                        <option value="niederlassung"@if($kontakt->type==="niederlassung") selected @endif>Niederlassung</option>
                    </select>
                </div>
                <label>Schule</label>
                <select class="select2" name="school[]" multiple="multiple">
                    @foreach($schulen as $schule)
                        <option value="{{$schule->id}}" @if($kontakt->schulen->contains($schule->id)) selected @endif>{{$schule->name}}</option>
                    @endforeach
                </select>
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

            <h5>Kontaktdaten</h5>
            <div class="form-group">
                <label>Telefon, geschäftlich</label>
                <input type="text" class="form-control" name="tel_business" value="{{$addInfo->tel_business}}">
                <label>Telefon, privat</label>
                <input type="text" class="form-control" name="tel_private" value="{{$addInfo->tel_private}}">
                <label>Telefon, sonstige</label>
                <input type="text" class="form-control" name="tel_other" value="{{$addInfo->tel_other}}">
                <label>Handy, Mobil</label>
                <input type="text" class="form-control" name="mobile" value="{{$addInfo->mobile}}">
                <label>Fax</label>
                <input type="text" class="form-control" name="fax" value="{{$addInfo->fax}}">
                <label>Email, geschäftlich</label>
                <input type="email" class="form-control" name="email" value="{{$addInfo->email}}">
                <label>Email, privat</label>
                <input type="email" class="form-control" name="email_private" value="{{$addInfo->email_private}}">
                <label>Email, sonstige</label>
                <input type="email" class="form-control" name="email_other" value="{{$addInfo->email_other}}">
                <label>Homepage</label>
                <input type="text" class="form-control" name="homepage" value="{{$addInfo->homepage}}">
            </div>

            @if($cloud_user->hasPermissionTo('verwaltung.kontakte.edit'))
                <button type="submit" class="btn btn-success mt-1 ">Speichern</button>
            @endif
        </div>

        <div id="pers" class="tabcontent">

            @if($kontakt->type === "person")
                <h5>Persönliche Daten</h5>
                <div class="form-group">
                    <label>Geburtsdatum</label>
                    <input type="date" class="form-control" name="birthdate" value="{{$addInfo->birthdate}}">
                    <label>Geburtsort</label>
                    <input type="text" class="form-control" name="birthplace" value="{{$addInfo->birthplace}}">
                    <label>Geburtsname</label>
                    <input type="text" class="form-control" name="birthname" value="{{$addInfo->birthname}}">
                    <label>Geschlecht</label>
                    <select class="form-control" id="anrede" name="gender">
                        <option value="male" @if($addInfo->gender == "male")selected="selected"@endif>männlich</option>
                        <option value="female" @if($addInfo->gender == "female")selected="selected"@endif>weiblich</option>
                        <option value="na" @if($addInfo->gender == "na")selected="selected"@endif>keine Angabe</option>
                    </select>
                    <label>Konfession</label>
                    <input type="text" class="form-control" name="religion" value="{{$addInfo->religion}}">
                    <label>Familienstand</label>
                    <select class="form-control" name="familienstand">
                        <option value="ledig" @if($addInfo->familienstand == "ledig")selected="selected"@endif>ledig</option>
                        <option value="verheiratet" @if($addInfo->familienstand == "verheiratet")selected="selected"@endif>verheiratet</option>
                        <option value="geschieden" @if($addInfo->familienstand == "geschieden")selected="selected"@endif>geschieden</option>
                        <option value="verwitwet" @if($addInfo->familienstand == "verwitwet")selected="selected"@endif>verwitwet</option>
                    </select>
                    <label>Schulabschluss</label>
                    <input type="text" class="form-control" name="schulabschluss" value="{{$addInfo->schulabschluss}}">
                    <label>Bundesland</label>
                    <input type="text" class="form-control" name="bundesland" value="{{$addInfo->bundesland}}">

                </div>
            @endif
            <h5>Bank Informationen</h5>
            <div class="form-group">
                <label>Bankleitzahl</label>
                <input type="text" class="form-control" name="blz" value="{{$addInfo->blz}}">
                <label>Kreditinstitut</label>
                <input type="text" class="form-control" name="bank" value="{{$addInfo->bank}}">
                <label>Kontonummer</label>
                <input type="text" class="form-control" name="kontonummer" value="{{$addInfo->kontonummer}}">
                <label>IBAN</label>
                <input type="text" class="form-control" name="iban" value="{{$addInfo->iban}}">
                <label>BIC</label>
                <input type="text" class="form-control" name="bic" value="{{$addInfo->bic}}">
            </div>


                @if($cloud_user->hasPermissionTo('verwaltung.kontakte.edit'))
                    <button type="submit" class="btn btn-success mt-1 ">Speichern</button>
                @endif
        </div>

    </form>

        <div id="dokumente" class="tabcontent">

            <form class="form" method="POST" action="{{route('kontakt.storeFormular',$kontakt->id)}}" enctype = "multipart/form-data">
                @csrf
            <div class="form-group row">
                <label for="staticEmail" class="col-sm-2 col-form-label">Verfügbare Formulare</label>
                <div class="col-sm-8">
                    @php $unterricht_formular = explode(",",$kontakt->getMerkmal("formulare", "")); @endphp
                    <select name="revisions[]" class="form-control select2" multiple>
                        @foreach($schule->formulare as $formular)
                                <option value="{{ $formular->id }}" @if($unterricht_formular != null && in_array($formular->id, $unterricht_formular)) selected @endif>{{ $formular->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mb-2">Speichern</button>
            </div>
            </form>
            @component('documents.list',[ "model" => $kontakt, "type" => "kontakt", "text" => "Hier gibt es die Möglichkeit, Dokumente dem Kontakt zur Verfügung zu stellen.", "collapse"=>false])
            @endcomponent
        </div>

    <div id="beziehungen" class="tabcontent">
        <h5>Beziehungen</h5>
        <div style="margin-bottom: 3px;">
            <div class="float-right">
                @if($cloud_user->hasPermissionTo('verwaltung.kontakte.beziehung.edit'))
                    <a href="#" data-toggle="modal" data-target="#addBeziehung" class="btn btn-primary">Beziehung hinzufügen</a>
                @endif
            </div>
            <div class="clearfix"></div>
        </div>
        <table id="table_id" class="data-table table table-striped table-bordered">
            <thead>
            <tr>
                <th>Kontakt 1</th>
                <th>Typ</th>
                <th>Kontakt 2</th>
                <th>Bemerkung</th>
                <th>Aktion</th>
            </tr>
            </thead>
            <tbody>
            @foreach($beziehungen as $beziehung)
                <tr>
                    <td><a href="/verwaltung/stammdaten/kontakte/{{ $beziehung->kontakt1 }}/edit">{{ $beziehung->kontakt1name }}</a></td>
                    <td>@if($beziehung->type == "arbeitet_bei")arbeitet bei
                        @elseif($beziehung->type == "tochter")tochter von
                        @elseif($beziehung->type == "kooperiert")kooperiert mit
                        @elseif($beziehung->type == "teil_von")teil von
                        @else Sonstige
                        @endif
                    </td>
                    <td><a href="/verwaltung/stammdaten/kontakte/{{ $beziehung->kontakt2 }}/edit">{{ $beziehung->kontakt2name }}<a/></td>
                    <td>{{ $beziehung->bemerkung }}</td>
                    <td>
                        <a href="/verwaltung/stammdaten/kontakte/{{ $kontakt->id }}/deleteBeziehung/{{$beziehung->id}}" class="btn btn-xs btn-danger">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div id="kapazitaet" class="tabcontent">
        <h5>Praxis-Kapazitäten</h5>
        <div style="margin-bottom: 3px;">
            <div class="float-right">
                @if($cloud_user->hasPermissionTo('verwaltung.kontakte.praxiskapazitaet.edit'))
                    <a href="#" data-toggle="modal" data-target="#addKapazitaet" class="btn btn-primary">Kapazität hinzufügen</a>
                @endif
            </div>
            <div class="clearfix"></div>
        </div>
        <table id="table_id" class="data-table table table-striped table-bordered">
            <thead>
            <tr>
                <th>Datum (von)</th>
                <th>Datum (bis)</th>
                <th>Modul</th>
                <th>Plätze</th>
                <th>Aktion</th>
            </tr>
            </thead>
            <tbody>
            @foreach($kontakt->praxisKapazitaeten as $kapazitaet)
                <tr>
                    <td>{{ $kapazitaet->start }}</td>
                    <td>{{ $kapazitaet->end }}</td>
                    <td>{{ $kapazitaet->lehrplanEinheit->name }}</td>
                    <td>{{ $kapazitaet->plaetze }}</td>
                    <td>
                        <a href="/verwaltung/stammdaten/kontakte/{{ $kontakt->id }}/deleteKapazitaet/{{$kapazitaet->id}}" class="btn btn-xs btn-danger">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="addBeziehung" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Kontaktbeziehung erstellen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form form-horizontal" method="POST" action="{{route('kontaktbeziehung.create',$kontakt->id)}}" enctype = "multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <label>Kontakt 1</label>
                        <select class="select2" name="beziehung_kontakt1">
                            @foreach($kontakte as $kont)
                                <option value="{{$kont->id}}">{{$kont->name}}</option>
                            @endforeach
                        </select>
                        <label>Typ</label>
                        <select class="select2" name="beziehung_typ">
                            <option value="arbeitet_bei">arbeitet bei</option>
                            <option value="tochter">tochter von</option>
                            <option value="arbeitet_bei">arbeitet bei</option>
                            <option value="kooperiert">kooperiert mit</option>
                            <option value="teil_von">teil von</option>
                            <option value="other">sonstiges</option>
                        </select>
                        <label>Kontakt 2</label>
                        <select class="select2" name="beziehung_kontakt2">
                            @foreach($kontakte as $kont)
                                <option value="{{$kont->id}}">{{$kont->name}}</option>
                            @endforeach
                        </select>

                        <label>Bemerkung</label>
                        <textarea class="form-control" name="bemerkung"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Speichern</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addKapazitaet" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Praxiskapazität hinzufügen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form form-horizontal" method="POST" action="{{route('praxiskapazitaet.create',$kontakt->id)}}" enctype = "multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Datum (von)</label>
                            <div class="input-group date" id="datepicker1" data-target-input="nearest">
                                <input id="from_edit" name="from" type="text" class="form-control datetimepicker-input" data-target="#datepicker1" required/>
                                <div class="input-group-append" data-target="#datepicker1" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Datum (bis)</label>
                            <div class="input-group date" id="datepicker2" data-target-input="nearest">
                                <input id="to_edit" name="to" type="text" class="form-control datetimepicker-input" data-target="#datepicker2" required/>
                                <div class="input-group-append" data-target="#datepicker2" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Modul (als Anrechnung)</label>
                            <select name="modul" class="select2 ">
                                @foreach($global_school->studiengange as $studiengang)
                                    <optgroup label="{{ $studiengang->name }}">
                                        @foreach($studiengang->module as $module)
                                             <option value="{{ $module->id }}">{{ $module->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Plätze</label>
                            <input id="plaetze" name="plaetze" type="number" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Erstellen</button>
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
            localStorage.setItem('activeTab_kontakt', evt.target.id);
        }

        // Get the element with id="defaultOpen" and click on it
        if(localStorage.getItem('activeTab_kontakt') != null){
            document.getElementById(localStorage.getItem('activeTab_kontakt')).click();
        }
        else{
            document.getElementById("link1").click();
        }
    </script>

@endsection
