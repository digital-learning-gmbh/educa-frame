@extends('verwaltung.main')

@section('siteContent')
    <h3>{{ __('System-Einstellungen') }}</h3>
    <div class="card mt-2">
        <div class="card-body">
            <h5 class="card-title">System-Einstellungen</h5>
            <h6 class="card-subtitle mb-2 text-muted">Globale Systemeinstellungen für diese StuPla Instanz.</h6>
            <form method="POST">
                @csrf
                <div class="form-group">
                    <label for="exampleFormControlTextarea1">System-Nachricht</label>
                    <textarea name="system.message" class="form-control" id="exampleFormControlTextarea1" rows="3">{{ \App\SystemEinstellung::getEinstellungen("system.message","") }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">Speichern</button>
            </form>
        </div>
    </div>
@endsection
