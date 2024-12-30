<div class="card" style="margin-top: 5px;">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <b>Teilnehmerverteilung</b>
        <ul class="navbar-nav mr-auto">
            <!-- <li class="nav-item active">
                <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Link</a>
            </li> -->
            @if($selectAbschnitt != null && $selectAbschnitt->type == "praxis")
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Export
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="/praxis/sections/{{ $selectKlasse->id }}/{{ $selectAbschnitt->id }}/export/?overview">Übersichtsliste</a>
                    <a class="dropdown-item" href="/praxis/sections/{{ $selectKlasse->id }}/{{ $selectAbschnitt->id }}/export/?page">Pro Blatt ein Teilnehmer</a>
                    <div class="dropdown-divider"></div>
                 <!--   <a class="dropdown-item" href="#">Excel</a> -->
                </div>
            </li>
                @endif
        </ul>

    </div>
    </nav>
    <div class="card-body">
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Hinweis:</strong> Halten Sie Strg-Taste gedrückt und markieren Sie einen Bereich, um einen Praxiseinsatz zu erstellen.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div id="visualization"></div>

        @include('praxis.parts.modale.praxiseinsatz')
        @include('praxis.parts.modale.praxisbesucht')

      </div>
</div>
<style>
    @foreach($selectKlasse->getLehrplan as $lehrplan)
        @foreach($lehrplan->lehreinheiten() as $lehrEinheit)
            @if($lehrEinheit->form == "praxis")
                  .modul{{ $lehrEinheit->id }} {
                        background-color: {{ $lehrEinheit->color }} !important;
                    }
            @endif
        @endforeach
    @endforeach
</style>


@section('additionalScript')
    @if($selectAbschnitt != null && $selectAbschnitt->type == "praxis")
        <script type="text/javascript" src="/js/vis/vis-timeline-graph2d.min.js"></script>
        <script type="text/javascript">

            moment.updateLocale('de', {
                holidays: [],
                holidayFormat: 'MM-DD-YYYY',
                workingWeekdays: [1, 2, 3, 4, 5] // sunday is zero
            });

            function updateBuisnessDays() {
                console.log("Calculate complete hours");
                var diff = moment($('#inputStartCreate').val(), 'DD.MM.YYYY').businessDiff(moment($('#inputEndCreate').val(),'DD.MM.YYYY'));
                $('#workingDays').val(diff);
                $('#complete_hours').val(diff * $('#hours_day').val());
            }

            $("#hours_day").on('input', function() {
                updateBuisnessDays();
            });

            $("#inputStartCreate").on("input", function (e) {
                updateBuisnessDays();
            });

            $("#inputEndCreate").on("input", function (e) {
                updateBuisnessDays();
            });

            var besuchstable = $('#besuchstabelle').DataTable({
                ajax: '',
                "columns": [
                    { "data": "lehrer" },
                    { "data": "termin" },
                    { "data": "documentation" },
                    { "data": "done" },
                    { "data": "aktion" }
                ]
            });
            function loadAndShowPraxisEinsatz(id) {
                console.log(id)
                postData('/praxis/{{ $selectKlasse->id }}/{{ $selectAbschnitt->id }}/ajax/praxiseinsatz/info', { id: id }, '{{ csrf_token() }}',(data) => {
                        console.log(data);
                        besuchstable.ajax.url("/praxis/{{ $selectKlasse->id }}/{{ $selectAbschnitt->id }}/ajax/praxiseinsatz/besuche?id=" + id + "&_token={{ csrf_token() }}").load();
                        $('#praxis_id').val(data["id"]);
                        $('#inputStartCreate').val(moment(new Date(data["startDate"])).format("DD.MM.YYYY"));
                        $('#inputEndCreate').val(moment(new Date(data["endDate"])).format("DD.MM.YYYY"));
                        $('#hours_day').val(data["hours_day"]);
                        if(data["unternehmen_id"] != null) {
                            var newOption = new Option(data.unternehmen_name, data.unternehmen_id, false, false);
                            $('#unternehmen_id').empty().append(newOption).change();
                        } else {
                            $('#unternehmen_id').val("-1").change();
                        }
                        if(data["kontakt_id"] != null) {
                            var newOption = new Option(data.kontakt_name, data.kontakt_id, false, false);
                            $('#kontakt_id').empty().append(newOption).change();
                        } else {
                            $('#kontakt_id').val("-1").change();
                        }
                        if(data["lehrplan_einheit_id"] != null) {
                            $('#lehreinheit_id').val(data["lehrplan_einheit_id"]).change();
                        } else {
                            $('#lehreinheit_id').val("-1").change();
                        }
                        $('#schuler_id').val(data["schuler_id"]).change();
                        updateBuisnessDays();
                        $('#complete_hours').val(data["complete"]);
                        $('#editModal').modal('show');
                    });
            }

            function reloadData() {
                postData('/praxis/{{ $selectKlasse->id }}/{{ $selectAbschnitt->id }}/ajax/dataset','', '{{ csrf_token() }}',(data) => {
                        var dataBackend = data;
                        for (var item in dataBackend)
                        {
                            item["start"] = new Date(item["start"])
                            item["end"] = new Date(item["end"])
                        }
                        timeline.setItems(new vis.DataSet(dataBackend));
                    });
            }

            var groups = new vis.DataSet([
                @foreach($selectKlasse->schulerAktuell()->orderBy('lastname')->orderBy('firstname')->get() as $schuler)
                { content: {!! json_encode(strval($schuler->lastname.", ".$schuler->firstname)) !!}, id: {{ $schuler->id }}, value: {{ $schuler->id }}, className: "{{ $schuler->id }}" },
                @endforeach
            ]);

            var items = new vis.DataSet([
                @foreach($selectAbschnitt->praxisEinsatze as $einsatz)
                {
                    start: new Date("{{ date("Y/m/d H:i",strtotime($einsatz->startDate)) }}"),
                    end: new Date("{{ date("Y/m/d H:i",strtotime($einsatz->endDate)) }}"),
                    group: {{ $einsatz->schuler_id }},
                    className: "{{ $einsatz->id }} @if($einsatz->module() != null) modul{{ $einsatz->module()->id }} @endif",
                    @if($einsatz->unternehmen_id != null)
                    content: {!! json_encode($einsatz->unternehmen->name) !!},
                    @elseif($einsatz->lehrplan_einheit_id != null)
                    content: {!! json_encode($einsatz->module()->name) !!},
                    @else
                    content: {!!  json_encode($einsatz->name) !!},
                    @endif
                    id: {{ $einsatz->id }},
                },
                @endforeach
            ]);

            // DOM element where the Timeline will be attached
            var container = document.getElementById('visualization');

            // Configuration for the Timeline
            var options = {
                margin: {
                    item : {
                        horizontal : 0
                    }
                },
                // option groupOrder can be a property name or a sort function
                // the sort function must compare two groups and return a value
                //     > 0 when a > b
                //     < 0 when a < b
                //       0 when a == b
                groupOrderSwap: function (a, b, groups) {
                    var v = a.value;
                    a.value = b.value;
                    b.value = v;
                },
                onAdd: function (item, callback) {
                    callback(null);
                    if(item.type == "range")
                    {
                        console.log(item);
                        postData('/praxis/{{ $selectKlasse->id }}/{{ $selectAbschnitt->id }}/ajax/praxiseinsatz/create', { item: item }, '{{ csrf_token() }}',
                            (data) => {
                                reloadData();
                                loadAndShowPraxisEinsatz(data["id"]);
                            });
                    }
                },
                onUpdate: function (item, callback) {
                    loadAndShowPraxisEinsatz(item["id"]);
                },
                onMove: function(item, callback)
                {
                    console.log("Move",item);
                    postData('/praxis/{{ $selectKlasse->id }}/{{ $selectAbschnitt->id }}/ajax/praxiseinsatz/move', { item: item }, '{{ csrf_token() }}',
                        (data) => {
                            callback(item);
                        });
                },
                onRemove: function(item, callback)
                {
                    console.log(item);
                        postData('/praxis/{{ $selectKlasse->id }}/{{ $selectAbschnitt->id }}/ajax/praxiseinsatz/remove', { item: item }, '{{ csrf_token() }}',
                            (data) => {
                                callback(item);
                            });
                },
                groupTemplate: function(group){
                    var container = document.createElement('div');
                    var label = document.createElement('span');
                    label.innerHTML = group.content + ' ';
                    container.insertAdjacentElement('afterBegin',label);
                    var hide = document.createElement('button');
                    hide.innerHTML = '<i class="fas fa-plus"></i>';
                    hide.className = 'btn btn-primary';
                    hide.addEventListener('click',function(){
                        item  = { "group" : group.id , "end": "{{ date("Y-m-d H:i",strtotime($selectAbschnitt->end)) }}" , "start": "{{ date("Y-m-d H:i",strtotime($selectAbschnitt->begin)) }}" };
                        postData('/praxis/{{ $selectKlasse->id }}/{{ $selectAbschnitt->id }}/ajax/praxiseinsatz/create', { item: item }, '{{ csrf_token() }}',
                            (data) => {
                                reloadData();
                                loadAndShowPraxisEinsatz(data["id"]);
                            });
                    });
                    container.insertAdjacentElement('beforeEnd',hide);
                    return container;
                },
                zoomKey : 'ctrlKey',
                zoomMin : 86400000*5,
                orientation: "both",
                editable: true,
                timeAxis: {scale: 'day', step: 1},
                groupEditable: true,
                min: new Date("{{ date("Y/m/d",strtotime($selectAbschnitt->begin)) }}"),
                max: new Date("{{ date("Y/m/d",strtotime($selectAbschnitt->end)) }}"),
                start: new Date("{{ date("Y/m/d",strtotime($selectAbschnitt->begin)) }}"),
                end: new Date("{{ date("Y/m/d",strtotime("+30 days",strtotime($selectAbschnitt->begin))) }}"),
            };

            var timeline = new vis.Timeline(container);
            timeline.setOptions(options);
            timeline.setGroups(groups);
            timeline.setItems(items);
        </script>

        <script>
            $("#unternehmen_id").select2({
                minimumInputLength: 0,
                theme: 'bootstrap4',
                ajax: {
                    url: "/api/search/companies",
                    dataType: 'json',
                    data: function (params) {
                        return {
                            q: params.term, // search term
                            type: 'unternehmen',
                            school_id: {{ $global_school->id }}
                        };
                    }
                },
            });
            $("#kontakt_id").select2({
                minimumInputLength: 0,
                theme: 'bootstrap4',
                ajax: {
                    url: "/api/search/companies",
                    dataType: 'json',
                    data: function (params) {
                        return {
                            q: params.term, // search term
                            type: 'person',
                            school_id: {{ $global_school->id }},
                            relation_to: $("#unternehmen_id").val(),
                        };
                    }
                },
            });
        </script>
    @endif

@endsection
