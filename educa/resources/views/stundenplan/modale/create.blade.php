<div class="modal fade" id="createModal" data-backdrop="static"  role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Unterricht manuell anlegen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputSubjectCreate">Fach</label>
                        <select id="inputSubjectCreate" class="form-control select2">
                        </select>
                    </div>
                    <div class="form-group col-md-8">
                        <label for="inputDay">Beginnt am</label>
                        <div class="form-group">
                            <div class="input-group date" id="datepicker10" data-target-input="nearest">
                                <input id="inputDayCreate" name="inputDayCreate" type="text" class="form-control datetimepicker-input" data-target="#datepicker10" required/>
                                <div class="input-group-append" data-target="#datepicker10" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputStartCreate">Beginn</label>
                        <div class="form-group">
                            <div class="input-group date" id="datepicker5" data-target-input="nearest">
                                <input id="inputStartCreate" name="inputStartCreate" type="text" class="form-control datetimepicker-input" data-target="#datepicker5" required/>
                                <div class="input-group-append" data-target="#datepicker5" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-clock"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="inputEndCreate">Ende</label>
                        <div class="form-group">
                            <div class="input-group date" id="datepicker6" data-target-input="nearest">
                                <input id="inputEndCreate" name="inputEndCreate" type="text" class="form-control datetimepicker-input" data-target="#datepicker6" required/>
                                <div class="input-group-append" data-target="#datepicker6" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-clock"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                <button id="createUnterricht" type="button" class="btn btn-primary">Erstellen</button>
            </div>
        </div>
    </div>
</div>
