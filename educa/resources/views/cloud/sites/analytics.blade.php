@extends('cloud.main')

@section('cloudContent')

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Analytics</h5>
            <h6 class="card-subtitle mb-2 text-muted">Legen Sie fest, welche Rollenzugriff auf welche Berichte haben</h6>
            <div style="margin-bottom: 3px;">
                <div class="float-right">
                    <!-- <a href="#" data-toggle="modal" data-target="#exampleModal" class="btn btn-primary">Hinzufügen</a>
     -->
                </div>
                <div class="clearfix"></div>
            </div>
            <form method="POST">
                @csrf
                <table id="table_id" class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>Bericht</th>
                        <th>Rollen, die Zugriff haben</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($reports as $report)
                        <tr>
                            <td>{{ $report->name }}</td>
                            <td><select class="select2 form-control" multiple name="rights_{{ $report->id }}[]" style="max-width: 600px;">
                                    @foreach($rols as $role)
                                        <option value="{{ $role->id }}" @if(\Illuminate\Support\Facades\DB::table("report_role")->where([
    "role_id" => $role->id,"report_id" => $report->id
])->exists()) selected @endif >{{ $role->name }}</option>
                                    @endforeach
                                </select></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <button class="btn btn-success">Speichern</button>
            </form>
        </div>
    </div>

@endsection
