@extends('cloud.main')

@section('cloudContent')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Gruppen</h5>
            <h6 class="card-subtitle mb-2 text-muted">Übersichtsliste aller Gruppen im System</h6>
            <div style="margin-bottom: 3px;">
                <div class="float-right">
                    <!-- <a href="#" data-toggle="modal" data-target="#exampleModal" class="btn btn-primary">Hinzufügen</a>
     -->
                </div>
                <div class="clearfix"></div>
            </div>
            <table id="table_id" class="data-table table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Gruppe</th>
                    <th>Anzahl Mitglieder</th>
                    <th>Archiviert</th>
                    <th>Aktion</th>
                </tr>
                </thead>
                <tbody>
                @foreach($groups as $group)
                    <tr>
                        <td>{{ $group->name }}</td>
                        <td>{{ $group->members()->count() }}</td>
                        <td>{!! $group->isArchived() ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times"></i>' !!}</td>
                        <td>
                            @if($group->isArchived())
                            <a href="/cloud/groups/{{ $group->id }}/unarchive" class="btn btn-xs btn-primary">Dem Archiv entnehmen</a>
                                @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
