<div class="modal" role="dialog" id="detailsShow">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Unterrichtseinheit bearbeiten</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="card">
                <div class="card-header" id="headingOne">
                    <h5 class="mb-0">
                        Allgemein
                    </h5>
                </div>

                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne"
                     data-parent="#accordionExample">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm">
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="inputRoom">Raum</label>
                                        <select id="inputRoom" class="form-control">
                                        </select>
                                    </div>
                                    <div class="form-group col-md-8">
                                        <label for="inputDay">Wochentag</label>
                                        <select id="inputDay" class="form-control" disabled>
                                            <option selected>Bitte auswählen...</option>
                                            <option value="Mon">Montag</option>
                                            <option value="Tue">Dienstag</option>
                                            <option value="Wed">Mittwoch</option>
                                            <option value="Thu">Donnerstag</option>
                                            <option value="Fri">Freitag</option>
                                            <option value="Sat">Samstag</option>
                                            <option value="Sun">Sonntag</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputStart">Beginn</label>

                                        <div class="form-group">
                                            <div class="input-group date" id="datepicker12" data-target-input="nearest">
                                                <input id="inputStart" name="inputStart" type="text"
                                                       class="form-control datetimepicker-input tobeHidden"
                                                       data-target="#datepicker12" required/>
                                                <div class="input-group-append" data-target="#datepicker12"
                                                     data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-clock"></i></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputEnd">Ende</label>
                                        <div class="form-group">
                                            <div class="input-group date" id="datepicker13" data-target-input="nearest">
                                                <input id="inputEnd" name="inputEnd" type="text"
                                                       class="form-control datetimepicker-input tobeHidden"
                                                       data-target="#datepicker13" required/>
                                                <div class="input-group-append" data-target="#datepicker13"
                                                     data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-clock"></i></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputDozent">Dozent/in</label>
                                        <select id="inputDozent" class="form-control">
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputSubject">Fach</label>
                                        <select id="inputSubject" class="form-control select2">
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="inputSchoolClass">Klassen</label>
                                        <select id="inputSchoolClass" class="form-control select2" multiple>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm">
                                <div class="form-group">
                                    <label for="inputSubtitle">Untertitel</label>
                                    <input id="inputSubtitle" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="inputDesc">Inhalt</label>
                                    <textarea id="inputDesc" class="form-control" rows="5"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
             <div class="card">
                <div class="card-header" id="headingThree">
                    <h5 class="mb-0">
                        Unterrichtsdetails
                    </h5>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="merkmal_form">Unterrichtsform</label>
                            <select id="merkmal_form" class="form-control" onchange="changeUnterrichtsform()">
                                <option value="praesenz">Präsenz</option>
                                <option value="online">Online</option>
                                <option value="hybrid">Hybrid</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4" id="formgroup_program">
                            <label for="merkmal_programm">Programm</label>
                            <select id="merkmal_programm" class="form-control">
                                <option selected value="-1">Bitte auswählen...</option>
                                <option value="zoom">Zoom</option>
                                <option value="teams">Microsoft Teams</option>
                                <option value="skype">Skype</option>
                                <option value="bigbluebutton">BigBlueButton</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4" id="formgroup_id">
                            <label for="merkmal_meeting_id">ID</label>
                            <input type="text" id="merkmal_meeting_id" class="form-control">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="merkmal_device">Geräte</label>
                            <select id="merkmal_device" class="form-control">
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="merkmal_anrechnen">Anrechnen im Lehrplan</label>
                            <select id="merkmal_anrechnen" class="form-control">
                                <option value="1" selected>Ja</option>
                                <option value="0">Nein</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
           <!-- <div class="card">
                <div class="card-header" id="headingThree">
                    <h5 class="mb-0">
                        Teilnehmerbeschränkung
                    </h5>
                </div>
                <div class="card-body">
                </div>
            </div> -->
            <div class="card">
                <div class="card-header" id="headingThree">
                    <h5 class="mb-0">
                        Planungsoptionen
                    </h5>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputStartFirst">Begin der Wiederholung</label>
                            <div class="form-group">
                                <div class="input-group date" id="datepicker8" data-target-input="nearest">
                                    <input id="inputStartFirst" name="inputStartFirst" type="text"
                                           class="form-control datetimepicker-input tobeHidden"
                                           data-target="#datepicker8" required/>
                                    <div class="input-group-append" data-target="#datepicker8"
                                         data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputEndLast">Ende der Wiederholungen</label>
                            <div class="form-group">
                                <div class="input-group date" id="datepicker9" data-target-input="nearest">
                                    <input id="inputEndLast" name="inputEndLast" type="text"
                                           class="form-control datetimepicker-input tobeHidden"
                                           data-target="#datepicker9" required/>
                                    <div class="input-group-append" data-target="#datepicker9"
                                         data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="repeatType">Wiederholungs-Art</label>
                            <select id="repeatType" class="form-control">
                                <option value="never">Keine</option>
                                <option value="daily">Täglich</option>
                                <option value="weekly" selected>Wöchentlich</option>
                                <option value="monthly">Monatlich</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="intervall">Turnus</label>
                            <input type="number" class="form-control" id="intervall" placeholder="1">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                <button type="button" id="deletePlan" class="btn btn-danger">Löschen</button>
                <button type="button" id="updateplan" class="btn btn-primary">Änderungen speichern</button>
            </div>
        </div>
    </div>
</div>
