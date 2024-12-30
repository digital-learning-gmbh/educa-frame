@extends('verwaltung.main')

@section('siteContent')
    <h3>{{ __('API-Schlüssel') }}</h3>
    <div class="card mt-2">
        <div class="card-body">
            <h5 class="card-title">API-Schlüssel</h5>
            <h6 class="card-subtitle mb-2 text-muted">Liste von Aufgaben, die sich im System befinden.</h6>
            <table id="table_id" class="data-table table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Token</th>
                    <th>Aktion</th>
                </tr>
                </thead>
                <tbody>
                @foreach($keys as $key)
                    <tr>
                        <td>{{ $key->name }}</td>
                        <td>{{ $key->token }}</td>
                        <td></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
