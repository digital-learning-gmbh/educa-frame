@extends('verwaltung.main')

@section('siteContent')
    <div class="card mt-2">
        <div class="card-body">
            <form method="POST">
                @csrf
            <h5 class="card-title">Einstellungen für {{ $name }}</h5>
            {!! $child !!}
                <button type="submit" class="btn btn-success">Speichern</button>
            </form>
        </div>
    </div>
@endsection
