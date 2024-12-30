<div class="row">
    <div class="col-sm">
        <div style="margin-bottom: 3px;">
            <div class="float-right">
                @isset($_GET["withFehlzeitVorgemerkt"])
                    <a href="#" data-toggle="modal" data-target="#fehlzeitModal" class="btn btn-primary">Vormerken</a>
                @else
                    <a href="?withFehlzeitVorgemerkt=true" class="btn btn-primary">vorgemerkte Fehlzeiten anzeigen</a>
                @endisset
            </div>
            <div class="clearfix"></div>
        </div>
        <table id="table_id" class="data-table table table-striped table-bordered">
            <thead>
            <tr>
                <th>Datum / Zeitraum</th>
                <th>Fach</th>
                <th>Grund</th>
                <th>Aktion</th>
            </tr>
            </thead>
            <tbody>
            @foreach($schuler->klassenbuchTeilnahme as $fehlzeit)
                @if($fehlzeit->eintrag->exists() && $fehlzeit->typ->standart == 0)
                <tr>
                    <td data-order="{{ strtotime($fehlzeit->eintrag->startDate) }}" >{{ date("d.m.Y H:i", strtotime($fehlzeit->eintrag->startDate)) }} - {{ date("H:i", strtotime($fehlzeit->eintrag->endDate)) }}</td>
                    @if($fehlzeit->eintrag->fach != null)
                        <td>{{ $fehlzeit->eintrag->fach->name }}</td>
                        @else
                        <td><i>Fach gelöscht oder unbekannt</i></td>
                        @endif
                    <td> {{ $fehlzeit->typ->name }}</td>
                    <td>
                        <a href="/verwaltung/schulerlisten/{{ $schuler->id }}/fehlzeiten/normal/{{ $fehlzeit->id }}/edit" class="btn btn-primary">Fehlzeit anpassen</a>
                    </td>
                </tr>
                @endif
            @endforeach
            @isset($_GET["withFehlzeitVorgemerkt"])
            @foreach($schuler->klassenbuchVorgemerkt() as $fehlzeit)
                <tr>
                    <td>{{date('d.m.Y H:i', strtotime($fehlzeit->startDate))}} - {{date('d.m.Y H:i', strtotime($fehlzeit->endDate))}}</td>
                    <td><i>vorgemerkt</i></td>
                    <td>
                        @foreach($fehlzeit_typs as $fehlzeit_typ)
                            @if($fehlzeit->fehlzeit_typ_id == $fehlzeit_typ->id) {{ $fehlzeit_typ->name }} @endif
                        @endforeach
                    </td>
                    <td>
                        <a href="/verwaltung/schulerlisten/{{ $schuler->id }}/fehlzeiten/reserved/{{ $fehlzeit->id }}/delete" class="btn btn-danger">Vormerken löschen</a>
                    </td>
                </tr>
            @endforeach
            @endisset
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="fehlzeitModal" role="dialog" aria-labelledby="fehlzeitModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fehlzeitModalLabel">Fehlzeit vormerken</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="note_form" class="form" method="POST" action="{{route('fehlzeit.create',['id2'=>$schuler->id, 'id' => isset($selectedKlasse) ? $selectedKlasse->id : 0])}}" enctype = "multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Start</label>
                        <div class="input-group date" id="datetimepicker1" data-target-input="nearest">
                            <input value="{{ date("d.m.Y H:i") }}" id="inputDate" name="start" type="text" class="form-control datetimepicker-input tobeHidden" data-target="#datetimepicker1" required/>
                            <div class="input-group-append" data-target="#datetimepicker1" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>

                        <label>Ende</label>
                        <div class="input-group date" id="datetimepicker2" data-target-input="nearest">
                            <input value="{{ date("d.m.Y H:i") }}" id="inputDate" name="end" type="text" class="form-control datetimepicker-input tobeHidden" data-target="#datetimepicker2" required/>
                            <div class="input-group-append" data-target="#datetimepicker2" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>

                        <label>Grund</label>
                        <select class="form-control select2" name="fehlzeit_typ" required>
                            @foreach($fehlzeit_typs as $fehlzeit_typ)
                                @if($fehlzeit_typ->aktive == 1 && $fehlzeit_typ->default != true)
                            <option value="{{ $fehlzeit_typ->id }}">{{ html_entity_decode($fehlzeit_typ->name) }}</option>
                                @endif
                            @endforeach
                        </select>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Vormerken</button>
                </div>
            </form>
        </div>
    </div>
</div>
