@extends('cloud.main')

@section('cloudContent')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Benutzer bearbeiten: {{ $editUser->name }}</h5>
        <h6 class="card-subtitle mb-2 text-muted">Hier haben Sie die Möglichkeit einen Cloud-User zu bearbeiten</h6>
        <div style="margin-bottom: 3px;">
            <div class="float-right">
               <!-- <a href="#" data-toggle="modal" data-target="#exampleModal" class="btn btn-primary">Hinzufügen</a>
-->
            </div>
            <div class="clearfix"></div>
        </div>
        <form method="POST">
            @csrf
            <div class="form-group">
                <label for="exampleFormControlInput1">Login-Name</label>
                <input readonly disabled type="email" class="form-control" id="exampleFormControlInput1" placeholder="name@example.com" value="{{ $editUser->email }}">
            </div>
            <div class="form-group">
                <label for="exampleFormControlInput1">Anzeigename</label>
                <input name="name" class="form-control" id="exampleFormControlInput1" placeholder="Test Nutzer" value="{{ $editUser->name }}">
            </div>
            <div class="form-group">
                <label for="exampleFormControlInput1">Datenschutz</label>
                <div class="form-group form-check">
                    <input name="agreedPrivacy" type="checkbox" class="form-check-input" id="privacyCheck1" @if($editUser->agreedPrivacy) checked @endif>
                    <label class="form-check-label" for="privacyCheck1">akzeptiert</label>
                 </div>
            </div>
            <div class="form-group">
                <label>Rollen</label>
                <select class="select2" id="roles" name="roles[]" multiple autocomplete="nope">
                    @foreach($permissions as $permission)
                        @if(!$permission->hasPermissionTo(\App\PermissionConstants::IS_MULTI_TENANT_USER) ||
                            $cloud_user->hasPermissionTo(\App\PermissionConstants::IS_MULTI_TENANT_USER))
                        <option value="{{ $permission->id }}" @if($editUser->hasRole($permission)) selected @endif>{{ $permission->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            @if($editUser->loginType == "eloquent")
            <div class="form-group">
                <label for="exampleFormControlInput1">Passwort zurücksetzen</label>
                <input name="password_new" type="password" class="form-control" id="exampleFormControlInput1"  autocomplete="nope">
            </div>
            @endif

            @if($cloud_user->hasPermissionTo(\App\PermissionConstants::IS_MULTI_TENANT_USER))
                <div class="form-group">
                    <label>Tenants (wenn kein Tenant angegeben ist, ist der Nutzer auf allen Tenants verfügbar)</label>
                    <select class="select2" id="tenants" name="tenants[]" multiple>
                        @foreach(\App\Models\Tenant::all() as $tenant)
                            <option value="{{ $tenant->id }}" @if(in_array($tenant->id,$editUser->tenants->pluck("id")->toArray())) selected @endif>{{ $tenant->name }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <div class="form-group">
                    <label>Tenants: {{ $editUser->tenants->pluck("name")->join(", ") }}
                    </label>
                </div>
            @endif

            @if($editUser->loginType == "eloquent")
                <a href="/cloud/user/{{ $editUser->id }}/delete" class="btn btn-outline-danger">Nutzer löschen</a>
            @endif
            <a href="/cloud/user/{{ $editUser->id }}/links" class="btn btn-info">Apps aktualisieren</a>
            <button type="submit" class="btn btn-primary">Speichern</button>
        </form>
    </div>
</div>


<h4 class="mt-2">Verknüpfte Apps</h4>
<div class="list-group">
    @foreach($editUser->getApps() as $app)
        <a href="#" class="list-group-item list-group-item-action" data-toggle="modal" data-target="#modal_{{ $app["appName"] }}">
            <img src="{{ $app["icon"] }}" class="rounded img-fluid appIcon float-left m-2" style="    border-radius: 1.25rem !important;
    height: 75px;
    width: 75px;">
            <div class="float-left">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1">{{ $app["name"] }}</h5>
                </div>
                <p class="mb-1">{{ $app["description"] }}</p>
            </div>
        </a>
        <div class="modal fade" id="modal_{{ $app["appName"] }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Account-Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Model</label>
                            <div class="col-sm-9">
                                <input type="text" readonly class="form-control-plaintext" value="{{ $app["account"]->model }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Login-ID</label>
                            <div class="col-sm-9">
                                <input type="text" readonly class="form-control-plaintext" value="{{ $app["account"]->loginId }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

@endsection
