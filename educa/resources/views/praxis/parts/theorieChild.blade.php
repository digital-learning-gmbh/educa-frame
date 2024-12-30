@foreach($einheiten as $module)
    <tr class="treegrid-{{ $module->id }} @if($module->lehrplan_einheit_id != null)  treegrid-parent-{{ $module->lehrplan_einheit_id }} @endif">
        <th>
            <input name="module[]" value="{{ $module->id }}" type="checkbox" @if(in_array($module->id,$lehrplanEinheitenIds)) checked @endif />
        </th>
        <td>{{ $module->name }}</td>
        <td>{{ $module->type }}</td>
        <td>{{ $module->getCredisChilds() }}</td>
        <td>{{ $module->getAnzahlChilds() }}</td>
        @if($module->fach != null)
            <td>{{ $module->fach->name }}</td>
        @else
            <td><i>Kein Fach hinterlegt</i></td>
        @endif
    </tr>
    @component('praxis.parts.theorieChild',[ "einheiten" => $module->childern(), "lehrplan" => $lehrplan, "lehrplanEinheitenIds" => $lehrplanEinheitenIds])
    @endcomponent
@endforeach
