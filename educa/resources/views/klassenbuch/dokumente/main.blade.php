@extends('klassenbuch.basic')

@section('siteContent')
    <div class="col-md-10">
        <div class="card">
            <div class="card-header" id="dokumentHeading">
                <b>Dokumente</b>
            </div>
                <div class="card-body" id="dokumentCard">
                    <p>Hier gibt es die Möglichkeit, Dokumente an das Klassenbuch der Klasse zu hängen.</p>
                    @component('documents.list',[ "model" => $selectedKlasse, "type" => "klasse"])
                    @endcomponent
                </div>
        </div>
    </div>
@endsection

