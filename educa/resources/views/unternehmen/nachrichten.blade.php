@extends('layouts.unternehmen')

@section('appContent')
    <style>
        .subpage-main {
            margin-top: 0px;
        }
    </style>
    <div class="container-fluid subpage-main">
        <div class="row h-100">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header"><b>Nachrichten</b> <button class="btn btn-success"  data-toggle="modal" data-target="#exampleModal" ><i class="fas fa-plus"></i></button>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="/praxis/" class="list-group-item list-group-item-action d-flex" style="padding-left: 0px;">

                            <img src="/api/image/schuler/?user_id=1&size=60" class="rounded m-1" alt="...">
                            <div class="flex-fill">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">Benjamin Ledel</h5>
                                    <small>vor 6 Minuten</small>
                                </div>
                                <p class="mb-1">Testnachricht</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-9 h-100">
                <div class="card h-100">
                    <div class="card-header"><b>Benjamin Ledel</b>
                        <small>Nachrichten werden per E-Mail zugestellt</small>
                    </div>
                    <div class="list-group" style="overflow: auto">
                        @for($i = 0; $i < 10 ; $i++)
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <div class="d-flex mb-1">
                                        <img src="/api/image/schuler/?user_id=1&size=30" class="rounded m-1" alt="...">
                                        <h5 class="mt-2">Benjamin Ledel</h5>
                                    </div>
                                    <small class="text-muted">vor 3 Tagen</small>
                                </div>
                                <p class="mb-1">
                                    {!! nl2br("Sehr geehrte Frau Dr. Müller,

                                    haben Sie vielen Dank für das nette und äußerst informative Gespräch in der vergangenen Woche.

                                    Wie bereits angedeutet, haben wir zum gleichen Thema einen entsprechenden Workshop organisiert. Da uns nun die finalen Daten hierzu vorliegen, möchte ich Sie gerne zu der Veranstaltung einladen.

                                    24. August 2017

                                    10–16 Uhr

                                    Großer Sitzungssaal in unseren Räumlichkeiten
                                    Wir rechnen derzeit mit 28 Teilnehmern. Für Verpflegung ist gesorgt.

                                    Gerne möchten wir Ihnen die Gelegenheit geben, auch als Rednerin aufzutreten. Falls Sie daran Interesse haben, geben Sie mir doch bitte schnellstmöglich Bescheid.

                                    Ich würde mich freuen, Sie an diesem Termin wiederzutreffen.

                                    Freundliche Grüße nach Stuttgart,

                                    Peter Schmidt

                                    Personalabteilung")
                                    !!}
                                </p>
                                <small class="text-muted">Übertragen</small>
                            </a>
                        @endfor
                    </div>
                    <div class="card-body" style="padding: 0px;"></div>
                    <div class="card-footer">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customSwitch1">
                            <label class="custom-control-label" for="customCheck1">Signatur automatisch anhängen</label>
                        </div>
                        <textarea class="form-control  mt-2" id="exampleFormControlTextarea1" rows="3"></textarea>
                        <button type="submit" class="btn btn-primary mt-2">Absenden</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
