@extends('klassenbuch.basic')

@section('siteContent')
    <div class="col-md-10">
        <div class="card">
            <div class="card-header" id="dokumentHeading">
                <b>Dokumente aus dem Lehrplan</b>
            </div>
                <div class="card-body" id="dokumentCard">
                    <p>Hier werden die Dokumente aus dem Lehrplan der Klasse angezeigt.</p>
                    @foreach($selectedKlasse->getLehrplan as $lehrplan)
                        <h5>{{ $lehrplan->name }}</h5>
                    @component('documents.list',[ "model" => $lehrplan, "type" => "curriculum"])
                    @endcomponent
                    @endforeach
                </div>
        </div>
    </div>
@endsection

