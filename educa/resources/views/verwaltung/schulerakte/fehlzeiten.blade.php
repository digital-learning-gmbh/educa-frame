@extends('verwaltung.schulerakte.main')

@section('siteContent')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header" id="fehlzeitenHeading">
                <b>Fehlzeiten</b>
            </div>
            <div class="card-body" id="dokumentCard">
                @component('verwaltung.schulerakte.snippets.fehlzeiten',["fehlzeit_typs" => $fehlzeit_typs, "schuler" => $schuler])
                @endcomponent
            </div>
        </div>
    </div>
@endsection

