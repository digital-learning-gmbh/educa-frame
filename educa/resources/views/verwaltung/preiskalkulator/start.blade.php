@extends('verwaltung.preiskalkulator.main')

@section('siteContent')
    <h3>Starttermine</h3>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Starttermine</h5>
            <p class="card-text">Hier können Starttermine für die Levelstufen verwaltet werden</p>
            <div style="margin-bottom: 3px;">
                <div class="float-right">
                    <a href="#" data-toggle="modal" data-target="#exampleModal" class="btn btn-primary">Starttermin
                        hinzufügen</a>

                </div>
                <div class="clearfix"></div>
            </div>
            <table id="table_id" class="data-table table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Datum</th>
                    <th>Kurs</th>
                    <th>Level</th>
                    <th>Aktion</th>
                </tr>
                </thead>
                <tbody>
                @foreach($startDates as $startDate)
                    <tr>
                        <td>{{ date("d.m.Y H:i",strtotime($startDate->startDate)) }}</td>
                        @if(\App\PreisAuswahl::find($startDate->preis_auswahl) == null)
                            <td><i>Kurs gelöscht</i></td>
                        @else
                            @php $auswahl = \App\Http\Controllers\Verwaltung\Tools\PreiskalkulatorExternController::getRecusiveAuswahl($startDate->preis_auswahl) @endphp
                            <td>@foreach($auswahl as $element) \ {{ $element->name }} @endforeach</td>
                        @endif
                        <td>{{ $startDate->level }}</td>
                        <td>
                            <a href="/verwaltung/preiskalkulator/start/{{$startDate->id}}/delete"
                               class="btn btn-xs btn-danger">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Starttermin hinzufügen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form" method="POST" action="{{route('startDate.create')}}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Datum</label>
                            <div class="input-group date" id="datepicker11" data-target-input="nearest">
                                <input id="startDate" required name="startDate" type="text"
                                       class="form-control datetimepicker-input" data-target="#datepicker11"/>
                                <div class="input-group-append" data-target="#datepicker11"
                                     data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Bezieht sich auf Kurs</label>
                            <select class="form-control select2" multiple name="kurse[]" required>
                                @php
                                    $kategorie = \App\PreisKategorie::find(1);
                                    $secondkategorie = \App\PreisKategorie::find(2);
                                    $thirdkategorie = \App\PreisKategorie::find(3);
                                @endphp
                                @foreach($kategorie->auswahl(0) as $auswahl)
                                    <optgroup label="{{ $auswahl->name }}">
                                        @foreach($secondkategorie->auswahl($auswahl->id) as $secondAuswahl)
                                            <optgroup label="{{ $secondAuswahl->name }}">
                                                @foreach($thirdkategorie->auswahl($secondAuswahl->id) as $thirdAuswahl)
                                                    <option value="{{ $thirdAuswahl->id }}">{{ $thirdAuswahl->name }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Level</label>
                            <select class="form-control select2" name="level">
                                @php
                                    $levels = \App\Http\Controllers\Verwaltung\Tools\PreiskalkulatorController::$levels;
                                @endphp
                                @foreach($levels as $auswahl)
                                    <option value="{{ $auswahl }}">{{ $auswahl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- <div class="form-group">
                            <label>zusätzlicher Preis</label>
                            <input name="name" type="text" class="form-control" required>
                        </div> -->
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Erstellen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
