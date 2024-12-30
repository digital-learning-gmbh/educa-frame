@extends('cloud.main')

@section('cloudContent')
    <div class="float-right mb-2">
    </div>
    <div class="clearfix"></div>
    <div id="react-cloud-editUser" jwt="{{ \Illuminate\Support\Facades\Session::get("jwt_token") }}"></div>

    <div class="modal fade" id="addUser" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Benutzer (Datenbank) hinzufügen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="/cloud/user/addUser" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <p>Bitte füllen Sie alle Felder aus:</p>
                        <div class="form-group">
                            <label>Login-Name (*)</label>
                            <input name="email" type="text" class="form-control" required autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label>Anzeigename (*)</label>
                            <input name="name" type="text" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Passwort (*)</label>
                            <input name="password_new" type="password" class="form-control" required>
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
