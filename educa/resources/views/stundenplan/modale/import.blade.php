<div class="modal fade" id="importModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Unterricht importieren</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/stundenplan/plan/{{ $selectKlasse->id }}/import" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="custom-file">
                        <input name="import" type="file" class="custom-file-input" id="customFile">
                        <label class="custom-file-label" for="customFile">Datei auswählen</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                    <button type="submit" class="btn btn-primary">Importieren</button>
                </div>
            </form>
        </div>
    </div>
</div>

