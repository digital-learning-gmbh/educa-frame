<div class="modal fade" id="ordnerModal" role="dialog" aria-labelledby="ordnerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ordnerModalLabel">Neuer Ordner</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="dokument_form" class="form" method="POST" action="/dokument/folder" enctype = "multipart/form-data">
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
                        <label for="folder">Ordnername</label>
                        <input class="form-control" name="folder" type="text" id="folder" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Erstellen</button>
                </div>
            </form>
        </div>
    </div>
</div>
