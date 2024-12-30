@extends('emails.bootstrap')


@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Kopie der Nachricht an {{ $correspondez->getModel()->displayName }} <small>(#{{ $correspondez->unique_id }})</small></h5>

            <p class="card-text">Betreff: <b>{{ $correspondez->subject }}</b></p>
            <p>
                {!! $correspondez->content !!}
            </p>

        </div>
        <div class="card-footer text-muted">
            {{ config('stupla.correspondez.signature', 'Diese Nachricht wurde über educa geschickt.') }}
        </div>
    </div>

@endsection
