@extends('verwaltung.preiskalkulator.main')

@section('siteContent')
    <h3>{{ $preiskalkulator->name }}</h3>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ $preiskalkulator->name }} / {{ $preiskalkulator->name_en }} @if($preiskalkulator->required)*@endif</h5>
            <p class="card-text">Hier haben Sie die Möglichkeit Optionen aus dem Preiskalkulator zu entfernen oder weitere hinzufügen</p>
        </div>
    </div>
    @php
    $parent_id = 0;
    $url = "/verwaltung/preiskalkulator/?id=".$preiskalkulator->id;
    @endphp
    <div class="row mt-2">
    @foreach($preiskalkulator->kategorien as $kategorie)
            <div class="col-3">
                @php
                    $selected_id = \Illuminate\Support\Facades\Request::input("kategorie_".$kategorie->id);
                @endphp
                <h5>{{ $kategorie->name }} @if($parent_id != -1)<a href="#" onclick="addAuswahl({{ $parent_id }}, {{ $kategorie->id }})" class="btn btn-primary"><i class="fas fa-plus"></i></a>@endif</h5>
        <div class="list-group">
            @foreach($kategorie->auswahl($parent_id) as $auswahl)
            <a href="#" onclick="openUrl('{{ $url."&kategorie_".$kategorie->id."=".$auswahl->id }}')" class="list-group-item list-group-item-action @if($selected_id == $auswahl->id) active @endif">
                {{ $auswahl->name }}
                <span onclick="event.stopPropagation();editAuswahl({{ $auswahl->id }},'{{ $auswahl->name }}','{{ $auswahl->name_en }}','{{ $auswahl->preis }}','{{ $auswahl->order }}','{{ $auswahl->description }}','{{ $auswahl->image }}','{{ $auswahl->preis_1 }}','{{ $auswahl->preis_2 }}')" class="btn btn-primary"><i class="fas fa-pencil-alt"></i></span>
                <span onclick="event.stopPropagation();copyAuswahl({{ $auswahl->id }})"
                      class="btn btn-warning"><i class="fas fa-copy"></i></span>
                <span onclick="event.stopPropagation();deleteAuswahl({{ $auswahl->id }})" class="btn btn-danger"><i class="fas fa-trash-alt"></i></span>
            </a>
            @endforeach
          </div>
                @php
                    $parent_id = \Illuminate\Support\Facades\Request::input("kategorie_".$kategorie->id,-1);
                    $url .= "&kategorie_".$kategorie->id."=".$parent_id;
                @endphp
            </div>
    @endforeach
    </div>
@endsection

<div class="modal fade" id="modalAuswahl" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Preisauswahl</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" enctype="multipart/form-data">
            <div class="modal-body">
                @csrf
                <input type="hidden" name="auswahl_id" id="auswahl_id" value="-1">
                <input type="hidden" id="parent_id" name="parent_id">
                <input type="hidden" id="kategorie_id" name="kategorie_id">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="name_en">Name (en)</label>
                    <input type="text" class="form-control" id="name_en" name="name_en" required>
                </div>
                <div class="form-group">
                    <label for="order_id">Position</label>
                    <input type="number" class="form-control" id="order_id" name="order"  required>
                </div>
                <div class="form-group">
                    <label for="preis">Preis</label>
                    <input type="number" class="form-control" id="preis" name="preis"   pattern="[0-9]+([\.,][0-9]+)?" step="0.01">
                    <small id="emailHelp" class="form-text text-muted">Bitte geben Sie ein Preis nur an, wenn es die letzte Stufe des Preiskalkulators ist.</small>
                </div>
                <div class="form-group">
                    <label for="preis">Kaution</label>
                    <input type="number" class="form-control" id="preis_2" name="preis_2"  pattern="[0-9]+([\.,][0-9]+)?" step="0.01">
                    <small id="emailHelp" class="form-text text-muted">Bitte geben Sie den Preis in der obersten Stufe an</small>
                </div>
                <div class="form-group">
                    <label for="preis">Verwaltungsgebühr</label>
                    <input type="number" class="form-control" id="preis_1" name="preis_1" pattern="[0-9]+([\.,][0-9]+)?" step="0.01">
                    <small id="emailHelp" class="form-text text-muted">Bitte geben Sie den Preis in der obersten Stufe an</small>
                </div>
                <div class="form-group">
                    <label for="description">Beschreibung</label>
                    <textarea  class="form-control" id="description" name="description"></textarea>
                </div>
                <div class="form-group">
                    <label for="image">Bild hochladen</label> <br>
                    <input name="image" type="file" id="image">
                     <div class="upload-drop-zone" id="drop-zone">
                        Datei hier hin ziehen...
                    </div>
                </div>
                <!--<div class="form-group hideAdd">
                    <label>Bild</label>
                    <img id="previewImage" class="img-fluid" src=""  style="width: 100%;" />
                </div>-->

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                <button type="submit" class="btn btn-primary">Speichern</button>
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

<script>
    function addAuswahl(parent_id, kategorie_id) {
        $('#modalAuswahl').modal('show');
        $('#auswahl_id').val("-1");
        $('#parent_id').val(parent_id);
        $('#kategorie_id').val(kategorie_id);
        //$('.hideEdit').show();
    }

    function copyAuswahl(auswahl_id) {
        window.location.href = "/verwaltung/preiskalkulator/copyAuswahl?id=" + auswahl_id;
    }

    function deleteAuswahl(auswahl_id) {
        bootbox.confirm({message:"Soll der Eintrag wirklich gelöscht werden?", locale: "de", callback: function(result){
                if(result) {
                    window.location.href = "/verwaltung/preiskalkulator/deleteAuswahl?id=" + auswahl_id;
                }
            }})
    }
    function openUrl(url) {
        window.location.href = url;
    }

    function editAuswahl(auswahl_id, name, name_en, preis, order, description, image, preis_1, preis_2) {
        $('#modalAuswahl').modal('show');
        $('#auswahl_id').val(auswahl_id);
        $('#name').val(name);
        $('#name_en').val(name_en);
        $('#preis').val(preis);
        $('#preis_1').val(preis_1);
        $('#preis_2').val(preis_2);
        $('#order_id').val(order);
        $('#description').val(description);
        //$('.hideEdit').hide();
        //$('#previewImage').attr('src','/'+image);
    }

    //Upload
    var dropZone = document.getElementById('drop-zone');
    var dokumentInput = document.getElementById('image');

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
</script>
