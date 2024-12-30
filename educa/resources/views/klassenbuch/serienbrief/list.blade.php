@extends('klassenbuch.basic')

@section('siteContent')
    <div class="col-md-10">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ __('Serienbriefe') }}</h5>
            <h6 class="card-subtitle mb-2 text-muted">Wählen Sie eine Vorlage für den Serienbrief</h6>
            @component('klassenbuch.serienbrief.list2',[ "model" => $global_school, "type" => "schule", "mainCategory" => "mailMerge"])
            @endcomponent
        </div>
    </div>
    </div>
@endsection
