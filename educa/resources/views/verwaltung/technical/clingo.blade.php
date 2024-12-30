@extends('verwaltung.technical.main')

@section('siteContent')
    <h3>{{ __('Clingo') }}</h3>

    <div class="card" style="margin-bottom: 10px;">
        <div class="card-body">
            <h5 class="card-title">Clingo TimeTable Test</h5>
            <h6 class="card-subtitle mb-2 text-muted">Execute clingo with timetable reasoning</h6>
            <form class="form" onsubmit="return false;">
                <div class="form-group">
                    <label for="exampleFormControlSelect1">Basic Program</label>
                    <select class="form-control" id="program">
                        @foreach(config('clingo.program') as $key => $value)
                              <option value="{{ $key }}">{{ config('clingo.program.'.$key.".name") }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="staticEmail2" class="sr-only">Atoms</label>
                    <textarea rows="10" type="text" class="form-control" id="timetable-atoms"></textarea>
                </div>
                <button onclick="executeClingTimetable();" class="btn btn-primary">Execute</button>
            </form>
            <h6 class="card-subtitle mb-2 text-muted">Result</h6>
            <p class="text-monospace" id="timetable-result"></p>
        </div>
    </div>
    <script>
        function executeClingTimetable() {
            axios.post('/verwaltung/technical/clingo/timetable', {
                atoms: $('#timetable-atoms').val(),
                progam:  $('#program').val()
            })
                .then(function (response) {
                    $('#timetable-result').html(response.data);
                })
                .catch(function (response) {
                    $('#timetable-result').html(response);
                })
        }
    </script>

    <div class="card" style="margin-bottom: 10px;">
        <div class="card-body">
            <h5 class="card-title">Commandline</h5>
            <h6 class="card-subtitle mb-2 text-muted">Simple execute clingo command</h6>
            <form class="form" onsubmit="return false;">
                <div class="form-group">
                    <label for="staticEmail2" class="sr-only">Command</label>
                    <input type="text" class="form-control" id="command">
                </div>
                <button onclick="executeClingCommand();" class="btn btn-primary">Execute</button>
            </form>
            <h6 class="card-subtitle mb-2 text-muted">Result</h6>
            <p class="text-monospace" id="command-result"></p>
        </div>
    </div>
    <script>
        function executeClingCommand() {
            axios.post('/verwaltung/technical/clingo/execute', {
                command: $('#command').val(),
            })
                .then(function (response) {
                    $('#command-result').html(response.data);
                })
        }
    </script>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Information</h5>
            <h6 class="card-subtitle mb-2 text-muted">Clingo Help Output</h6>
            <p class="text-monospace">{!! nl2br($clingoHelpText) !!}</p>
        </div>
    </div>
@endsection