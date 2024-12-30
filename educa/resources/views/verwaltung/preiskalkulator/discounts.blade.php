@extends('verwaltung.preiskalkulator.main')

@section('siteContent')
    <h3>Rabatt-Codes</h3>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Rabatt-Codes</h5>
            <p class="card-text">Hier können Rabatt-Codes des Preiskalkulators verwaltet werden</p>
            <div style="margin-bottom: 3px;">
                <div class="float-right">
                    <a href="#" data-toggle="modal" data-target="#exampleModal" class="btn btn-primary">Rabatt-Code hinzufügen</a>

                </div>
                <div class="clearfix"></div>
            </div>
            <table id="table_id" class="data-table table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Code</th>
                    <th>Gültigkeit</th>
                    <th>Kurs</th>
                    <th>Rabatt (fix)</th>
                    <th>Rabatt (Prozent)</th>
                    <th>Aktion</th>
                </tr>
                </thead>
                <tbody>
                @foreach($codes as $code)
                    <tr>
                        <td>{{ $code->code }}</td>
                        <td>{{ date("d.m.Y H:i",strtotime($code->from)) }} - {{ date("d.m.Y H:i",strtotime($code->to)) }}</td>
                        @if(\App\PreisAuswahl::find($code->constraint_1) == null)
                            <td><i>Kurs gelöscht</i></td>
                        @else
                            @php $auswahl = \App\Http\Controllers\Verwaltung\Tools\PreiskalkulatorExternController::getRecusiveAuswahl($code->constraint_1) @endphp
                            <td>@foreach($auswahl as $element) \ {{ $element->name }} @endforeach</td>
                        @endif
                        <td>{{ $code->amount }}</td>
                        <td>{{ $code->percent }}</td>
                        <td>
                            <a href="/verwaltung/preiskalkulator/discount/{{$code->id}}/delete" class="btn btn-xs btn-danger">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Rabatt-Code hinzufügen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form" method="POST" action="{{route('discount.create')}}" >
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Rabatt-Code</label>
                            <input name="code" type="text" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Gültig von</label>
                            <div class="input-group date" id="datepicker11" data-target-input="nearest">
                                <input id="from" required name="from" type="text" class="form-control datetimepicker-input" data-target="#datepicker11"/>
                                <div class="input-group-append" data-target="#datepicker11" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Gültig bis</label>
                            <div class="input-group date" id="datetimepicker2" data-target-input="nearest">
                                <input id="until" required name="until" type="text" class="form-control datetimepicker-input" data-target="#datetimepicker2"/>
                                <div class="input-group-append" data-target="#datetimepicker2" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Bezieht sich auf:</label>
                            <select class="form-control select2" multiple name="kurse[]" required>
                                @php
                                    $kategorie = \App\PreisKategorie::find(1);
                                    $secondkategorie = \App\PreisKategorie::find(2);
                                    $thirdkategorie = \App\PreisKategorie::find(3);
                                    $foruthkategorie = \App\PreisKategorie::find(4);
                                @endphp
                                @foreach($kategorie->auswahl(0) as $auswahl)
                                    <optgroup label="{{ $auswahl->name }}">
                                        @foreach($secondkategorie->auswahl($auswahl->id) as $secondAuswahl)
                                            <optgroup label="{{ $secondAuswahl->name }}">
                                            @foreach($thirdkategorie->auswahl($secondAuswahl->id) as $thirdAuswahl)
                                                <optgroup label="{{ $thirdAuswahl->name }}">
                                                @foreach($foruthkategorie->auswahl($thirdAuswahl->id) as $fourthKategorie)
                                                <option value="{{ $fourthKategorie->id }}">{{ $fourthKategorie->name }}</option>
                                                @endforeach
                                                </optgroup>
                                            @endforeach
                                            </optgroup>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>fester Rabatt</label>
                            <input name="amount" type="number" class="form-control" required value="0">
                        </div>
                        <div class="form-group">
                            <label>prozentualer Rabatt (0% - 100%)</label>
                            <input name="percent" type="number" class="form-control" required value="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Erstellen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
