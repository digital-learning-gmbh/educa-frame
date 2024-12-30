@extends('layouts.loggedIn')

@section('appContent')
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Neuer Unterrichtsabschnitt</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="/praxis/sections/{{ $selectKlasse->id }}">
                    @csrf
                <div class="modal-body">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Name des Abschnitts</label>
                            <input required name="name" type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Name des Abschnitts">
                        </div>
                    <div class="form-group">
                        <label for="typ">Typ</label>
                        <select name="typ" class="form-control" id="typ">
                            <option value="theorie">Theorie</option>
                            <option value="praxis">Praxis</option>
                            <option value="other">Sonstiges (z.B. Ferien)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Beginn</label>
                        <div class="input-group date" id="datepicker1" data-target-input="nearest">
                            <input required name="begin" type="text" class="form-control datetimepicker-input" data-target="#datepicker1"/>
                            <div class="input-group-append" data-target="#datepicker1" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Ende</label>
                        <div class="input-group date" id="datepicker2" data-target-input="nearest">
                            <input required name="end" type="text" class="form-control datetimepicker-input" data-target="#datepicker2"/>
                            <div class="input-group-append" data-target="#datepicker2" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                    <button type="submit" class="btn btn-primary">Anlegen</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container-fluid subpage-main">
        <div class="row">
            <div class="col-md-3">
                <h3>Abschnittsplanung:  <select id="personDropDown" class="custom-select" onchange="this.options[this.selectedIndex].value && (window.location = '/praxis/sections/' + this.options[this.selectedIndex].value);">
                        @foreach($klassen as $klasse)
                            <option value="{{ $klasse->id }}" @if($selectKlasse->id == $klasse->id) selected @endif>{{ $klasse->displayName }}</option>
                        @endforeach
                    </select></h3>

                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>Hinweis:</strong> Sollen in einer Klasse/Kurs Praxiseinsätze stattfinden, so legen Sie zunächst Praxis- und Theorieabschnitte für die Klasse fest.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="card">
                    <div class="card-header"><b>Unterrichtsabschnitte</b> <button class="btn btn-success"  data-toggle="modal" data-target="#exampleModal" ><i class="fas fa-plus"></i></button>
                        <ul class="navbar-nav mr-auto">
                            <!-- <li class="nav-item active">
                                <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Link</a>
                            </li> -->
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Export
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="/praxis/sections/{{ $selectKlasse->id }}/export/?overview">Praxis: Pro Abschnitt</a>
                                        <a class="dropdown-item" href="/praxis/sections/{{ $selectKlasse->id }}/export/?page">Praxis: Pro Teilnehmer</a>
                                        <div class="dropdown-divider"></div>
                                        <!--   <a class="dropdown-item" href="#">Excel</a> -->
                                    </div>
                                </li>
                        </ul>

                    </div>
                    <div class="list-group list-group-flush">
                        @foreach($selectKlasse->lehrabschnitte as $abschnitt)
                        <a href="/praxis/sections/{{ $selectKlasse->id }}/{{ $abschnitt->id }}" class="list-group-item list-group-item-action @if($selectAbschnitt != null && $selectAbschnitt->id == $abschnitt->id) active @endif">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">{{ $abschnitt->name }} <span class="badge @if($abschnitt->type == "praxis") badge-secondary @elseif($abschnitt->type == "theorie") badge-info @else badge-success @endif">@if($abschnitt->type == "praxis") Praxis @elseif($abschnitt->type == "theorie") Lehre @else Sonstiges @endif</span></h5>
                                <small>{{ date("d.m.Y",strtotime($abschnitt->begin)) }} - {{ date("d.m.Y",strtotime($abschnitt->end)) }}</small>
                            </div>
                            <p class="mb-1">{{ $abschnitt->inhalt }}</p>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                @if($selectAbschnitt != null)
                <div class="card">
                    <div class="card-header">
                    <h5 class="card-title"><b>Abschnitt: {{ $selectAbschnitt->name }}</b></h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="/praxis/sections/{{ $selectKlasse->id }}/{{ $selectAbschnitt->id }}">
                            @csrf
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Name des Abschnitts</label>
                                    <input value="{{ $selectAbschnitt->name }}" required name="name" type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Name des Abschnitts">
                                </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Inhalte/Beschreibung</label>
                                <textarea name="inhalt" class="form-control" id="exampleFormControlTextarea1" rows="3">{{ $selectAbschnitt->inhalt }}</textarea>
                            </div>

                            <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Beginn</label>
                                    <div class="input-group date" id="datepicker3" data-target-input="nearest">
                                        <input value="{{ date("d.m.Y",strtotime($selectAbschnitt->begin)) }}" required name="begin" type="text" class="form-control datetimepicker-input" data-target="#datepicker3"/>
                                        <div class="input-group-append" data-target="#datepicker3" data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Ende</label>
                                    <div class="input-group date" id="datepicker4" data-target-input="nearest">
                                        <input value="{{ date("d.m.Y",strtotime($selectAbschnitt->end)) }}" required name="end" type="text" class="form-control datetimepicker-input" data-target="#datepicker4"/>
                                        <div class="input-group-append" data-target="#datepicker4" data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                    <div class="row">
                            <div class="form-group col-4">
                                <label for="typ">Bearbeitungsstatus</label>
                                <select name="status" class="form-control" id="typ">
                                    <option value="draft" @if($selectAbschnitt->inPlanung) selected @endif>Entwurf</option>
                                    <option value="complete" @if(!$selectAbschnitt->inPlanung) selected @endif>Freigabe</option>
                                </select>
                            </div>
                            <div class="form-group col-4">
                                <label for="typ">Typ</label>
                                <select name="typ" class="form-control" id="typ">
                                    <option value="theorie" @if($selectAbschnitt->type == "theorie") selected @endif>Theorie</option>
                                    <option value="praxis" @if($selectAbschnitt->type == "praxis") selected @endif>Praxis</option>
                                    <option value="other" @if($selectAbschnitt->type == "other") selected @endif>Sonstiges (z.B. Ferien)</option>
                                </select>
                            </div>
                            <div class="form-check col-4">
                                <label for="typ">Stundenplananzeige</label>  <div class="custom-control custom-checkbox">
                                <input name="visibleTimetable" type="checkbox" class="form-check-input" id="exampleCheck1" @if($selectAbschnitt->visibleTimetable) checked @endif>
                                <label class="form-check-label" for="exampleCheck1">Abschnittsname im Stundenplan anzeigen</label>
                            </div>
                            </div>
                    </div>
                            <div class="row">
                                <div class="form-group col-4">
                                    <label for="typ">Fehlzeiten (max)</label>
                                    <input type="number" class="form-control" value="{{ $selectAbschnitt->maxFehlzeit }}" name="maxFehlzeit">
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                                <button type="submit" class="btn btn-primary">Speichern</button>
                            </div>
                        </form>
                    </div>
                </div>
                     @if($selectAbschnitt->type == "praxis")
                        @include('praxis.parts.praxis')
                    @endif
                    @if($selectAbschnitt->type == "theorie")
                        @include('praxis.parts.theorie')
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection
