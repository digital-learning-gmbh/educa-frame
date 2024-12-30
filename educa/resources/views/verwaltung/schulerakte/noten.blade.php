@extends('verwaltung.schulerakte.main')

@section('siteContent')
    @if (session()->has('status'))
        <div class="alert alert-success">
            {{ session()->get('status') }}
        </div>
    @endif
    <h2>Noten Übersicht</h2>
    @foreach($result as $schuljahr => $notes)
    <div class="card mt-2">
        <div class="card-header">
            <h5>{{ $schuljahr }}</h5>
        </div>
        <div class="card-body">
            <div class="list-group list-group-flush">
                @foreach($notes as $fach => $noteFach)
                        <b>{{$fach}}</b>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Schriftlich
                                <ul class="list-group list-group-flush">
                                    @foreach($noteFach as $key => $note)
                                        @if($key !=="gesamtnote" && $note->typ == 1)
                                            <li class="list-group-item">{{date_format(new DateTime($note->datum),"d.m.Y")}}: <b>{{number_format((float)$note->note, 1, '.', '')}}</b> ({{$note->gewicht*100}}%) <div class="float-right">{{$note->gewicht}} x <b>{{number_format((float)$note->note, 1, '.', '')}}</b></div></li>
                                        @endif

                                    @endforeach
                                </ul>
                            </li>
                            <li class="list-group-item">Mündlich
                                <ul class="list-group list-group-flush">
                                    @foreach($noteFach as $key => $note)
                                        @if($key !=="gesamtnote" && $note->typ == 0)
                                            <li class="list-group-item">{{date_format(new DateTime($note->datum),"d.m.Y")}}: <b>{{number_format((float)$note->note, 1, '.', '')}}</b> ({{$note->gewicht*100}}%) <div class="float-right">{{$note->gewicht}} x <b>{{number_format((float)$note->note, 1, '.', '')}}</b></div></li>
                                        @endif

                                    @endforeach
                                </ul>
                            </li>
                            <li class="list-group-item"><b>Gesamtnote</b> <div class="float-right"><b>{{number_format((float)$noteFach["gesamtnote"], 2, '.', '')}}</b></div></li>

                        </ul>
                @endforeach
            </div>


        </div>
    </div>

    @endforeach
@endsection
