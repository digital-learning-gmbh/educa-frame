@extends('layouts.unternehmen')

@section('appContent')
    <div class="container">
        <h2>Dokumente</h2>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Dokumente Ihrer Praxis/Klinik/Einrichtung</h5>
                <h6 class="card-subtitle mb-2 text-muted">Übersichtsliste aller Dokumente, die Ihnen von der Akademie bereitgestellt werden oder auf die Sie Zugriffsrechte haben</h6>
                @component('documents.list',[ "model" => $user, "type" => "kontakt"])
                @endcomponent
            </div>
        </div>
    </div>
@endsection
