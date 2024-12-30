@extends('verwaltung.schuljahr.main_erweiterte')

@section('erweiterung')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Fehlzeiten</h5>
            <h6 class="card-subtitle mb-2 text-muted">Fehlzeiten der Schule im Schuljahr für das digitale Klassenbuch</h6>
           <!-- <div style="margin-bottom: 3px;">
                <div class="float-right">
                    <a href="#" data-toggle="modal" data-target="#exampleModal" class="btn btn-primary">Hinzufügen</a>
                    <a href="/verwaltung/stammdaten/dozenten/import" class="btn btn-warning">Synchronisiere mit Vorsystem</a>
                </div>
                <div class="clearfix"></div>
            </div> -->
            <table id="table_id" class="data-table table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Bezeichnung</th>
                    <th>Prozentuale Anrechnung</th>
                    <th>Standard</th>
                    <th>Aktion</th>
                </tr>
                </thead>
                <tbody>
                @foreach($schuljahr->fehlzeiten as $fehlzeit)
                    <tr>
                        <td>{{ $fehlzeit->name }}</td>
                        <td>{{ $fehlzeit->percent }}</td>
                        <td>{{ $fehlzeit->standart }}</td>
                        <td>
                           <!-- <a href="/verwaltung/stammdaten/dozenten/{{$fehlzeit->id}}/delete" class="btn btn-xs btn-danger">
                                <i class="fas fa-trash"></i>
                            </a> -->
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
