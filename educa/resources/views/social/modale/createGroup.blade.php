<nav class="navbar navbar-dark bg-dark navbar-expand-lg">

    <a class="navbar-brand" href="#">
        Neue Gruppe erstellen
    </a>
    <div class="collapse navbar-collapse"  id="navbarSupportedContent">
    </div>
</nav>
<div class="container">
            <form id="createGroupForm" method="POST" action="/social/createGroup">
                @csrf
            <div class="modal-body">
                <input type="hidden" name="members" id="members" value="">
                <div class="form-group">
                    <label for="name">Gruppenname</label>
                    <input name="name" required type="text" class="form-control" id="name" aria-describedby="emailHelp" placeholder="Gruppenname">
                    <small id="emailHelp" class="form-text text-muted">Der Gruppenname kann später auch geändert werden.</small>
                </div>

                <label for="name">Mitglieder</label>
                <small id="emailHelp" class="form-text text-muted">Wähle die Mitglieder der Gruppe aus, die hinzugefügt werden sollen.</small>

                <table id="searchUser" class="table table-striped table-bordered" style="width: 100%;">
                <thead>
                <tr>
                    <th></th>
                    <th style="display: none">ID</th>
                    <th>Name</th>
                    <th>E-Mail</th>
                </tr>
                </thead>
                <tbody>
                @foreach($cloudIds as $cloudid)
                    @if($cloudid->id != $cloud_user->id)
                        <tr>
                            <td></td>
                            <td style="display: none">{{ $cloudid->id }}</td>
                            <td>{{ $cloudid->name }}</td>
                            <td>{{ $cloudid->email }}</td>
                        </tr>
                @endif
                @endforeach
            </table></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                <button type="submit" style="display: none" id="suCreate" class="btn btn-primary">Weiter</button>
                <button type="button" onclick="createGroup();" class="btn btn-primary">Weiter</button>
            </div></form>
</div>
<script>
    function createGroup()
    {
        var selected = window.searchUserTable.rows( { selected: true } )[0];
        $('#members').val(selected.join(","));
        $('#suCreate').click();
    }
</script>
