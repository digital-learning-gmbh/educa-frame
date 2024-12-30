@extends('verwaltung.main')

@section('siteContent')
    <div id="react-administration-modules-hook" jwt="{{ \Illuminate\Support\Facades\Session::get("jwt_token") }}" school_id="{{ $global_school->id }}" year_id="{{ $global_year->id }}" draft_id="{{ $global_entwurf->id }}"></div>


@endsection
