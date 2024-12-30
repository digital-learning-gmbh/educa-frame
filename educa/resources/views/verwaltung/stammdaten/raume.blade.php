<h5 class="card-title">Räume</h5>
<h6 class="card-subtitle mb-2 text-muted">Übersichtsliste aller Räume</h6>
<div style="margin-bottom: 3px;">
    <div class="float-right">
        <a href="#" data-toggle="modal" data-target="#exampleModal" class="btn btn-primary">Hinzufügen</a>

    </div>
    <div class="clearfix"></div>
</div>
<table id="table_id" class="data-table table table-striped table-bordered">
    <thead>
    <tr>
        <th>Raumbezeichnung</th>
        <th>Gebäude</th>
        <th>Schule</th>
        <th>max. Teilnehmeranzahl</th>
        <th>Bemerkungen</th>
        <th>Ausstattung</th>
        <th>Aktion</th>
    </tr>
    </thead>
    <tbody>
        @foreach($schule->raume as $raum)
            <tr>
                <td>{{ $raum->name }}</td>
                <td>{{ $raum->gebaeude }}</td>
                <td>@foreach($raum->schulen as $sch) <span class="badge badge-secondary">{{ $sch->name }}</span> @endforeach</td>
                <td>{{ $raum->size }}</td>
                <td>{{ $raum->bemerkungen }}</td>
                <td>{{ $raum->ausstattung }}</td>
                <td>

                    <a href="/verwaltung/stammdaten/raume/{{$raum->id}}/edit" class="btn btn-xs btn-primary">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="/verwaltung/stammdaten/raume/{{$raum->id}}/delete" class="btn btn-xs btn-danger">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

    <!-- Modal to add a new raum -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Raum hinzufügen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form" method="POST" action="{{route('raum.create')}}" enctype = "multipart/form-data">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="form-group">
                            <!--<div class="row">
                                <div class="col-3">
                                    <label>Titel</label>
                                    <select class="form-control" id="title" name="title">
                                        <option value="notitle" selected="selected"></option>
                                        <option value="dr">Dr.</option>
                                        <option value="prof">Prof. Dr.</option>
                                        <option value="dipl">Dipl. Ing.</option>
                                    </select>
                                </div>
                                <div class="col-3">
                                    <label>Anrede</label>
                                    <select class="form-control" id="anrede" name="anrede">
                                        <option value="0">Herr</option>
                                        <option value="1">Frau</option>
                                        <option value="2">keine Angabe</option>
                                    </select>
                                </div>
                            </div>-->
                            <label>Bezeichnung</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <label>Gebäude</label>
                            <input type="text" class="form-control" id="gebaeude" name="gebaeude">
                            <label>Größe</label>
                            <input type="number" class="form-control" id="size" name="size" min="1" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Speichern</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

