
<div class="card">
    <div class="card-header">
        <b>Praxisteilnahme am {{ $date->format("d.m.Y") }} </b>
        <a href="#" onclick="savePraxisTeilnahme('{{ $date->format("d.m.Y") }}')" class="btn btn-xs btn-success float-right"><i class="fas fa-check"></i></a>
    </div>

    <form id="anwesenheitForm">
        <table id="anwesenheitTable" class="data-table table table-striped table-bordered">
            <thead>
            <tr>
                <th>Vorname</th>
                <th>Nachname</th>
                <th>Schule</th>
                <th>Anwesenheit</th>
            </tr>
            </thead>
            <tbody>
                @foreach($schulers as $schuler)
                <tr>
                    <td>{{ $schuler->firstname }}</td>
                    <td>{{ $schuler->lastname }}</td>
                    <td>@foreach($schuler->schulen as $schule){{ $schule->name }} @endforeach</td>
                    <td>
                        <select name="{{ $schuler->id }}" class="select2 form-control" required>
                            <option value="anwesend"@if($schuler->teilnahme == "anwesend")selected @endif >Anwesend</option>
                            <option value="unentschuldigt"@if($schuler->teilnahme == "unentschuldigt")selected @endif >Unentschuldigt</option>
                            <option value="krank"@if($schuler->teilnahme == "krank")selected @endif >Krank</option>
                        </select>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </form>
</div>
