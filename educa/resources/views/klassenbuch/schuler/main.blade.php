@extends('klassenbuch.basic')

@section('siteContent')
    <div class="col-md-3">
        <div class="card">
            <div class="card-header"><b>Studenten</b>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="defaultCheck1"  onchange="window.location.href = '/klassenbuch/{{ $selectedKlasse->id }}/schueler?showAll={{ !$showAll }}'" @if($showAll) checked @endif>
                    <label class="form-check-label" for="defaultCheck1">
                        Ausgeschiedene Studenten anzeigen
                    </label>
                </div>
            </div>
            <div class="list-group list-group-flush" style="max-height: 100vh; overflow-y: auto;">

                @foreach($schulers->orderBy('lastname')->orderby('firstname')->get() as $teilnehmer)
                <a href="/klassenbuch/{{ $selectedKlasse->id }}/schueler/{{ $teilnehmer->id }}" class="list-group-item list-group-item-action @if(isset($schuler) && $schuler->id == $teilnehmer->id) active @endif">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">{{ $teilnehmer->lastname }} {{ $teilnehmer->firstname }}</h5>
                    </div>
                    <p class="mb-0">{{ $teilnehmer->getFormatedVonBisInKlasse($selectedKlasse->id) }}</p>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-md-7">
        @if(isset($schuler) && $schuler != null)
           @include('klassenbuch.schuler.details')
        @endif
    </div>
@endsection
