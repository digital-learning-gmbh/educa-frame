@extends('verwaltung.technical.main')

@section('siteContent')
    <h3>{{ __('Such-Index') }}</h3>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Informationen über Such-Indexe</h5>
            <div style="margin-bottom: 3px;">
                <div class="float-right">
            <a href="/verwaltung/technical/searchIndex/1000" class="btn btn-xs btn-warning">Alle neu erstellen <i class="fas fa-sync"></i></a>
                </div>
                <div class="clearfix"></div>
            </div>
            <table id="table_id" class="data-table table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Model</th>
                    <th>Anzahl</th>
                    <th>Aktion</th>
                </tr>
                </thead>
                <tbody>
                @php $i = 0 @endphp
                @foreach($models as $model)
                    <tr>
                        <td>{{ $model }}</td>
                        <td>{{ $model::count() }}</td>
                        <td>
                            <a href="/verwaltung/technical/searchIndex/{{ $i }}" class="btn btn-xs btn-warning">Neu erstellen <i class="fas fa-sync"></i></a>
                        </td>
                    @php $i++; @endphp
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
