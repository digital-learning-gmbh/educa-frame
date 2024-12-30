@extends('verwaltung.schulerakte.main')

@section('siteContent')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header" id="kenntnisseHeading">
                <b>Kenntnisse</b>
            </div>
            <div class="card-body" id="dokumentCard">
                @if($errors->any())
                    <div class="alert alert-danger">
                        {{$errors->first()}}
                    </div>
                @endif
                @if (session()->has('status'))
                    <div class="alert alert-success">
                        {{ session()->get('status') }}
                    </div>
                @endif
                        <h5 class="card-title">Übersichtsliste aller Kennnisse von {{$schuler->getDisplayNameAttribute()}}</h5>
                        <!--<h6 class="card-subtitle mb-2 text-muted"></h6>-->
                        <div style="margin-bottom: 3px;">
                            <div class="float-right">
                                <a href="#" data-toggle="modal" data-target="#exampleModal" class="btn btn-primary">Hinzufügen</a>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <table id="table_id" class="data-table table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>Bezeichnung</th>
                                <th>Typ</th>
                                <th>Ergebnis</th>
                                <th>Erreicht am.</th>
                                <th>Bemerkung</th>
                                <th>Aktion</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($kenntnisse as $kenntnis)
                                <tr>
                                    <td>{{ $kenntnis->bezeichnung }}</td>
                                    <td>@switch($kenntnis->typ)
                                            @case('other')
                                            Sonstige
                                            @break
                                            @case('abschluss')
                                            Abschluss
                                            @break
                                            @case('studienabschluss')
                                            Studienabschluss
                                            @break
                                            @case('sprache')
                                            Sprache
                                            @break

                                            @default
                                            Default case...
                                        @endswitch</td>
                                    <td>{{ $kenntnis->ergebnis }}</td>
                                    <td>{{ date_format(new DateTime($kenntnis->tag_erreicht),"d.m.Y") }}</td>
                                    <td>{{ $kenntnis->bemerkung }}</td>
                                    <td>
                                        <a class="btn btn-xs btn-primary" href="/verwaltung/schulerlisten/{{$schuler->id}}/kenntnisse/{{$kenntnis->id}}/delete">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal to add a new teilnehmer -->
                <div class="modal fade" id="exampleModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Kenntnisse hinzufügen</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form class="form" method="POST" action="{{route('kenntnis.create',['id'=>$schuler->id])}}" enctype = "multipart/form-data">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="form-row">
                                            <div class="form-group col-12">
                                            <label for="bezeichnung">Bezeichnung</label>
                                            <input type="text" class="form-control" name="bezeichnung" required>
                                            </div>
                                            <div class="form-group col-12">
                                                <label for="inputTyp">Typ</label>
                                                <select id="inputTyp" name="typ" class="select2" required>
                                                    <option value="other">Sonstiges</option>
                                                    <option value="sprache">Sprache</option>
                                                    <option value="abschluss">Abschluss</option>
                                                    <option value="studienabschluss">Studienabschluss</option>
                                                </select>

                                            </div>
                                            <div class="form-group col-12">
                                                <label for="ergebnis">Ergebnis</label>
                                                <input type="text" class="form-control" name="ergebnis">
                                            </div>
                                            <div class="form-group col-12">
                                                <label for="erreicht_datum">Erreicht am.</label>
                                                <div class="input-group date" id="datepicker1" data-target-input="nearest">
                                                    <input id="erreicht_datum" name="erreicht_datum" type="text" class="form-control datetimepicker-input tobeHidden" data-target="#datepicker1" required/>
                                                    <div class="input-group-append" data-target="#datepicker1" data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group col-12">
                                                <label for="bemerkung">Bemerkung</label>
                                                <textarea class="form-control" name="bemerkung"></textarea>
                                            </div>

                                        </div>
                                    </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Speichern</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
