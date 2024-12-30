@extends('klassenbuch.basic')

@section('siteContent')
    <div class="col-md-10">
        <div class="card">
            <div class="card-header" id="dokumentHeading">
                <b>Noten und Prüfungen</b>
            </div>
                <div class="card-body" id="dokumentCard">
                    <p>Übersicht über geplante Prüfungen der Planungsgruppe. Prüfungen können über die Prüfungsverwaltung erstellt und veändert werden.</p>
                    <table id="table_id" class="data-table table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>Modul</th>
                            <th>Prüfungsart</th>
                            <th>Datum</th>
                            <th>Prüfungsteile</th>
                            <th>Aktion</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
        </div>

@endsection

