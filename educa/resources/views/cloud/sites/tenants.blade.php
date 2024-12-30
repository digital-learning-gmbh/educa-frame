@extends('cloud.main')

@section('cloudContent')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Tenants</h5>
            <h6 class="card-subtitle mb-2 text-muted">Übersichtsliste aller Tenants auf diesem System</h6>
            <div style="margin-bottom: 3px;">
                <div class="float-right">
                     <a href="#" data-toggle="modal" data-target="#addTenant" class="btn btn-primary">Tenant hinzufügen</a>
                </div>
                <div class="clearfix"></div>
            </div>
            <table class="data-table table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Domain</th>
                    <th>Name</th>
                    <th>Logo</th>
                    <th>Farbe</th>
                    <th>Lizenz</th>
                    <th>Aktion</th>
                </tr>
                </thead>
                <tbody>
                @foreach(\App\Models\Tenant::all() as $tenant)
                    <tr>
                        <td>{{ $tenant->domain }}</td>
                        <td>{{ $tenant->name }}</td>
                        <td> @if($tenant->logo != null)
                                <img src="/storage/images/tenants/{{ $tenant->logo }}" height="50" width="50" >
                            @endif</td>
                        <td>{{ $tenant->color }}</td>
                        <td>{{ $tenant->licence }}</td>
                        <td>
                            <a href="/cloud/tenants/{{ $tenant->id }}/edit" class="btn btn-xs btn-primary"><i class="fas fa-edit"></i></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>


    <div class="modal fade" id="addTenant" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Tenant hinzufügen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="/cloud/tenants/addTenant">
                    @csrf
                    <div class="modal-body">
                        <p>Bitte füllen Sie alle Felder aus:</p>
                        <div class="form-group">
                            <label>Name (*)</label>
                            <input name="name" type="text" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Domain (*)</label>
                            <input name="domain" type="text" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Lizenz (*)</label>
                            <input name="license" type="text" class="form-control" required>
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
