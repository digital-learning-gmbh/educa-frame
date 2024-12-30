@foreach($einheiten as $module)
    <tr class="treegrid-{{ $module->id }} @if($module->lehrplan_einheit_id != null)  treegrid-parent-{{ $module->lehrplan_einheit_id }} @endif">
        <td>{{ $module->name }}</td>
        <td>{{ $module->type }}</td>
        <td>{{ $module->getCredisChilds() }}</td>
        <td>{{ $module->getAnCredisChilds() }}</td>
        <td>{{ $module->getAnzahlChilds() }}</td>
        @if($module->fach != null)
            <td>{{ $module->fach->name }}</td>
        @else
            <td><i>Kein Fach hinterlegt</i></td>
        @endif
        <td>@if(\App\LehrplanGroups::find($module->profil_id)) <span class="badge badge-pill badge-primary" style="background-color: {{ \App\LehrplanGroups::find($module->profil_id)->color }}">{{ \App\LehrplanGroups::find($module->profil_id)->name }}</span> @endif</td>
        <td>
            <a href="/verwaltung/lehrplan/{{ $lehrplan->id }}/module/{{ $module->id }}" class="btn btn-xs btn-primary"><i class="fas fa-edit"></i></a>
            <a href="/verwaltung/lehrplan/{{ $lehrplan->id }}/module/{{ $module->id }}/delete" class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></a>
        </td>
    </tr>
    @component('verwaltung.lehrplan.childern_list',[ "einheiten" => $module->childern(), "lehrplan" => $lehrplan])
    @endcomponent
@endforeach
