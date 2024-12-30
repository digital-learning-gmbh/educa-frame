<div class="modal fade" id="createMessage" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Nachricht senden</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="createGroupForm" method="POST" action="/social/createMessage">
                @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label for="name">Benutzer</label>
                    <select id="teilnehmer" name="benutzer[]" multiple required>

                    </select>
                    <small id="emailHelp" class="form-text text-muted">An welchen Personen, soll die Nachricht geschickt werden.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                <button type="submit" id="suCreate" class="btn btn-primary">Weiter</button>
            </div></form>
        </div>
    </div>
</div>


