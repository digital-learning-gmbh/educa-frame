@extends('verwaltung.preiskalkulator.main')

@section('siteContent')
    <h3>Einstellungen</h3>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Einstellungen Preiskalkulator</h5>
            <p class="card-text">Hier finden Sie die Einstellungen des Preiskalkulators</p>


            <form class="mt-5" method="POST">
                @csrf
                <div class="form-group">
                    <label for="exampleInputEmail1">Anmeldegebühr (in €)</label>
                    <input type="number" class="form-control" id="exampleInputEmail1" required name="initial_amount" value="{{ \App\KalkulatorSettings::getValue('initial_amount',0) }}">
                </div>
            <div class="mt-2">
                <button type="submit" class="btn btn-primary">Speichern</button>
            </div>
            </form>
        </div>
    </div>
@endsection
