<div class="row">
    <div class="col-sm">
        <div style="margin-bottom: 3px;">
            <div class="float-right">
                <a href="#" data-toggle="modal" data-target="#dokumentModal" class="btn btn-primary">Hochladen</a>
                <a href="#" data-toggle="modal" data-target="#ordnerModal" class="btn btn-primary">Neuer Ordner</a>
            </div>
            <div class="clearfix"></div>
        </div>
        <table id="table_id" class="tree table table-striped table-bordered">
            <thead>
            <tr>
                <th>Name</th>
                <th>Typ</th>
                <th>Größe</th>
                <th>zuletzt geändert</th>
                <th>Hochgeladen von</th>
                <th>Aktion</th>
            </tr>
            </thead>
            <tbody>
            @component('documents.childern_list',[ "documents" => $model->dokumente(isset($mainCategory) ?  $mainCategory : "haupt")])
            @endcomponent
            </tbody>
        </table>
    </div>
</div>

@include('documents.upload')
@include('documents.folder')
@include('documents.move')
@include('documents.preview')

@section('additionalScript')
    @parent
    <script>
        //Upload
        var dropZone = document.getElementById('drop-zone');
        var dokumentInput = document.getElementById('dokument')

        dropZone.addEventListener("drop", function(event) {
            event.stopPropagation();
            event.preventDefault();
            var selectedFile = event.dataTransfer.files;
            dokumentInput.files = selectedFile;
            dropZone.classList.remove("drop");
        }, false);

        dropZone.ondragover = dropZone.ondragenter = function(event) {
            event.preventDefault();
            dropZone.classList.add("drop");
        };

        dropZone.ondragleave = function(event) {
            dropZone.classList.remove("drop");
        };


        //Move
        var documentId = document.getElementById("documentid");
        var movelevel = document.getElementById("movelevel");
        function setId(id) {
            documentId.value = id;
            for (var i=0; i<movelevel.length; i++) {
                if (movelevel.options[i].value == id)
                    movelevel.options[i].disabled = true;
                else
                    movelevel.options[i].disabled = false;
            }
        }
    </script>
@endsection
