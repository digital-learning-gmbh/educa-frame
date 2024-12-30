@extends('verwaltung.schulerakte.main')

@section('siteContent')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header" id="practiceHeading">
                <b>{{ __('Praxiseinsätze') }}</b>
            </div>
            <div class="card-body" id="practiceCard">
                <div style="margin-bottom: 3px;">
                    <div class="float-right">
                        <a href="/verwaltung/schulerlisten/{{ $schuler->id }}/practiceexport" class="btn btn-primary">Exportieren</a>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="row">
                    <div class="col-sm">
                        <table id="practiceTable" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>Unternehmen</th>
                                <th>Ansprechpartner</th>
                                <th>Stunden / Tag</th>
                                <th>Stunden insgesamt</th>
                                <th>Von</th>
                                <th>Bis</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($practices as $practice)
                                <tr>
                                    <td>{{ $practice->unternehmenDisplay() }}</td>
                                    <td>{{ $practice->kontaktDisplay() }}</td>
                                    <td>{{ $practice->hours_day }}</td>
                                    <td>{{ $practice->complete }}</td>
                                    <td>{{ (new \Carbon\Carbon($practice->startDate))->format("d.m.Y") }}</td>
                                    <td>{{ (new \Carbon\Carbon($practice->endDate))->format("d.m.Y") }}</td>
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
        var anwesenheitTable = $('#practiceTable').DataTable({
            pageLength: 10,
            colReorder: true,
            dom: 'fBrtlip',
            buttons: [ ],
            language : {
                url: "/js/german.json"
            },
            columns: [
                { data: 'company' },
                { data: 'partner' },
                { data: 'hours_d' },
                { data: 'hours' },
                { data: 'from' },
                { data: 'until' },
            ],
            order: [[4, "desc"]]
        });
    </script>
@endsection
