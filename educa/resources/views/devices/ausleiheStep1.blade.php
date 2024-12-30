@extends('layouts.devices')

@section('pageContent')
    <div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h5>Für welchen Zeitraum wird ein Gerät benötigt?</h5>
        </div>
        <div class="card-body">
            @foreach($errors->all() as $error)
                <p class="alert alert-danger">{{ $error }}</p>
            @endforeach
            <form class="form-horizontal" role="form" method="post" action="/devices/ausleihe2">
                {{ csrf_field() }}

                <div class="form-row">
                    <label for="fach" class="col-sm-3 label">Beginn</label>
                    <div class="col-sm-4">
                    <div class="input-group date" id="datepicker1" data-target-input="nearest">
                        <input name="start" id="datepicker1" type="text" class="form-control datetimepicker-input" autocomplete="off" data-target="#datepicker1">
                        <div class="input-group-append" data-target="#datepicker1" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
                    </div>
                    <div class="col-sm-4">
                        <select name="stunde1" style="width: 100%;" class="select2">
                            @foreach($stunden as $stunde)
                            <option value="{{ $stunde }}">{{ $stunde }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row mt-1">
                    <label for="fach" class="col-sm-3 control-label">Ende</label>
                    <div class="col-sm-4">
                        <div class="input-group date" id="datepicker2" data-target-input="nearest">
                            <input name="end" id="datepicker2" type="text" class="form-control datetimepicker-input" autocomplete="off" data-target="#datepicker2">
                            <div class="input-group-append" data-target="#datepicker2" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <select name="stunde2" style="width: 100%;" class="select2">
                            @foreach($stunden as $stunde)
                                <option value="{{ $stunde }}">{{ $stunde }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-sm-offset-3 col-sm-9">
                        <a href="/ressourcen" class="btn btn-white">Abbrechen</a>
                        <button type="submit" class="btn btn-primary">Suchen</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    </div>

@endsection
