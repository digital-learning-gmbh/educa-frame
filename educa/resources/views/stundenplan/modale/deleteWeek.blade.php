<div class="modal fade" id="deleteWeekModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Unterricht löschen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Diese Aktion würde den Unterricht der gesamten Woche löschen. Sind Sie sicher?</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                    <button type="submit" id="deleteWeekUnterricht" class="btn btn-danger">Löschen</button>
                </div>
        </div>
    </div>
</div>

