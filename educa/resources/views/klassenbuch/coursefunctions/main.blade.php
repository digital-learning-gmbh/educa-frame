@extends('klassenbuch.basic')
@section('additionalStyle')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">
@endsection
@section('siteContent')

    <div class="col-md-10">
        <div id="react-administration-course-functions-hook" jwt="{{ \Illuminate\Support\Facades\Session::get("jwt_token") }}" school_id="{{ $global_school->id }}" year_id="{{ $global_year->id }}" draft_id="{{ $global_entwurf->id }}"></div>
    </div>
@endsection
