<div class="modal fade" id="editModal" data-backdrop="static" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Praxiseinsatz bearbeiten</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form>
                @csrf
                <input type="hidden" id="praxis_id">
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="inputStartCreate">Beginn</label>
                            <div class="form-group">
                                <div class="input-group date" id="datepicker8" data-target-input="nearest">
                                    <input id="inputStartCreate" name="inputStartCreate" type="text" class="form-control datetimepicker-input" data-target="#datepicker8" required/>
                                    <div class="input-group-append" data-target="#datepicker8" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="inputEndCreate">Ende</label>
                            <div class="form-group">
                                <div class="input-group date" id="datepicker9" data-target-input="nearest">
                                    <input id="inputEndCreate" name="inputEndCreate" type="text" class="form-control datetimepicker-input" data-target="#datepicker9" required/>
                                    <div class="input-group-append" data-target="#datepicker9" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="inputDay">Werktage (berechnet)</label>
                            <input id="workingDays" type="text" class="form-control" disabled/>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="inputSubjectCreate">Teilnehmer</label>
                            <select id="schuler_id" class="form-control select2" required>
                                @foreach($selectKlasse->schuler as $schuler)
                                    <option value="{{$schuler->id}}" >{{ $schuler->firstname." ".$schuler->lastname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="inputDay">Tagesstunden</label>
                            <input id="hours_day" type="number" class="form-control" required/>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="inputDay">Gesamtstunden</label>
                            <input id="complete_hours" type="number" class="form-control" required/>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputDay">Anrechnung in Modul:</label>
                            <select id="lehreinheit_id" class="form-control select2">
                                <option value="-1">Bitte wählen</option>
                                @foreach($selectKlasse->getLehrplan as $lehrplan)
                                    <optgroup label="{{ $lehrplan->name }}">
                                    @foreach($lehrplan->lehreinheiten() as $lehrEinheit)
                                        @if($lehrEinheit->form == "praxis")
                                        <option value="{{ $lehrEinheit->id }}">{{ $lehrEinheit->name }}</option>
                                            @endif
                                    @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputDay">Unternehmen</label>
                            <select id="unternehmen_id" class="form-control select2">
                                <option value="-1">Bitte wählen</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputDay">Ansprechpartner</label>
                            <select id="kontakt_id" class="form-control select2">
                                <option value="-1">Bitte wählen</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="inputDay">Besuchstermine</label>
                        <div style="margin-bottom: 3px;">
                            <div class="float-right">
                                <a href="#" onclick="createBesuch()" data-toggle="modal" data-target="#dateModal" class="btn btn-primary">Hinzufügen</a>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <table id="besuchstabelle" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>Dozent</th>
                                <th>Termin</th>
                                <th>Bericht erforderlich</th>
                                <th>Durchgeführt</th>
                                <th>Aktion</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                    <button onclick="deletePraxisEinsatz();" type="button" class="btn btn-danger" data-dismiss="modal">Löschen</button>
                    <button type="button" onclick="savePraxisEinsatz()" class="btn btn-primary">Speichern</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function savePraxisEinsatz() {
        var id = $('#praxis_id').val();
        var startDate = $('#inputStartCreate').val();
        var endDate = $('#inputEndCreate').val();
        var hours_day = $('#hours_day').val();
        var complete = $('#complete_hours').val();
        var unternehmen_id = $('#unternehmen_id').val();
        var modul_id = $('#lehreinheit_id').val();
        var schuler_id = $('#schuler_id').val();
        var kontakt_id = $('#kontakt_id').val();
        postData('/praxis/{{ $selectKlasse->id }}/{{ $selectAbschnitt->id }}/ajax/praxiseinsatz/update',
            { id: id, startDate: startDate, endDate: endDate, hours_day: hours_day, complete: complete, modul_id: modul_id, unternehmen_id: unternehmen_id, schuler_id: schuler_id, kontakt_id: kontakt_id }, '{{ csrf_token() }}',
            (data) => {
                reloadData();
                $('#editModal').modal('hide');
            });
    }
    function createBesuch()
    {
        $('#besuch_id').val("new");
    }

    function deletePraxisEinsatz()
    {
        var id = $('#praxis_id').val();
        postData('/praxis/{{ $selectKlasse->id }}/{{ $selectAbschnitt->id }}/ajax/praxiseinsatz/remove', { item: { "id" : id } }, '{{ csrf_token() }}',
            (data) => {
                reloadData();
            });
    }
</script>
