@extends('layouts.klassenbuch')

@section('appContent')
    <div class="container-xl">
        <div class="row">
            <div class="col-md-12">
                <select id="personDropDown" class="select2 mb-2" onchange="changeKlasse(this.options[this.selectedIndex].value);" data-select2-id="personDropDown" tabindex="-1" aria-hidden="true">
                    @foreach($klassen as $klasse)
                        <option value="{{ $klasse->id }}" @if($selectedKlasse->id == $klasse->id) selected @endif>{{ $klasse->displayName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-12 mt-3">
                <div id="react-administration-course-functions-hook" jwt="{{ \Illuminate\Support\Facades\Session::get("jwt_token") }}" school_id="{{ $global_school->id }}" year_id="{{ $global_year->id }}" draft_id="{{ $global_entwurf->id }}"></div>
            </div>
        </div>
    </div>
    <script>
        function changeKlasse(id) {
            window.location.href = "/dozent/klassenamt/" + id;
        }
    </script>
@endsection
