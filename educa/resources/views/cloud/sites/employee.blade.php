@extends('cloud.main')

@section('cloudContent')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Mitarbeiterliste</h5>
        <h6 class="card-subtitle mb-2 text-muted">Übersichtsliste aller Mitarbeiter, die mindestens die Rolle Verwaltung haben.</h6>
        <div style="margin-bottom: 3px;">
            <div class="float-right">
               <!-- <a href="#" data-toggle="modal" data-target="#exampleModal" class="btn btn-primary">Hinzufügen</a>
-->
            </div>
            <div class="clearfix"></div>
        </div>
        <table id="user_table" class="table table-striped table-bordered">
            <thead>
            <tr>
                <th>E-Mail / Login</th>
                <th>Name</th>
                <th>Rollen</th>
                <th>Aktion</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
@endsection

@section("additionalScript")
    <script>

        $('#user_table').each(function() {
            $(this).DataTable({
                colReorder: true,
                dom: 'fBrtlip',
                buttons: ['searchPanes', 'print', 'excel', 'pdf', 'colvis'
                ],
                searchPanes: {
                    columns: [0, 1, 2],
                    layout: 'columns-6'
                },
                columnDefs: [
                    {
                        searchPanes: {
                            show: true,
                        },
                        targets: [0, 1, 2],
                    }
                ],
                language: {
                    url: "/js/german.json",
                    searchPanes: {
                        clearMessage: 'Zurücksetzen',
                        collapse: {0: 'Suchoptionen', _: 'Suchoptionen (%d)'}
                    }
                },
                ajax: '/cloud/employee/ajax',
                columns: [
                    {data: 'email'},
                    {data: 'name'},
                    {data: 'roles'},
                    {data: 'action'}
                ],
            });
        });
    </script>
@endsection
