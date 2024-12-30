@extends('emails.bootstrap')


@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Nachricht von {{ $displayNames }} <small>(#{{ $correspondez->unique_id }})</small></h5>

            <p class="card-text">Betreff: <b>{{ $correspondez->subject }}</b></p>
            <p>
                {!! $correspondez->content !!}
            </p>

        </div>
        <div class="card-footer text-muted">
            {{ config('stupla.correspondez.signature', 'Diese Nachricht wurde über educa geschickt.') }}

            <div id="msg-id">MSG:{{ $correspondez->unique_id }} </div>
            <p>Bitte antworten Sie unter diesem Bereich und entfernen Sie nicht die obigen Zeichenketten, so dass die Nachricht wieder automatisch zugeordnet werden kann.</p>
        </div>
    </div>
@endsection
