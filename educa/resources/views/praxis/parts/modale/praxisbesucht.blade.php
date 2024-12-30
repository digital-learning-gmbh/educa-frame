<div class="modal fade" id="dateModal" data-backdrop="static"  role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Besuchstermin anlegen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form>
                @csrf
                <input value="new" id="besuch_id" type="hidden">
                <div class="modal-body">
                    <label for="inputDay">Dozent</label>
                    <select name="dozent" id="dozent_bericht" class="form-control select2">
                        @foreach($dozenten as $dozent)
                            <option value="{{ $dozent->id }}">{{ $dozent->displayName }}</option>
                        @endforeach
                    </select>
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <label for="exampleInputEmail1">Datum</label>
                            <div class="input-group date" id="datepicker11" data-target-input="nearest">
                                <input id="besuchsdatum" required name="begin" type="text" class="form-control datetimepicker-input" data-target="#datepicker11" />
                                <div class="input-group-append" data-target="#datepicker11" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="exampleInputEmail1">Dauer (Minuten)</label>
                            <input id="duration" type="number" class="form-control" value="60" required>
                        </div>
                    </div>
                    <div class="form-group col-md-12" id="ausfallSwitchDiv">
                        <div class="custom-control custom-switch">
                            <input id="documentation" type="checkbox" class="custom-control-input">
                            <label class="custom-control-label" for="documentation">Besuchsbericht erforderlich</label>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                    <button type="button" onclick="savePraxisBesuch()" class="btn btn-primary">Speichern</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function savePraxisBesuch() {
        var id = $('#besuch_id').val();
        var praxis_id = $('#praxis_id').val();
        var startDate = $('#besuchsdatum').val();
        var endDate = moment( $('#besuchsdatum').val(),'DD.MM.YYYY HH:mm').add($('#duration').val(), 'minute').format('DD.MM.YYYY HH:mm');
        var teacher_id = $('#dozent_bericht').val();
        var needDocumentation = $('#documentation').is(":checked");
        if(id == "new") {
            postData('/praxis/{{ $selectKlasse->id }}/{{ $selectAbschnitt->id }}/ajax/praxisbesuch/create',
                {
                    startDate: startDate,
                    endDate: endDate,
                    teacher_id: teacher_id,
                    praxis_id: praxis_id,
                    needDocumentation: needDocumentation
                }, '{{ csrf_token() }}',(data) => {
                    loadAndShowPraxisEinsatz(praxis_id);
                    $('#dateModal').modal('hide');
                });
        } else {
            postData('/praxis/{{ $selectKlasse->id }}/{{ $selectAbschnitt->id }}/ajax/praxisbesuch/update',
                {
                    id: id,
                    startDate: startDate,
                    endDate: endDate,
                    teacher_id: teacher_id,
                    praxis_id: praxis_id,
                    needDocumentation: needDocumentation
                }, '{{ csrf_token() }}',(data) => {
                    loadAndShowPraxisEinsatz(praxis_id);
                    $('#dateModal').modal('hide');
                });
        }
    }
    function editBesuch(id) {
        postData('/praxis/{{ $selectKlasse->id }}/{{ $selectAbschnitt->id }}/ajax/praxisbesuch/info',
            {
                id: id,
            }, '{{ csrf_token() }}',(data) => {
                $('#besuch_id').val(data["id"]);
                $('#besuchsdatum').val(moment(new Date(data["startDate"])).format("DD.MM.YYYY HH:mm"));
                $('#dozent_bericht').val(data["lehrer_id"]).change();
                $('#documentation').prop('checked', data["needDocumentation"] == 1);

                var start = moment(new Date(data["startDate"]));
                var end = moment(new Date(data["endDate"]));
                $('#duration').val(end.diff(start, 'minutes'));

                $('#dateModal').modal('show');

            });
    }
    function deleteBesuch(id) {
        var praxis_id = $('#praxis_id').val();
        postData('/praxis/{{ $selectKlasse->id }}/{{ $selectAbschnitt->id }}/ajax/praxisbesuch/delete',
            {
                id: id,
            }, '{{ csrf_token() }}',(data) => {
            loadAndShowPraxisEinsatz(praxis_id);
            $('#dateModal').modal('hide');
        });
    }
</script>
