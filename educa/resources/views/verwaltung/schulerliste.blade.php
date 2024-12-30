@extends('verwaltung.main')

@section('siteContent')
    <div id="react-administration-base-data-students" jwt="{{ \Illuminate\Support\Facades\Session::get("jwt_token") }}" school_id="{{ $global_school->id }}" year_id="{{ $global_year->id }}" draft_id="{{ $global_entwurf->id }}"></div>

    <!-- Modal to add a new teilnehmer -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">SchülerInnen hinzufügen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="/verwaltung/schulerlisten/add" method="POST">
                    @csrf
                <div class="modal-body">
                    <label>Vorname</label>
                    <input type="text" class="form-control" id="firstname" name="firstname" required>
                    <label>Nachname</label>
                    <input type="text" class="form-control" id="lastname" name="lastname" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Speichern</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="excelImportModal" tabindex="-1" role="dialog" aria-labelledby="excelImportModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="excelImportModalLabel">SchülerInnen aus Excel-Liste importieren</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="/verwaltung/schulerlisten/excelImport" method="POST" enctype = "multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <label>Präfix für die Wahlpflichtfächer</label><br>
                        <input name="wahl_prefix" class="form-control" placeholder="Präfix für die Wahlpflichtfächer..."><br>
                        <label>Excel-Datei</label><br>
                        <input name="excelfile" type="file" id="excelfile">
                        <div class="upload-drop-zone" id="drop-zone">
                            Datei hier hin ziehen...
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Importieren</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('additionalScript')
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

    <script>
        //Upload
        var dropZone = document.getElementById('drop-zone');
        var dokumentInput = document.getElementById('excelfile')

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

        $('#schulerliste').each(function(){
            $(this).DataTable({
                colReorder: true,
                dom: 'fBrtlip',
                buttons: [ 'searchPanes','print','excel', 'pdf', 'colvis'
                ],
                searchPanes:{
                    columns:[0,1,2,3,4,5],
                    layout: 'columns-6'
                },
                columnDefs:[
                    {
                        searchPanes:{
                            show: true,
                        },
                        targets: [0,1,2,3,4,5],
                    }
                ],
                language : {
                    url: "/js/german.json",
                    searchPanes: {
                        clearMessage: 'Zurücksetzen',
                        collapse: {0: 'Suchoptionen', _: 'Suchoptionen (%d)'}
                    }
                },
                ajax: '/verwaltung/schulerlisten/ajax/',
                columns: [
                    { data: 'lastname' },
                    { data: 'firstname' },
                    { data: 'email' },
                    { data: 'schule' },
                    { data: 'klasse' },
                    { data: 'praxispartner' },
                    { data: 'action' },
                ],
                stateSave:true
            });
        });
    </script>
    @endsection
