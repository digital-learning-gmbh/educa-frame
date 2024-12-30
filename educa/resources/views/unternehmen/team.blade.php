@extends('layouts.unternehmen')

@section('appContent')
    <div class="container">
        <h2>Team</h2>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Zugriffsberechtigte Personen</h5>
                <p>Verwalten Sie, welche Personen Zugriff zu StuPla haben.</p>
                <table id="table_id" class="data-table table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>Vorname</th>
                        <th>Nachname</th>
                        <th>Bereich</th>
                        <th>Aktion</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            Test
                        </td>
                        <td>
                            Muster
                        </td>
                        <td>
                            Station 1
                        </td>
                        <td>

                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
