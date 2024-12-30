@extends('cloud.main')

@section('cloudContent')
<style>
    .sticky-col {
  position: -webkit-sticky;
  position: sticky;
  background-color: white;
}

.first-col {
  width: 300px;
  min-width: 300px;
  max-width: 300px;
  left: 0px;
  z-index: 4;
}

.second-col {
  width: 150px;
  min-width: 150px;
  max-width: 150px;
  left: 100px;
}

.wrapper {
  position: relative;
  overflow: auto;
  border: 1px solid #dee2e6;
  white-space: nowrap;
  height: 100vh;
}
th {
  position: -webkit-sticky;
  position: sticky;
  top: 0;
  z-index: 1;
  background-color: white;
}

    </style>
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Rechte und Rollen</h5>
        <h6 class="card-subtitle mb-2 text-muted">Übersichtsliste aller Rollen und der jeweiligen Rechte</h6>
        <div style="margin-bottom: 3px;">
            <div class="float-right">
                <a href="#" data-toggle="modal" data-target="#addRole" class="btn btn-primary">Rolle hinzufügen</a>
            </div>
            <div class="clearfix"></div>
        </div>
        <form method="POST" class="wrapper">
            @csrf
        <table id="table_id" class="table table-striped table-bordered">
            <thead>
            <tr>
                <th class="sticky-col first-col" style="z-index:10;">Recht</th>
                @foreach($rols as $role)
                    <th>{{ $role->name }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach($rights as $right)
                <tr>
                    <td class="sticky-col first-col">{{ $right->name }}</td>
                    @foreach($rols as $role)
                    <td>
                        <input name="permission_{{ $role->id }}_{{ $right->id }}" type="checkbox" @if($role->hasPermissionTo($right)) checked @endif >
                    </td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
            <button class="btn btn-success">Speichern</button>
        </form>
    </div>
</div>


<div class="modal fade" id="addRole" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Rolle hinzufügen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="/cloud/rights/addRole">
                @csrf
                <div class="modal-body">
                    <p>Bitte füllen Sie alle Felder aus:</p>
                    <div class="form-group">
                        <label>Rollen-Name (*)</label>
                        <input name="name" type="text" class="form-control" required>
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
