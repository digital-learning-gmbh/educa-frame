<div class="modal" tabindex="-1" role="dialog" id="modalPreview">
    <div class="modal-dialog modal-xl " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Datei-Vorschau</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <iframe id="previewFrame" style="width: 100%; height: calc(100vh - 200px); border: none;"></iframe>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Vorschau schließen</button>
            </div>
        </div>
    </div>
</div>

    <script>
        function loadPreview(id) {
            $('#previewFrame').attr('src', "/dokument/"+ id + "/view");
            $('#modalPreview').modal('show');
        }
    </script>
