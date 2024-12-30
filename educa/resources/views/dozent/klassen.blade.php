@extends('layouts.klassenbuch')

@section('appContent')
    <div class="container-xl">
        <div class="row">
            <div class="col-md-3">
                <select id="personDropDown" class="select2 mb-2" onchange="changeKlasse(this.options[this.selectedIndex].value);" data-select2-id="personDropDown" tabindex="-1" aria-hidden="true">
                    @foreach($klassen as $klasse)
                        <option value="{{ $klasse->id }}" @if($selectedKlasse->id == $klasse->id) selected @endif>{{ $klasse->displayName }}</option>
                    @endforeach
                </select>
                <div class="card mt-2">
                    <div class="card-header"><a href="#" onclick="toggleVisibility()"><b>Teilnehmer</b></a></div>
                    <div class="list-group list-group-flush" style="max-height: 100vh; overflow-y: auto;">
                        @foreach($selectedKlasse->schulerAktuell()->orderBy('firstname')->orderby('lastname')->get() as $currentschuler)
                            <a href="/dozent/klassen/{{ $selectedKlasse->id }}/schueler/{{ $currentschuler->id }}" class="list-group-item list-group-item-action @if($schuler->id == $currentschuler->id)active @endif">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">{{ $currentschuler->displayname }}</h5>
                                </div>
                                <p class="mb-0"></p>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <span id="schuler">@include('klassenbuch.schuler.details')</span>
                <span id="list" style="display: none">
                    <table id="teilnehmer_table" class="table table-striped table-bordered" style="margin-top: 0px !important;">
                        <thead>
                            <tr>
                                <th>Nachname</th>
                                <th>Vorname</th>
                                <th>Zeitraum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($selectedKlasse->schulerAktuell()->orderBy('firstname')->orderby('lastname')->get() as $currentschuler)
                                <tr>
                                    <td>{{ $currentschuler->lastname }}</td>
                                    <td>{{ $currentschuler->firstname }}</td>
                                    <td>{{ $currentschuler->getFormatedVonBisInKlasse($selectedKlasse->id) }}</td>
                                </tr>
                            </a>
                            @endforeach
                        </tbody>
                    </table>
                </span>
            </div>
        </div>
    </div>
    <script>
        function changeKlasse(id) {
            window.location.href = "/dozent/klassen/" + id;
        }
        function toggleVisibility()
        {
            if($("#schuler").css("display") != "none")
            {
                $("#schuler").css("display", "none");
                if(! $.fn.DataTable.isDataTable('#teilnehmer_table'))
                {
                    $("#teilnehmer_table").DataTable({
                        colReorder: true,
                        dom: 'fBrtlip',
                        buttons: [ 'print','excel', 'pdf', 'colvis'
                        ],
                        language : {
                            url: "/js/german.json"
                        }
                    });
                }
                $("#list").css("display", "block")
            }
            else
            {
                $("#schuler").css("display", "block");
                $("#list").css("display", "none")
            }
        }
    </script>
@endsection
