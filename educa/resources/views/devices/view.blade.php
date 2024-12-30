@extends('layouts.devices')

@section('pageContent')
    <div class="row">
        <div class="col-lg-6">
            <div class="card mt-2">
                <div class="card-header">
                    <h3 style="padding-bottom: 10px;">{{ $ressource->name }}<a href="/devices/ausleihe1" class="btn btn-success float-right">Jetzt reservieren</a></h3>

                    <div class="clearfix"></div>
                </div>
                <div class="card-body">
                    @if($canEdit)
                        <form class="form-horizontal" role="form" method="post" action="/devices/ressource/view/{{ $ressource->id }}/save">
                            {{ csrf_field() }}
                        <div class="form-group row">
                            <label for="name" class="col-sm-3 control-label">Anzeigename</label>
                            <div class="col-sm-9">
                                <input value="{{ $ressource->name }}" name="name" class="form-control" id="name" placeholder="Beamer, Moderationskoffer, ...">
                            </div>
                        </div>
                            <div class="form-group row">
                                <label for="name" class="col-sm-3 control-label">Anzahl</label>
                                <div class="col-sm-9">
                                    <select data-toggle="select2" id="dynamic_select" name="anzahl" class="form-control">
                                        @for($i = 1; $i < 100; $i++)

                                            <option value="{{ $i  }}" @if(isset($ressource->anzahl) && $ressource->anzahl == $i) selected @endif>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        <div class="form-group row">
                            <label for="name" class="col-sm-3 control-label">Beschreibung</label>
                            <div class="col-sm-9">
                                <textarea name="text" class="form-control" rows="3">{{ $ressource->text }}</textarea>
                            </div>
                        </div>
                            <div class="form-group row margin-none">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <button type="submit" class="btn btn-primary">Speichern</button>
                                </div>
                            </div>
                        </form>
                         @else
                        <p>Beschreibung:</p>
                    <p>{{ $ressource->text }}</p>
                        @endif
                </div>
            </div>


            <div class="card mt-2">
                <div class="card-header">
                    <h5>Die nächsten Reservierungen:</h5>
                </div>
                <table data-toggle="" class="table data-table table-striped" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>ab</th>
                        <th>bis</th>
                        <th>Nutzer</th>
                        <th>Anzahl</th>
                        <th>Bemerkungen</th>
                        <th>Aktion</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th>#</th>
                        <th>ab</th>
                        <th>bis</th>
                        <th>Nutzer</th>
                        <th>Anzahl</th>
                        <th>Bemerkungen</th>
                        <th>Aktion</th>
                    </tr>
                    </tfoot>
                    <tbody>
                    @php $counter = 1; @endphp
                    @foreach($reservations as $reservation)
                        <tr>
                            <td>{{ $counter }}</td>
                            <td>{{ $parser::strtimeZuBeautiStunde($reservation->start) }}</td>
                            <td>{{ $parser::strtimeZuBeautiStunde($reservation->end,false) }}</td>
                            <td>{{ $reservation->user->displayName }}</td>
                            <td>{{ $reservation->anzahl }}</td>
                            <td>{{ $reservation->bemerkung }}</td>
                            <td>@if($canManage || $user->id == $reservation->user_id)
                                    <a href="/devices/reservation/delete/{{ $reservation->id }}" class="btn btn-default btn-xs" data-toggle="tooltip" data-placement="top" title="" data-original-title="Löschen"><i class="fa fa-trash"></i></a>
                                @endif
                            </td>
                        </tr>
                        @php $counter++; @endphp
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card mt-2">
                <div class="card-header">
            <h5>Verfügbarkeit <a href="/devices/ressource/view/{{ $ressource->id }}?page={{ $page -1 }}" class="btn btn-sm m-1 btn-primary"><</a><a href="/devices/ressource/view/{{ $ressource->id }}?page={{ $page +1 }}" class="btn btn-sm m-1 btn-primary">></a></h5>
                </div>
                <div class="card-body">
                    @php $l = 0; @endphp
                    @foreach($weeks as $week)
                        <h5>Woche {{ $startdate->copy()->addWeek($l)->format('W., d.m.')  }} - {{ $startdate->copy()->addWeek($l+1)->format('d.m.')  }}</h5>
                        <div class="row">
                            <div style="width: 10%; float:left;">
                                \
                            </div>
                            @for($i = 0; $i < $stunden; $i++)
                                <div style="height:20px; width: {{(100 - 10)/$stunden}}%; float:left; border: 1px solid #fff; text-align: center">
                                    {{  $i + 1 }}. Stunde
                                </div>
                            @endfor
                        </div>
                        @for($c = 0; $c< 7; $c++)
                            <div class="row">
                                <div style="width: 10%; float:left;">
                                        {{ $startdate->copy()->addDays($c)->formatLocalized('%A')  }}

                                </div>
                                @for($i = 0; $i< $stunden; $i++)
                                    <div style="height:20px; width: {{(100 - 10)/$stunden}}%; padding-left: 3px; color: #f0f0f0; background-color: {{ $week[$c][$i] }}; float:left; border: 1px solid #fff;">
                                        {{ $description[$l][$c][$i] }}
                                    </div>
                                @endfor
                            </div>
                        @endfor
                        @php $l++; @endphp
                    @endforeach

                        <br>
                        <h5>Legende:</h5>
                        <div class="row">
                            <div style="height:20px; width: 10%; background-color: #00C853; float:left; border: 1px solid #fff;">
                            </div>
                            <div style="width: 15%; float:left;">
                                Frei
                            </div>
                            <div style="height:20px; width: 10%; background-color: #bf2718; float:left; border: 1px solid #fff;">
                            </div>
                            <div style="width: 15%; float:left;">
                                Gebucht
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
@endsection
