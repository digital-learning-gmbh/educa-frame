<div class="card" style="margin-top: 5px;">
    <div class="card-header"><b>Lerninhalte</b></div>
    <div class="card-body">
        @if($selectKlasse->getLehrplan->count() == 0)
            <div class="alert alert-danger fade show" role="alert">
                <strong>Achtung!</strong> Dieser Klasse ist aktuell kein Curriculum zugeordnet. Dadurch können keine Lerninhalte geplant werden. Manuell können jedoch Unterrichtseinheiten in der Stundenplanung hinterlegt werden.
            </div>
        @else
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                Bitte wählen Sie aus den Lernplänen, die dieser Klasse zugeordnet sind, die Module aus, die in diesem Abschnitt unterricht werden sollen.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <form action="/praxis/sections/{{ $selectKlasse->id }}/{{ $selectAbschnitt->id }}/saveTheorie" method="POST">
            @csrf
            @foreach ($selectKlasse->getLehrplan as $lehrplan)
                <h4>{{ $lehrplan->name }}</h4>

                <table class="tree table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Name</th>
                        <th>Typ</th>
                        <th>Credits / Wertungsfaktor</th>
                        <th>SOLL (UE)</th>
                        <th>Fach</th>
                    </tr>
                    </thead>
                    <tbody>
                    @component('praxis.parts.theorieChild',[ "einheiten" => $lehrplan->lehreinheiten("12"), "lehrplan" => $lehrplan, "lehrplanEinheitenIds" => $lehrplanEinheitenIds])
                    @endcomponent
                    </tbody>
                    <!--<tr class="treegrid-2 treegrid-parent-1">
                        <td>Node 1-1</td><td>Additional info</td>
                    </tr>
                    <tr class="treegrid-3 treegrid-parent-2">
                        <td>Node 1-2</td><td>Additional info</td>
                    </tr>
                    <tr class="treegrid-4 treegrid-parent-1">
                        <td>Node 1-2-1</td><td>Additional info</td>
                    </tr> -->
                </table>
            @endforeach
            <div class="form-group">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                <button type="submit" class="btn btn-primary">Speichern</button>
            </div>
        </form>
    </div>
</div>
