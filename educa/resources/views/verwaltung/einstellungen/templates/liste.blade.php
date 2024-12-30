@extends('verwaltung.main')

@section('siteContent')
    <h3>{{ __('Vorlagen') }}</h3>
    <div class="card mt-2">
        <div class="card-body">
            <h5 class="card-title">Vorlagen</h5>
            <h6 class="card-subtitle mb-2 text-muted">Liste von Vorlagen, die sich im System befinden.</h6>
            <div style="margin-bottom: 3px;">
                <div class="float-right">
                    <a href="#" data-toggle="modal" data-target="#exampleModal" class="btn btn-primary">Hinzufügen/Importieren</a>
                </div>
                <div class="clearfix"></div>
            </div>
            <table id="table_id" class="data-table table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Letzte Änderung durch</th>
                    <th>Aktion</th>
                </tr>
                </thead>
                <tbody>
                @foreach($formulare as $formular)
                    <tr>
                        <td>{{ $formular->name }}</td>
                        @if($formular->user != null)
                        <td>{{ $formular->user->displayName }}</td>
                        @else
                            <td><i>Nutzer unbekannt / gelöscht</i></td>
                        @endif
                        <td>
                            <a href="/verwaltung/einstellungen/templates/{{$formular->id}}/edit" class="btn btn-xs btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="/verwaltung/einstellungen/templates/{{$formular->id}}/export" class="btn btn-xs btn-primary">
                                <i class="fas fa-file-export"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Template hinzufügen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" method="POST" action="{{route('template.create')}}" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>
                            Bezieht sich auf
                        </label>
                        <select class="form-control select2" name="formular_template_type_id">
                            @foreach(\App\FormularTemplateType::all() as $form_type)
                                <option value="{{ $form_type->id }}">{{ $form_type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="import">Template importieren</label> <br>
                        <input name="import" type="file" id="import">
                        <div class="upload-drop-zone" id="drop-zone">
                            Datei hier hin ziehen...
                        </div>
                    </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Erstellen</button>
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
@endsection
