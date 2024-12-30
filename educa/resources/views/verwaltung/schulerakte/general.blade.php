@extends('verwaltung.schulerakte.main')

@section('siteContent')
    @if (session()->has('status'))
        <div class="alert alert-success">
            {{ session()->get('status') }}
        </div>
    @endif
    <div class="tab">
        <button class="tablinks" onclick="openTab(event, 'allg')" id="link1">Allgemein</button>
        <button class="tablinks" onclick="openTab(event, 'pers')" id="link2">Persönliches</button>
        @foreach($formulare as $formula)
            <button class="tablinks" onclick="openTab(event, 'form_{{ $formula->id }}')" id="link_form_{{ $formula->id }}">{{ $formula->name }}</button>
        @endforeach
        <button class="tablinks" onclick="openTab(event, 'merk')" id="link3">Merkmale (veraltet)</button>


       <!-- <button class="tablinks" onclick="openTab(event, 'historie')" id="link4">Historie</button> -->
        <!-- <button class="tablinks" onclick="openTab(event, 'vorg')">Vorgänge</button> -->
    </div>
    <form class="form" style="margin-bottom: 0px;" method="POST" action="{{route('schuler.store',$schuler->id)}}" enctype = "multipart/form-data">
        @csrf
        <div id="allg" class="tabcontent">
            <h5>Allgemeine Daten</h5>
            @php try { @endphp
            @include('verwaltung.schulerakte.snippets.generalInformation')
            @php } catch(\Exception $e) { } @endphp
            <button type="submit" class="btn btn-success mt-1 ">Speichern</button>
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
                <label>Nationalität</label>
                <input type="text" class="form-control" name="nationalitaet" value="{{ $addInfo->nationalitaet }}">
                <!-- <label>Geschlecht</label>
                <select class="form-control" id="anrede" name="gender">
                    <option value="male" @if($addInfo->gender == "male")selected="selected"@endif>männlich</option>
                    <option value="female" @if($addInfo->gender == "female")selected="selected"@endif>weiblich</option>
                    <option value="na" @if($addInfo->gender == "na")selected="selected"@endif>keine Angabe</option>
                </select> -->
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
            <h5>Bank Informationen</h5>
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
            <button type="submit" class="btn btn-success mt-1 ">Speichern</button>
        </div>
        <div id="merk" class="tabcontent">
            <h5>Merkmale</h5>
            <div class="float-right">
                <a href="#merk" onclick="addMerkmalRow()" class="btn btn-primary">Hinzufügen</a>
            </div>
            <table id="merkmalsTable" class="tree table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Wert</th>
                    <th>Aktion</th>
                </tr>
                </thead>
                <tbody>
                @foreach($merkmale as $merkmal)
                    <tr>
                        <td><input name="merkmal_key_{{ $loop->index }}" class="form-control" type="text" readonly="" value="{{ $merkmal->key }}"></td>
                        <td><input name="merkmal_value_{{ $loop->index }}" class="form-control" type="text" value="{{ $merkmal->value }}"></td>
                        <td><a href="/verwaltung/schulerlisten/{{ $schuler->id }}/merkmale/{{ $merkmal->key }}/delete" class="btn btn-xs btn-danger"><i class="fas fa-trash-alt"></i></a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>


        <div id="historie" class="tabcontent">
            <h5>Historie</h5>
            @include('verwaltung.schulerakte.snippets.changeHistoryAdditionalInfo',["additionInfo" => $addInfo])
        </div>

    </form>


    @foreach($formulare as $formula)
        <div id="form_{{ $formula->id }}" class="tabcontent">
            <div style="display: none;" class="alert alert-warning" role="alert" id="form_formular_{{ $formula->id }}_warn">
                Es wurden nicht alle Pflichtfelder ausgefüllt.
            </div>
            <h5>{{ $formula->name }}</h5>
            @foreach($formula->last_revision->html("form_formular_".$formula->id, $schuler->getLatestFormulaDataFor($formula)) as $d)
                {!! $d !!}
            @endforeach
            <button type="button" class="btn btn-success" onclick="validateAndSubmit('form_formular_{{ $formula->id }}')">Speichern</button>
        </div>
    @endforeach


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
        function validateAndSubmit(formId)
        {
            $("#" + formId).validate({
                lang: 'de',
                invalidHandler: function(event, validator) {
                    // 'this' refers to the form
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        var message = errors == 1
                            ? 'Es wurde ein Feld nicht ausgefüllt.'
                            : 'Es wurden ' + errors + ' Felder nicht ausgefüllt.';
                        $("#" + formId + "_warn").html(message);
                        $("#" + formId + "_warn").show();
                    } else {
                        $("#" + formId + "_warn").hide();
                    }
                },
                errorElement: 'span',
                errorPlacement: function (error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });
            $("#" + formId).submit();
        }

    </script>
    <script>
        window.onload = function () {
            setAnrede();

        }
    </script>
    <script>
        var newRowCount = 0;
        function addMerkmalRow()
        {
            var table = document.getElementById("merkmalsTable");
            var row = table.insertRow(-1);
            var key = row.insertCell(0);
            var val = row.insertCell(1);
            var act = row.insertCell(2);

            key.innerHTML = "<input name=\"merkmal_key_NEW" + newRowCount + "\" class=\"form-control\" type=\"text\">";
            val.innerHTML = "<input name=\"merkmal_value_NEW" + newRowCount + "\" class=\"form-control\" type=\"text\">";
            act.innerHTML = "<a href=\"#merk\" class=\"btn btn-xs btn-danger\" onclick=\"deleteRow(this)\"><i class=\"fas fa-trash-alt\"></i></a>";
            newRowCount++;
        }

        function deleteRow(r)
        {
            var i = r.parentNode.parentNode.rowIndex;
            document.getElementById("merkmalsTable").deleteRow(i);
        }

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
            //get chosen title
            var title = getSelectedText("title");//document.getElementById("title");//.value;
            // get vorname
            var vorname = document.getElementById("firstname").value;
            var nachname = document.getElementById("lastname").value;
            if(title != ""){
                for (var i = 0; i<=3; i++){
                    // create new option element
                    var opt = document.createElement('option');
                    // create text nodes to add to option element (opt)
                    switch (i) {
                        case 0:
                            opt.appendChild( document.createTextNode(vorname + " " + nachname) );
                            break;
                        case 1:
                            opt.appendChild( document.createTextNode(title +" "+ vorname + " " + nachname) );
                            break;
                        case 2:
                            opt.appendChild( document.createTextNode(title +" "+ nachname + ", " + vorname) );
                            break;
                        case 3:
                            opt.appendChild( document.createTextNode(vorname + " " + nachname + ", " + title) );
                            break;
                    }

                    // set value property of opt
                    opt.value = i+"";
                    // add opt to end of select box (sel)
                    sel.appendChild(opt);
                }
            }else{
                var opt = document.createElement('option');
                opt.appendChild( document.createTextNode(vorname + " " + nachname) );
                opt.value = 0+"";
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
            localStorage.setItem('activeTab_general', evt.target.id);
        }

        // Get the element with id="defaultOpen" and click on it
        if(localStorage.getItem('activeTab_general') != null){
            document.getElementById(localStorage.getItem('activeTab_general')).click();
        }
        else{
            document.getElementById("link1").click();
        }



    </script>
@endsection
@section('additionalScript')
    <script>

        @foreach($formulare as $formula)
        $('#form_formular_{{ $formula->id }}').attr("action", "/verwaltung/schulerlisten/{{ $schuler->id }}/formsubmit/{{ $formula->id }}");
        @endforeach
    </script>
@endsection
