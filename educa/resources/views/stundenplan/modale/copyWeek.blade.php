<div class="modal fade" id="copyWeekModal" data-backdrop="static"  role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Woche kopieren</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="copyDayStart">Wochenbeginn</label>
                        <div class="form-group">
                            <div class="input-group date" id="datepicker101" data-target-input="nearest">
                                <input id="copyDayStart" name="copyDayStart" type="text" class="form-control datetimepicker-input" data-target="#datepicker101" readonly disabled/>
                                <div class="input-group-append" data-target="#datepicker101" data-toggle="datetimepicker">
                                    <!--<div class="input-group-text"><i class="fa fa-calendar"></i></div>-->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="copyDayEnd">Wochenende</label>
                        <div class="form-group">
                            <div class="input-group date" id="datepicker102" data-target-input="nearest">
                                <input id="copyDayEnd" name="copyDayEnd" type="text" class="form-control datetimepicker-input" data-target="#datepicker102" readonly disabled/>
                                <div class="input-group-append" data-target="#datepicker102" data-toggle="datetimepicker">
                                    <!--<div class="input-group-text"><i class="fa fa-calendar"></i></div>-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <h4 class="col-md-12">Kopieren nach Woche:</h4>

                    <div class="form-group col-md-6">
                        <label for="targetDayStart">Beginnt am</label>
                        <div class="form-group">
                            <div class="input-group date" id="datepicker103" data-target-input="nearest">
                                <input id="targetDayStart" name="targetDayStart" type="text" class="form-control datetimepicker-input" data-target="#datepicker103" required/>
                                <div class="input-group-append" data-target="#datepicker103" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--<div class="form-group col-md-6">
                        <label for="targetDayEnd">Endet am</label>
                        <div class="form-group">
                            <div class="input-group date" id="datepicker104" data-target-input="nearest">
                                <input id="targetDayEnd" name="targetDayEnd" type="text" class="form-control datetimepicker-input" data-target="#datepicker104" required/>
                                <div class="input-group-append" data-target="#datepicker104" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>-->
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                <button id="copyUnterricht" type="button" class="btn btn-primary">Kopieren</button>
            </div>
        </div>
    </div>
</div>
