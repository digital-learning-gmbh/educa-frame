<div class="modal fade" id="moveModal" role="dialog" aria-labelledby="moveModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="moveModalLabel">Datei Verschieben</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="dokument_form" class="form" method="POST" action="/dokument/move" enctype = "multipart/form-data">
                @csrf
                <input name="id" id="documentid" type="hidden" value="">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="movelevel">Übergeordneter Ordner</label>
                        <select class="form-control select2" id="movelevel" name="level" required>
                            <option value="0">Hauptordner</option>
                            @foreach($model->dokumente() as $dokument)
                                @if($dokument->type == "folder")
                                    <option value="{{$dokument->id}}">{{$dokument->name}}</option>
                                @endif
                            @endforeach
                        </select>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Verschieben</button>
                </div>
            </form>
        </div>
    </div>
</div>
