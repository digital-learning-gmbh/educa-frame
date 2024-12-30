<style>
    .list-group-item {
        user-select: none;
    }

    .list-group input[type="checkbox"] {
        display: none;
    }

    .list-group input[type="checkbox"] + .list-group-item {
        cursor: pointer;
    }

    .list-group input[type="checkbox"] + .list-group-item:before {
        content: "\2713";
        color: transparent;
        font-weight: bold;
        margin-right: 1em;
    }

    .list-group input[type="checkbox"]:checked + .list-group-item {
        background-color: #0275D8;
        color: #FFF;
    }

    .list-group input[type="checkbox"]:checked + .list-group-item:before {
        color: inherit;
    }

    .list-group input[type="radio"] {
        display: none;
    }

    .list-group input[type="radio"] + .list-group-item {
        cursor: pointer;
    }

    .list-group input[type="radio"] + .list-group-item:before {
        content: "\2022";
        color: transparent;
        font-weight: bold;
        margin-right: 1em;
    }

    .list-group input[type="radio"]:checked + .list-group-item {
        background-color: #0275D8;
        color: #FFF;
    }

    .list-group input[type="radio"]:checked + .list-group-item:before {
        color: inherit;
    }
</style>
<div class="modal fade" id="createReiter" tabindex="-1" role="dialog" aria-labelledby="createReiterTitel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createReiterTitle">Registerkarte hinzufügen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="createReiterForm" method="POST" action="/social/group/{{ $group->id }}/addReiter">
                @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label for="reiter">Füge deine Lieblings-Apps und Dateien als Registerkarte in die Menüleiste der Gruppe hinzu</label>
                    <div class="list-group">
                        @foreach($possibleReiters as $reiter)
                        <input type="radio" name="reiter" value="{{ $reiter->id }}" id="reiter{{ $reiter->id }}" required />
                        <label class="list-group-item" for="reiter{{ $reiter->id }}">{!! $reiter->icon !!} {{ $reiter->name }}</label>
                        @endforeach
                    </div>
                </div>
                </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                <button type="submit" id="reiterCreate" class="btn btn-primary">Reiter hinzufügen</button>
            </div></form>
        </div>
    </div>
</div>
