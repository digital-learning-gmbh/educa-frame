@extends('layouts.loggedIn')

@section('appContent')
    <div class="container-fluid subpage-main">
        <div id="react-administration-sections-hook" jwt="{{ \Illuminate\Support\Facades\Session::get("jwt_token") }}" school_id="{{ $global_school->id }}" year_id="{{ $global_year->id }}" draft_id="{{ $global_entwurf->id }}"></div>
    </div>

    <style>
        .sectionbackground {
            opacity: 0.3;
        }

        @foreach(\App\Lehrplan::all() as $lehrplan)
        @foreach($lehrplan->lehreinheiten() as $lehrEinheit)
            @if($lehrEinheit->form == "praxis")
                  .modul{{ $lehrEinheit->id }} {
            background-color: {{ $lehrEinheit->color }} !important;
        }
        @endif
        @endforeach
        @endforeach
    </style>

@endsection
