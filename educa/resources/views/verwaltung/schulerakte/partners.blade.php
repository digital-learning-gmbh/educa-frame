@extends('verwaltung.schulerakte.main')

@section('siteContent')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header" id="partnersHeading">
                <b>{{ __('Praxispartner') }}</b>
            </div>
            <div class="card-body" id="partnersCard">
                <div class="row">
                    <div class="col-sm">
                        <table id="partnersTable" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>Partner</th>
                                <th>Von</th>
                                <th>Bis</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($partners as $partner)
                                <tr>
                                    <td>{{ $partner["name"] }}</td>
                                    <td>{{ $partner["from"] }}</td>
                                    <td>{{ $partner["until"] }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('additionalScript')
    <script>
        var anwesenheitTable = $('#partnersTable').DataTable({
            pageLength: 10,
            colReorder: true,
            dom: 'fBrtlip',
            buttons: [ 'print','excel', 'pdf', 'colvis'
            ],
            language : {
                url: "/js/german.json"
            },
            columns: [
                { data: 'partner' },
                { data: 'from' },
                { data: 'until' },
            ],
            order: [[1, "desc"]]
        });
    </script>
@endsection
