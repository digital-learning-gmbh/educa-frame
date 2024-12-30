@extends('layouts.devices')

@section('pageContent')
<div class="row">
    <div class="col-5">
    <div class="card mt-3">
        <div class="card-header">
            <h5>Meine Reservierungen</h5>
        </div>
        <div class="card-body">
        <table class="table data-table" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th>ab</th>
                <th>bis</th>
                <th>Gerät</th>
                <th>Anzahl</th>
                <th>Bemerkungen</th>
                <th>Aktion</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <th>ab</th>
                <th>bis</th>
                <th>Gerät</th>
                <th>Anzahl</th>
                <th>Bemerkungen</th>
                <th>Aktion</th>
            </tr>
            </tfoot>
            <tbody>
            @foreach($myReservations as $reservation)
                <tr>
                    <td>{{ $parser::strtimeZuBeautiStunde($reservation->start) }}</td>
                    <td>{{ $parser::strtimeZuBeautiStunde($reservation->end,false) }}</td>
                    <td>{{ $reservation->ressource->name }}</td>
                    <td>{{ $reservation->anzahl }}</td>
                    <td>{{ $reservation->bemerkung }}</td>
                    <td><a href="/devices/reservation/delete/{{ $reservation->id }}" class="btn btn-default btn-xs" data-toggle="tooltip" data-placement="top" title="" data-original-title="Löschen"><i class="fa fa-trash"></i></a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        </div>
    </div>
    </div>
    <div class="col-5">
    <div class="card mt-3">
        <div class="card-header">
            <h5>Verfügbarkeit für diese Woche</h5>
        </div>
        <div class="card-body">
    @foreach($verfugbarkeit as $ressource)
        <h5 class="mt-1"><a href="/devices/ressource/view/{{ $ressource["id"] }}">{{$ressource["name"]}}</a></h5>

            @php
                $startdate = $start->copy();
            @endphp
                <div class="row">
                    <div style="width: 100px; float:left;">
                        \
                    </div>
                    <div style="width: calc(100% - 100px);">
                    @for($i = 0; $i < $stunden; $i++)
                        <div style="height:20px; width: {{100/$stunden}}%; float:left; border: 1px solid #fff; text-align: center">
                            {{  $i +7  }}:00
                        </div>
                    @endfor
                    </div>
                </div>
                @for($c = 0; $c< 7; $c++)
                    <div class="row">
                        <div style="width: 100px; float:left;">
                            {{ $startdate->formatLocalized('%A')  }}
                            @php $startdate->addDays(1) @endphp
                        </div>
                        <div style="width: calc(100% - 100px);">
                        @for($i = 0; $i< $stunden; $i++)
                            <div style="height:20px; width: {{100/$stunden}}%; background-color: {{ $ressource["matrix"][$c][$i] }}; padding-left: 3px; color: #f0f0f0; float:left; border: 1px solid #fff;">
                                {{ $ressource["description"][$c][$i] }}
                            </div>
                        @endfor
                        </div>
                    </div>
                @endfor
    @endforeach
        </div>
    </div>
    </div>
        <div class="col-2">

            <div class="card mt-3">
                <div class="card-header">
                    <h5>Legende</h5>
                </div>
                <div class="card-body">
            <div class="row">
                <div style="height:20px; width: 10%; background-color: #00C853; float:left; border: 1px solid #fff;">
                </div>
                <div style="float:left;">
                    Frei
                </div>
                <div style="height:20px; width: 10%; background-color: #bf2718; float:left; border: 1px solid #fff; margin-left: 5px;">
                </div>
                <div style="float:left;">
                    Gebucht
                </div>
            </div>
                </div>
            </div>
        </div>
    </div>
@endsection
