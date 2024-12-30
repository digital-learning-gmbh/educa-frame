<h6 class="dropdown-header">Schüler</h6>
@foreach($result["schuler"] as $schuler)
<a href="/verwaltung/schulerlisten?student_id={{ $schuler->id }}" class="dropdown-item">{{ $schuler->firstname." ".$schuler->lastname }}</a>
@endforeach
<h6 class="dropdown-header">Räume</h6>
@foreach($result["raume"] as $raum)
    <a href="/verwaltung/stammdaten/raume/{{ $raum->id }}/edit" class="dropdown-item">{{ $raum->name }}</a>
@endforeach
<h6 class="dropdown-header">Dozenten</h6>
@foreach($result["dozenten"] as $dozent)
    <a href="/verwaltung/stammdaten/dozenten?teacher_id={{ $dozent->id }}" class="dropdown-item">{{ $dozent->displayName }}</a>
@endforeach
