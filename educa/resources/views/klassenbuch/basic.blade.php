@extends('layouts.loggedIn')

@section('appContent')
    <div id="laravel-content-panel-classbook" class="container-fluid subpage-main">
        <div class="row">
            <div class="col-md-2">
                <div class="mb-2">
                <select id="personDropDown" class="select2 mb-2" onchange="changeKlasse(this.options[this.selectedIndex].value);">
                    @foreach($klassen as $klasse)
                        <option value="{{ $klasse->id }}" @if($selectedKlasse->id == $klasse->id) selected @endif>{{ $klasse->displayName }}</option>
                    @endforeach
                </select>
                </div>
                <div class="text-center mb-2">
                <img src="/api/image/klasse/?user_id={{ $selectedKlasse->id }}&size=100" class="rounded-circle">
                </div>
                <h3 class="text-center">{{ $selectedKlasse->displayName }}</h3>


                <ul class="list-group">

                    <li class="list-group-item"><a href="/klassenbuch/{{ $selectedKlasse->id }}" class="text-reset"><i class="fas fa-calendar-alt"></i> {{ __('Vorlesungen') }}</a></li>
                    <li class="list-group-item"><a href="/klassenbuch/{{ $selectedKlasse->id }}/schueler" class="text-reset"><i class="fas fa-list"></i> {{ __('Studenten') }}</a></li>
                    <li class="list-group-item"><a href="/klassenbuch/{{ $selectedKlasse->id }}/noten" class="text-reset"><i class="fas fa-star-half-alt"></i> {{ __('Noten & Prüfungen') }}</a></li>
                    <li class="list-group-item"><a href="/klassenbuch/{{ $selectedKlasse->id }}/aemter" class="text-reset"><i class="fas fa-award"></i> {{ __('Ämter') }}</a></li>
                    <li class="list-group-item"><a href="/klassenbuch/{{ $selectedKlasse->id }}/dokumente" class="text-reset"><i class="fas fa-file-alt"></i> {{ __('Dokumente') }}</a></li>            </ul>
            </div>
            @yield('siteContent')
        </div>
    </div>
    <script>
        function changeKlasse(id) {
            window.location.href = "/klassenbuch/" + id;
        }
    </script>
@endsection
