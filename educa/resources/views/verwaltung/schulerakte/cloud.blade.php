@extends('verwaltung.schulerakte.main')

@section('siteContent')
    @if (session()->has('status'))
        <div class="alert alert-success">
            {{ session()->get('status') }}
        </div>
    @endif
    <div class="alert alert-info" role="alert">
        Diese digitale Schülerdatei kann mit anderen Schulen über die StuPla Cloud geteilt werden.
    </div>
    <div class="card">
        <div class="card-body">

        </div>
    </div>
@endsection
