@extends('layouts.devices')

@section('pageContent')
    <style>
        label {
            width: 100%;
            font-size: 1rem;
        }
        .card-input-element+.card:hover {
            cursor: pointer;
        }

        .card-input-element:checked+.card {
            border: 2px solid var(--primary);
            -webkit-transition: border .3s;
            -o-transition: border .3s;
            transition: border .3s;
        }

        .card-input-element:checked+.card::after {
            content: "\F00C";
            color: #AFB8EA;
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            font-size: 24px;
            -webkit-animation-name: fadeInCheckbox;
            animation-name: fadeInCheckbox;
            -webkit-animation-duration: .5s;
            animation-duration: .5s;
            -webkit-animation-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            animation-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        }

        @-webkit-keyframes fadeInCheckbox {
            from {
                opacity: 0;
                -webkit-transform: rotateZ(-20deg);
            }
            to {
                opacity: 1;
                -webkit-transform: rotateZ(0deg);
            }
        }

        @keyframes fadeInCheckbox {
            from {
                opacity: 0;
                transform: rotateZ(-20deg);
            }
            to {
                opacity: 1;
                transform: rotateZ(0deg);
            }
        }

    </style>
    <div class="container mt-5">
        <div class="">
            <div class="card-header">
                <h5>Folgende Geräte sind in dem Zeitraum verfügbar</h5>
                <p>Zeitraum: {{ $start }} - {{ $end }}</p>
            </div>
            <div class="card-body">
            @foreach($errors->all() as $error)
                <p class="alert alert-danger">{{ $error }}</p>
            @endforeach
            <form method="post" action="/devices/ausleihe3">
                {{ csrf_field() }}
                <input style="visibility: hidden" type="text" value="{{ $start }}" name="start">
                <input style="visibility:hidden;" type="text" value="{{ $end }}" name="end">
@foreach($ressourcenAvaible as $ressource)
                    <div class="form-group row">
                        <label for="radio1{{ $ressource->id }}" style="width: 100%;">
                            <input type="radio" name="radio1" id="radio1{{ $ressource->id }}" class="card-input-element d-none" value="{{ $ressource->id }}" required>
                            <div class="card d-flex flex-row justify-content-between align-items-center">
                                <div class="col" style="padding: 0px;">
                                        <div class="card-header">{{ $ressource->name }} ({{ $ressource->anzahl }} verfügbar)</div>
                                <div class="card-body">{{ $ressource->text }}
                                    <div class="row">
                                        <div for="name" class="col-sm-3 control-label">Anzahl</div>
                                        <div class="col-sm-9">
                                            <select data-toggle="select2" id="dynamic_select" name="anzahl_{{ $ressource->id }}" class="form-control">
                                                @for($i = 1; $i <= $ressource->anzahl; $i++)

                                                    <option value="{{ $i  }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div></div></div></div>
                            </div>
                        </label>
                    </div>
                @endforeach
                <div class="form-group">
                    <label for="name" class="col-sm-3 control-label">Optionale Bemerkung</label>
                    <div class="col-sm-9">
                        <textarea name="text" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="form-group margin-none">
                    <div class="col-sm-offset-3 col-sm-9">
                        <a href="javascript:back();" class="btn btn-white">Zurück</a>
                        <button type="submit" class="btn btn-primary">Buchen</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(count($ressourceBlocked) > 0)
        <h3>Nicht verfügbare Geräte <a class="btn btn-primary" role="button" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">Anzeigen</a></h3>
        <div class="collapse" id="collapseExample">
    <div class="panel panel-default" style="max-width: 700px; margin: 0 auto; margin-top: 20px;">

        <div class="panel-body">
            @foreach($ressourceBlocked as $ressource)
                <div class="radio radio-primary"><div class="panel panel-primary">
                            <div class="panel-heading">{{ $ressource->name }} (0 verfügbar)</div>
                            <div class="panel-body">{{ $ressource->text }}</div>
                            <div class="panel-body"><h5>Begründung:</h5>
                                <ul>
                                    @foreach($ressourceProblems[$ressource->id] as $problem)
                                        <li>gebucht von @if($problem->user == null) <i>gelöschter Nutzer</i> @else {{ $problem->user->displayName }}@endif, <br><b>von:</b> {{ $parser::strtimeZuBeautiStunde($problem->start) }}<br><b>bis:</b> {{ $parser::strtimeZuBeautiStunde($problem->end,false) }}<br>Anzahl {{ $problem->anzahl }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                </div>
            @endforeach
        </div>
    </div>
        </div>
    </div>
    @endif
    <script>
        function back() {
            window.history.back();
        }
    </script>
@endsection
