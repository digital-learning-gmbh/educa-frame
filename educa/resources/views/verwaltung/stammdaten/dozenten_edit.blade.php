@extends('verwaltung.main')

@section('siteContent')
   <!-- <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/verwaltung">Verwaltung</a></li>
            <li class="breadcrumb-item" aria-current="page"><a href="/verwaltung/stammdaten">Stammdaten</a></li>
            <li class="breadcrumb-item active" aria-current="page"><a href="/verwaltung/stammdaten/dozenten">Dozenten</a></li>
        </ol>
    </nav> -->
    <h3>{{'Bearbeite das Profil von ' .$lehrer->firstname .' '. $lehrer->lastname}}</h3>
            @if (session()->has('status'))
                <div class="alert alert-success">
                    {{ session()->get('status') }}
                </div>
            @endif
            <div class="tab">
                <button class="tablinks" onclick="openTab(event, 'allg')" id="link1">Allgemein</button>
                <button class="tablinks" onclick="openTab(event, 'pers')" id="link2">Persönliches</button>
                <button class="tablinks" onclick="openTab(event, 'klassenbuch')" id="link3">Klassenbuch</button>
                <button class="tablinks" onclick="openTab(event, 'merk')" id="link4">Merkmale</button>
                <button class="tablinks" onclick="openTab(event, 'historie')" id="link6">Historie</button>
            </div>
            <form class="form" method="POST" action="{{route('dozent.store',$lehrer->id)}}" enctype = "multipart/form-data">
                @csrf
                <div id="klassenbuch" class="tabcontent">
                    <h5>Zugriff auf das digitale Klassenbuch</h5>
                    <div class="form-group">
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="exampleCheck1" name="allowedToLogin" @if($lehrer->allowedToLogin) checked @endif>
                            <label class="form-check-label" for="exampleCheck1">Dieser Dozent darf sich im digitalen Klassenbuch anmelden</label>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Benutzername</label>
                            <input type="text" name="username" class="form-control" id="exampleInputPassword1" value="{{ $lehrer->email }}">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Passwort neu setzen</label>
                            <input type="text" class="form-control" id="exampleInputPassword1" name="password">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Sicherheitsschlüssel (Token)</label>
                            <input type="text" class="form-control" id="exampleInputPassword1" readonly value="{{ $lehrer->securityToken }}">
                        </div>
                    </div>
                </div>
                <div id="allg" class="tabcontent">
                    <h5>Allgemeine Daten</h5>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-2">
                                <label>Titel</label>
                                <input type="text" class="form-control" id="title" name="title" value="{{$addInfo->title}}" onchange ="setAnrede()">
                            </div>
                            <div class="col-2">
                                <label>Anrede</label>
                                <select class="form-control" id="anrede" name="anrede">
                                    <option value="herr" @if($addInfo->anrede == "herr")selected="selected"@endif >Herr</option>
                                    <option value="frau" @if($addInfo->anrede == "frau")selected="selected"@endif >Frau</option>
                                    <option value="na" @if($addInfo->anrede == "na")selected="selected"@endif >keine Angabe</option>
                                </select>
                            </div>
                            <div class="col-2">
                                <label>Status</label>
                                <select class="form-control select2" id="status" name="status">
                                    <option value="active" @if($lehrer->status == "active")selected="selected"@endif >aktiv</option>
                                    <option value="inactive" @if($lehrer->status == "inactive")selected="selected"@endif >inaktiv</option>
                                </select>
                            </div>
                            <div class="col-2">
                                <label>Personaltyp *</label>
                                <select class="form-control select2" id="position" name="position" required>
                                    <option value="" @if($addInfo->position == "")selected="selected"@endif ></option>
                                    <option value="Dozent*in" @if($addInfo->position == "Dozent*in")selected="selected"@endif >Dozent*in</option>
                                    <option value="Mitarbeiter*in" @if($addInfo->position == "Mitarbeiter*in")selected="selected"@endif >Mitarbeiter*in</option>
                                    <option value="Schulassistent*in" @if($addInfo->position == "Schulassistent*in")selected="selected"@endif >Schulassistent*in</option>
                                    <option value="Referendar*in" @if($addInfo->position == "Referendar*in")selected="selected"@endif >Referendar*in</option>
                                    <option value="Praktikant*in" @if($addInfo->position == "Praktikant*in")selected="selected"@endif >Praktikant*in</option>
                                    <option value="Verwaltung" @if($addInfo->position == "Verwaltung")selected="selected"@endif >Verwaltung</option>
                                </select>
                            </div>
                            <div class="col-4">
                                <label>Dozent*in verlinkt mit Dozent*in aus dem SVS:</label>
                                <select class="form-control select2" name="external_booking_id">
                                    <option value="-1" selected>Nicht verlinkt</option>
                                    @foreach(\App\BookingSystemCacheKeyValue::where('name','=','svs.lehrer')->get() as $svs_room)
                                        <option value="{{$svs_room->foreign_id}}" @if($svs_room->foreign_id == $lehrer->external_booking_id) selected @endif>{{ json_decode($svs_room->value)->anrede." ".json_decode($svs_room->value)->vorname." ".json_decode($svs_room->value)->nachname." (#".$svs_room->foreign_id.")"}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <label>Vorname</label>
                        <input type="text" class="form-control" id="firstname" name="firstname" value="{{$lehrer->firstname}}" onchange ="setAnrede()">
                        <label>Nachname</label>
                        <input type="text" class="form-control" id="lastname" name="lastname" value="{{$lehrer->lastname}}" onchange ="setAnrede()">
                        <label>Anzeigen als</label>
                        <select class="form-control" id="displayname" name="displayname">
                        </select>
                        <label>Personalnummer</label>
                        <input type="text" class="form-control" name="personalnummer" value="{{$addInfo->personalnummer}}">
                        <label>Wochenstunden</label>
                        <input type="text" class="form-control" name="week_hours" value="{{$lehrer->week_hours}}">
                        <label>Faktor</label>
                        <input type="text" class="form-control" name="faktor_default" value="{{$lehrer->faktor_default}}">
                        <label>Schule</label>
                        <select class="select2" name="school[]" multiple="multiple">
                            @foreach($schulen as $schule)
                                <option value="{{$schule->id}}" @if($lehrer->schulen->contains($schule->id)) selected @endif>{{$schule->name}}</option>
                            @endforeach
                        </select>
                        <label>Fächer</label>
                        <select class="select2" name="facher[]" multiple="multiple">
                            @foreach($facher as $fach)
                                <option value="{{$fach->id}}" @if($lehrer->faecher->contains($fach->id)) selected @endif>{{$fach->name}}</option>
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
                </div>

                <div id="pers" class="tabcontent">
                    <h5>Persönliche Daten</h5>
                    <div class="form-group">
                        <label>Geburtsdatum</label>
                        <input type="date" class="form-control" name="birthdate" value="{{$addInfo->birthdate}}">
                        <label>Geburtsort</label>
                        <input type="text" class="form-control" name="birthplace" value="{{$addInfo->birthplace}}">
                        <label>Geburtsname</label>
                        <input type="text" class="form-control" name="birthname" value="{{$addInfo->birthname}}">
                        <label>Konfession</label>
                        <input type="text" class="form-control" name="religion" value="{{$addInfo->religion}}">
                        <label>Familienstand</label>
                        <select class="form-control" name="familienstand">
                            <option value="ledig" @if($addInfo->familienstand == "ledig")selected="selected"@endif>ledig</option>
                            <option value="verheiratet" @if($addInfo->familienstand == "verheiratet")selected="selected"@endif>verheiratet</option>
                            <option value="geschieden" @if($addInfo->familienstand == "geschieden")selected="selected"@endif>geschieden</option>
                            <option value="verwitwet" @if($addInfo->familienstand == "verwitwet")selected="selected"@endif>verwitwet</option>
                        </select>
                        <label>Berufsqualifikation</label>
                        <input type="text" class="form-control" name="schulabschluss" value="{{$addInfo->schulabschluss}}">
                        <label>Bundesland</label>
                        <input type="text" class="form-control" name="bundesland" value="{{$addInfo->bundesland}}">

                    </div>
                    <h5>Notizen</h5>
                    <label>Allgemeine Notizen</label>
                    <textarea type="text" class="form-control" name="notes" rows="5">{{$addInfo->notes}}</textarea>
                    <h5>Bankinformationen</h5>
                    <div class="form-group">
                        <label>Bankleitzahl</label>
                        <input type="text" class="form-control" name="blz" value="{{$addInfo->blz}}">
                        <label>Kreditinstitut</label>
                        <input type="text" class="form-control" name="bank" value="{{$addInfo->bank}}">
                        <label>Kontoinhaber</label>
                        <input type="text" class="form-control" name="kontoinhaber" value="{{$addInfo->kontoinhaber}}">
                        <label>Kontonummer</label>
                        <input type="text" class="form-control" name="kontonummer" value="{{$addInfo->kontonummer}}">
                        <label>IBAN</label>
                        <input type="text" class="form-control" name="iban" value="{{$addInfo->iban}}">
                        <label>BIC</label>
                        <input type="text" class="form-control" name="bic" value="{{$addInfo->bic}}">
                    </div>
                </div>
                <div id="merk" class="tabcontent">
                    <h5>Merkmale</h5>
                    <div class="form-group">
                        <input name = "merk" type="text" class="form-control">
                    </div>
                </div>

                <div id="historie" class="tabcontent">
                    <h5>Historie</h5>
                    @include('verwaltung.schulerakte.snippets.changeHistoryAdditionalInfo',["additionInfo" => $addInfo])
                </div>

                <button type="submit" class="btn btn-success mt-1 ">Speichern</button>
            </form>



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
        window.onload = function () {
           setAnrede();

        }
    </script>
    <script>
        function removeAllOptions(sel) {
            var len, par;

            len = sel.options.length;
            for (var i=len; i; i--) {
                par = sel.options[i-1].parentNode;
                par.removeChild( sel.options[i-1] );
            }
        }
        function getSelectedText(elementId) {
            var elt = document.getElementById(elementId);

            if (elt.selectedIndex == -1)
                return null;

            return elt.options[elt.selectedIndex].text;
        }


        function setAnrede() {
            // get reference to select element
            var sel = document.getElementById('displayname');
            //remove all options
            removeAllOptions(sel);
            var opt = new Option("{{ $addInfo->displayname }}", "{{ $addInfo->displayname }}");
            opt.selected = 'selected';
            sel.appendChild(opt);
            //get chosen title
            var title = document.getElementById("title").value;
            // get vorname
            var vorname = document.getElementById("firstname").value;
            var nachname = document.getElementById("lastname").value;
            if(title != ""){
                for (var i = 0; i<=3; i++){
                    // create new option element
                    opt = document.createElement('option');
                    // create text nodes to add to option element (opt)
                    switch (i) {
                        case 0:
                            opt.appendChild( document.createTextNode(vorname + " " + nachname) );
                            opt.value = vorname + " " + nachname;
                            break;
                        case 1:
                            opt.appendChild( document.createTextNode(title +" "+ vorname + " " + nachname) );
                            opt.value = title +" "+ vorname + " " + nachname;
                            break;
                        case 2:
                            opt.appendChild( document.createTextNode(title +" "+ nachname + ", " + vorname) );
                            opt.value = title +" "+ nachname + ", " + vorname;
                            break;
                        case 3:
                            opt.appendChild( document.createTextNode(vorname + " " + nachname + ", " + title) );
                            opt.value = vorname + " " + nachname + ", " + title;
                            break;
                    }

                    // set value property of opt
                    // add opt to end of select box (sel)
                    sel.appendChild(opt);
                }
            }else{
                var opt = document.createElement('option');
                opt.appendChild( document.createTextNode(vorname + " " + nachname) );
                opt.value = vorname + " " + nachname;
                sel.appendChild(opt);
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
            localStorage.setItem('activeTab_dozent', evt.target.id);
        }

        // Get the element with id="defaultOpen" and click on it
        if(localStorage.getItem('activeTab_dozent') != null){
            document.getElementById(localStorage.getItem('activeTab_dozent')).click();
        }
        else{
            document.getElementById("link1").click();
        }
    </script>

@endsection
