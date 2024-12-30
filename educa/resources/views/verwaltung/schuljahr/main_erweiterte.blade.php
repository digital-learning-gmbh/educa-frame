@extends('verwaltung.main')

@section('siteContent')
    <h3>Schuljahr bearbeiten</h3>
    @if (session()->has('status'))
        <div class="alert alert-success">
            {{ session()->get('status') }}
        </div>
    @endif
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/verwaltung/schulen/{{ $schule->id }}">{{ $schule->name }}</a></li>
            <li class="breadcrumb-item"><a href="/verwaltung/schulen/{{ $schule->id }}">Schuljahre</a></li>
            <li class="breadcrumb-item"><a href="/verwaltung/schulen/{{ $schule->id }}/schuljahr/{{ $schuljahr->id }}">{{ $schuljahr->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Erweiterte Einstellungen</li>
        </ol>
    </nav>
    @yield('erweiterung')
@endsection
