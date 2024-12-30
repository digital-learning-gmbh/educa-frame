<div class="modal fade" id="addMembersModal" tabindex="-1" role="dialog" aria-labelledby="addMembersModalTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMembersModalTitle">Mitglieder hinzufügen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addMembersForm" method="POST" action="/social/group/{{ $group->id }}/settings/addMembers">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="members" id="members" value="">

                    <label for="name">Mitglieder</label>
                    <small id="emailHelp" class="form-text text-muted">Wähle die Mitglieder aus, die der Gruppe hinzugefügt werden sollen.</small>

                    <table id="searchUserAdd" class="table table-striped table-bordered" width="100%">
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
                            <tr>
                                <td></td>
                                <td style="display: none">{{ $cloudid->id }}</td>
                                <td>{{ $cloudid->name }}</td>
                                <td>{{ $cloudid->email }}</td>
                            </tr>
                        @endforeach
                    </table></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                    <button type="submit" style="display: none" id="suAdd" class="btn btn-primary">Hinzufügen</button>
                    <button type="button" onclick="addMembers();" class="btn btn-primary">Hinzufügen</button>
                </div></form>
        </div>
    </div>
</div>

<script>
    function addMembers()
    {
        var selected = [];
        window.searchUserAddTable.rows( { selected: true } ).data().each(row => selected.push(row[1]));
        $('#members').val(selected.join(","));
        $('#suAdd').click();
    }
</script>
