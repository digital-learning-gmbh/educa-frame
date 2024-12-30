@if($schuler != null && $selectedKlasse != null)

    <div class="card">
        <div class="card-header"><b>{{ $schuler->displayName }}</b>
            @if(!isset($hideButton))
                <a href="/verwaltung/schulerlisten?student_id={{ $schuler->id }}" type="button" class="btn btn-primary">Studenten-Datei öffnen
                    öffnen</a>
            @endif
        </div>
        <div class="accordion" id="accordionExample">
            <div class="card">
                <div class="card-header" id="headingTwo">
                    <h2 class="mb-0">
                        <button onclick="openTab(event, 'fehl')" id="link2" class="btn btn-link collapsed" type="button"
                                data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false"
                                aria-controls="collapseTwo">
                            Fehlzeiten
                        </button>
                    </h2>
                </div>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                    <div class="card-body">
                        @component('verwaltung.schulerakte.snippets.fehlzeiten',["schuler" => $schuler, "fehlzeit_typs" => $fehlzeit_typs])
                        @endcomponent
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" id="headingThree">
                    <h2 class="mb-0">
                        <button onclick="openTab(event, 'noten')" id="link3" class="btn btn-link collapsed"
                                type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false"
                                aria-controls="collapseThree">
                            Noten
                        </button>
                    </h2>
                </div>
                <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                    <div class="card-body">
                        <p>Hier gibt es die Möglichkeit, Noten für einen Schüler zu hinterlegen.</p>
                        <div class="row">
                            <div class="col-sm">
                                <div style="margin-bottom: 3px;">
                                    <div class="float-right">
                                        <a href="#" data-toggle="modal" data-target="#noteModal"
                                           class="btn btn-primary">manuell Note anlegen</a>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <table id="table_id" class="data-table table table-striped table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Datum</th>
                                        <th>Fach</th>
                                        <th>Note</th>
                                        <th>Gewicht</th>
                                        <th>Typ</th>
                                        <th>Bemerkung</th>
                                        <th>Aktion</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($schuler->note as $note)
                                        <tr>
                                            <td>{{ date_format(new DateTime($note->datum),"d.m.Y")}}</td>
                                            <td>@if($note->fach != null) {{ $note->fach->name  }} @endif @if($note->exam != null) {{ $note->exam->name }} @endif</td>
                                            <td>{{ $note->note }}</td>
                                            <td>{{ $note->gewicht }}</td>
                                            <td>@if($note->typ) schriftlich @else mündlich @endif</td>
                                            <td>{{ $note->bemerkung }}</td>
                                            <td>
                                                <a onclick="editNote('{{ $note->id }}','{{ date_format(new DateTime($note->datum),"d.m.Y") }}','{{ $note->fach_id }}','{{ $note->note }}','{{ $note->gewicht }}','{{ $note->typ }}','{{ $note->bemerkung }}')"
                                                   href="#" class="btn btn-xs btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="/klassenbuch/note/delete/{{$note->id}}"
                                                   class="btn btn-xs btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" id="dokumentHeading">
                    <h2 class="mb-0">
                        <button onclick="openTab(event, 'noten')" id="link3" class="btn btn-link collapsed"
                                type="button" data-toggle="collapse" data-target="#collapseDokument"
                                aria-expanded="false" aria-controls="collapseDokument">
                            Dokumente
                        </button>
                    </h2>
                </div>
                <div id="collapseDokument" class="collapse" aria-labelledby="dokumentHeading"
                     data-parent="#accordionExample">
                    <div class="card-body" id="dokumentCard">
                        <p>Hier gibt es die Möglichkeit, Dokumente an die Schülerdatei zu hängen</p>
                        @component('documents.list',[ "model" => $schuler, "type" => "schuler"])
                        @endcomponent
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="noteModal" role="dialog" aria-labelledby="noteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="noteModalLabel">manuell Note hinzufügen/bearbeiten</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="note_form" class="form" method="POST"
                      action="{{route('note.create',['id'=>$selectedKlasse->id,'id2'=>$schuler->id])}}"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Datum</label>
                            <div class="input-group date" id="datepicker1" data-target-input="nearest">
                                <input value="{{ date("d.m.Y") }}" id="inputDate" name="datum" type="text"
                                       class="form-control datetimepicker-input tobeHidden" data-target="#datepicker1"
                                       required/>
                                <div class="input-group-append" data-target="#datepicker1" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                            <label>Fach</label>
                            <select class="form-control select2" id="fach" name="fach" required>
                                <option value="-1">Nichts ausgewählt</option>
                                @foreach($faches as $fach)
                                    <option value="{{$fach->id}}">{{$fach->name}}</option>
                                @endforeach
                            </select>
                            <label>Note</label>
                            <input title="Bitte eine Zahl zwischen 1 und 6 eingeben!" type="number" step="0.1"
                                   class="form-control" id="note" name="note" required>
                            <label>Gewichtung</label>
                            <input title="Bitte eine Zahl zwischen 0 und 1 eingeben! z.B. 0.25 für 25%" type="number"
                                   step="0.01" min="0" max="1" class="form-control" id="gewicht" name="gewicht"
                                   required>
                            <label>Typ</label>
                            <select class="form-control select2" id="typ" name="typ">
                                <option value="0">mündlich</option>
                                <option value="1">schriftlich</option>
                            </select>
                            <label>Bemerkung</label>
                            <textarea class="form-control" id="bemerkung" name="bemerkung"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Speichern</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <style>
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            /* display: none; <- Crashes Chrome on hover */
            -webkit-appearance: none;
            margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
        }

        input[type=number] {
            -moz-appearance: textfield; /* Firefox */
        }

    </style>
@endif
@section('additionalScript')
    @if($schuler != null && $selectedKlasse != null)
        <script>
            function editNote(id, datum, fach, note, gewicht, typ, bemerkung) {
                $('#noteModal').modal('show');
                //$('#schulerBox').val(idOfSchuler).trigger("change");
                $('#note_id').val(id);//add id to hidden input
                $('#inputDate').val(datum);
                $('#fach').val(fach).change();
                $('#note').val(note);
                $('#gewicht').val(gewicht);
                $('#typ').val(typ);
                $('#bemerkung').val(bemerkung);
                $('#note_form').attr('action', "/klassenbuch/note/edit/" + id);

            }

            $('#noteModal').on('hidden.bs.modal', function (e) {
                $('#note_id').val(-1);//set note id to default -1
                $('#inputDate').val(moment().format("DD.MM.YYYY"));
                $('#fach').val(-1).change();
                $('#note').val("");
                $('#gewicht').val("");
                $('#typ').val("");
                $('#bemerkung').val("");
                $('#note_form').attr('action', "/klassenbuch/{{$selectedKlasse->id}}/schueler/{{$schuler->id}}/createNote");
                // /{id}/schueler/{id2}/createNote
            });


            function openTab(evt, cityName) {
                localStorage.setItem('activeTab_schuler', document.getElementById(evt.target.id).getAttribute("data-target"));
            }

            // Get the element with id="defaultOpen" and click on it
            if (localStorage.getItem('activeTab_schuler') != null) {
                console.log(localStorage.getItem('activeTab_schuler').substring(1));
                document.getElementById(localStorage.getItem('activeTab_schuler').substring(1)).className += " show";
            }
        </script>
    @endif
@endsection
