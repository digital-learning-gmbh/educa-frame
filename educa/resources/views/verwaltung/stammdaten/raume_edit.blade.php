@extends('verwaltung.main')

@section('siteContent')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a  href="javascript:history.back()">Schule</a></li>
            <li class="breadcrumb-item active" aria-current="page">Raum bearbeiten</li>
        </ol>
    </nav>
    <h3>
        {{'Bearbeite Raum ' . $raum->name}}</h3>
    <div class="" >
            @if (session()->has('status'))
                <div class="alert alert-success">
                    {{ session()->get('status') }}
                </div>
            @endif
            <div class="tab">
                <button class="tablinks" onclick="openTab(event, 'allg')" id="link1">Allgemein</button>
                <button class="tablinks" onclick="openTab(event, 'ausstattung')" id="link2">Ausstattung</button>
            </div>
            <form id ="raumForm" class="form" onsubmit="return changedSize(this,{{$raum->size}})" method="POST" action="{{route('raum.store',$raum->id)}}" enctype = "multipart/form-data">
                {{ csrf_field() }}
                <div id="allg" class="tabcontent">
                    <h5>Allgemeine Daten</h5>
                    <div class="form-group">
                        <label>Bezeichnung</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{$raum->name}}">
                        <label>Gebäude</label>
                        <input type="text" class="form-control" id="gebaeude" name="gebaeude" value="{{$raum->gebaeude}}">
                        <label>Schule(n)</label>
                        <select class="form-control select2" name="school[]" multiple>
                            @foreach($schulen as $schule)
                                <option value="{{$schule->id}}" @if($raum->schulen->contains($schule)) selected @endif>{{$schule->name}}</option>
                            @endforeach
                        </select>
                        <label>max. Teilnehmeranzahl</label>
                        <input type="number" class="form-control" id="size" name="size" min="1" value="{{$raum->size}}">
                        <label>Bemerkungen</label>
                        <textarea  class="form-control" name="bemerkungen"> {{$raum->bemerkungen}}</textarea>
                        <label>Raum verlinkt mit Raum aus RIOS:</label>
                        <select class="form-control select2" name="external_booking_id" readonly="" disabled>
                            <option value="-1" selected>Nicht verlinkt</option>
                            @foreach(\App\BookingSystemCacheKeyValue::where('name','=','rios.room')->get() as $svs_room)
                                <option value="{{$svs_room->foreign_id}}" @if($svs_room->id == $raum->external_booking_id) selected @endif>{{ json_decode($svs_room->value)->Bezeichnung." ( ".json_decode($svs_room->value)->Nummer." ) #".$svs_room->foreign_id }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div id="ausstattung" class="tabcontent">
                    <label>Ausstattung</label>
                    <textarea class="form-control" name="ausstattung" rows="5" >{{$raum->ausstattung}}</textarea>
                </div>

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
            background-color: #fff;
            display: none;
            padding: 6px 12px;
            border: 1px solid #ccc;
            border-radius: 0rem 0rem 0.25rem 0.25rem;
            border-top: none;
        }
    </style>

    <script>

        function changedSize(form, orginalSize) {
            //var form = document.getElementById('raumForm');
            if(orginalSize != document.getElementById("size").value){
                if (confirm('Sie haben die Raumgröße verändert, dadurch müssen die Belegungen neugeprüft werden. Möchten Sie fortfahren?')) {
                    form.submit();
                } else {
                    document.getElementById("size").value = orginalSize;
                    return false;
                }
            }else{
                form.submit();
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
            localStorage.setItem('activeTab_raum', evt.target.id);
        }

        // Get the element with id="defaultOpen" and click on it
        if(localStorage.getItem('activeTab_raum') != null){
            document.getElementById(localStorage.getItem('activeTab_raum')).click();
        }
        else{
            document.getElementById("link1").click();
        }

    </script>

@endsection
