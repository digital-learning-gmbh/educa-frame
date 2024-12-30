
<div class="modal fade" id="dokumentModal" role="dialog" aria-labelledby="dokumentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dokumentModalLabel">Dokument hochladen</h5>
                <button id="closeUploadModal" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="dokument_form" class="form" method="POST" action="/dokument/upload" enctype = "multipart/form-data">
                @csrf
                <input name="id" type="hidden" value="{{ $model->id }}">
                <input name="model_type" type="hidden" value="{{ $type }}">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="level">Übergeordneter Ordner</label>
                        <select class="form-control select2" id="level" name="level" required>
                            <option value="0">Hauptordner</option>
                            @foreach($model->dokumente() as $dokument)
                                @if($dokument->type == "folder")
                                    <option value="{{$dokument->id}}">{{$dokument->name}}</option>
                                @endif
                            @endforeach
                        </select>
                        <br />
                        <input name="dokument" type="file" id="dokument">
                        <div class="upload-drop-zone" id="drop-zone">
                            Datei hier hin ziehen...
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="uploadDocument" type="submit" class="btn btn-primary">Hochladen</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <style>
    .upload-drop-zone {
        height: 200px;
        border-width: 2px;
        margin-bottom: 20px;
        color: #ccc;
        border-style: dashed;
        border-color: #ccc;
        line-height: 200px;
        text-align: center
    }
    .upload-drop-zone.drop {
        color: #222;
        border-color: #222;
    }
</style>
