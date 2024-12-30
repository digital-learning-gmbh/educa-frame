@extends('verwaltung.main')

@section('siteContent')
    <h3>{{ \App\Providers\AppServiceProvider::schoolTranslation('facher', 'Fächer') }}</h3>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ \App\Providers\AppServiceProvider::schoolTranslation('facher', 'Fächer') }}</h5>
            <h6 class="card-subtitle mb-2 text-muted">Verwalten Sie die {{ \App\Providers\AppServiceProvider::schoolTranslation('facher', 'Fächer') }} der Schule.</h6>
            <div style="margin-bottom: 3px;">
                <div class="float-right">
                    <a href="#" data-toggle="modal" data-target="#addFach" class="btn btn-primary">Hinzufügen</a>

                </div>
                <div class="clearfix"></div>
            </div>
            <table id="table_id" class="data-table table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>typische Dauer (UE)</th>
                    <th>Abkürzung</th>
                    <th>Farbe</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach(\App\Fach::all() as $fach)
                    <tr>
                        <td>{{ $fach->name }}</td>
                        <td>{{ $fach->duration }}</td>
                        <td>{{ $fach->abk }}</td>
                        <td><div style="width: 20px; height: 20px; background-color: {{ $fach->color }}"></div></td>
                        <td>
                            <a href="/verwaltung/stammdaten/fach/{{ $fach->id }}" class="btn btn-xs btn-primary"><i class="fas fa-edit"></i></a>
                            <a href="/verwaltung/stammdaten/fach/{{ $fach->id }}/delete" class="btn btn-xs btn-danger"><i class="fas fa-trash-alt"></i></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="addFach" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Fach hinzufügen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="/verwaltung/stammdaten/fach/addFach">
                    @csrf

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Name</label>
                            <input name="name" type="text" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Abkürzung</label>
                            <input name="abk" type="text" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>übliche Dauer (UE)</label>
                            <input name="duration" type="text" class="form-control" required value="1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                        <button type="submit" class="btn btn-primary">Erstellen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
