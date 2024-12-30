@extends('verwaltung.main')

@section('siteContent')
    <h3>Benutzer bearbeiten</h3>
    @if (session()->has('status'))
        <div class="alert alert-success">
            {{ session()->get('status') }}
        </div>
    @endif
    <div class="card">   <div class="card-body">
            <form method="POST">
                @csrf
                <div class="form-group">
                    <label>Vorname</label>
                    <input name="firstname" type="text" class="form-control" required value="{{ $user->firstname }}">
                </div>
                <div class="form-group">
                    <label>Nachname</label>
                    <input name="lastname" type="text" class="form-control" required value="{{ $user->lastname }}">
                </div>
                <div class="form-group">
                    <label>Schulen</label>
                    <select name="schulen[]" class="form-control select2" multiple>
                        @foreach(\App\Schule::all() as $schule)
                        <option value="{{ $schule->id }}" @if($user->schulen()->pluck("schule_id")->contains($schule->id)) selected @endif>{{ $schule->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Speichern</button>
            </form>
        </div>
    </div>
@endsection
