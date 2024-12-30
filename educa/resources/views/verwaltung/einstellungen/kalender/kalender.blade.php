@extends('verwaltung.main')

@section('siteContent')
    <h3><a href="/verwaltung/einstellungen/kalender">{{ __('Kalender') }}</a> / {{$kalender->name}}</h3>
    <div class="card mt-2">
        <div class="card-body">
            <h5 class="card-title">Ferienzeiten im Kalender "{{$kalender->name}}"</h5>
            <form class="form" method="POST" action="{{route('kalender.rename',$kalender->id)}}">
                @csrf
                <div class="form-group row">
                    <label for="staticEmail" class="col-sm-2 col-form-label">Bezeichnung</label>
                    <div class="col-sm-8">
                        <input class="form-control" type="text" value="{{ $kalender->name }}" required name="name">
                    </div>
                    <button type="submit" class="btn btn-primary mb-2">Speichern</button>
                </div>
            </form>
            <div style="margin-bottom: 3px;">
                <div class="float-right">
                    <a href="#" data-toggle="modal" data-target="#importKalenderModal" class="btn btn-secondary">Import (iCal)</a>
                    <a href="#" data-toggle="modal" data-target="#createFerienModal" class="btn btn-primary">Hinzufügen</a>
                </div>
                <div class="clearfix"></div>
            </div>
            <table id="table_id" class="data-table table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Beginn</th>
                    <th>Ende</th>
                    <th>Bezeichnung</th>
                    <th>Aktion</th>
                </tr>
                </thead>
                <tbody>
                @foreach($ferienzeits as $ferienzeit)
                    <tr>
                        <td data-order="{{ strtotime($ferienzeit->start) }}">
                            {{ date("d.m.Y, H:i", strtotime($ferienzeit->start)) }}
                        </td>
                        <td data-order="{{ strtotime($ferienzeit->end) }}">
                            {{ date("d.m.Y, H:i", strtotime($ferienzeit->end)) }}
                        </td>
                        <td>
                            {{ $ferienzeit->name }}
                        </td>
                        <td>
                            <a href="#" class="btn btn-primary" onclick="edit('{{ $ferienzeit->id }}', '{{ $ferienzeit->name }}','{{ date("d.m.Y, H:i", strtotime($ferienzeit->start)) }}', '{{ date("d.m.Y, H:i", strtotime($ferienzeit->end)) }}');">
                                <i class="fas fa-pen"></i>
                            </a>
                            <a href="{{route('kalender.deleteFerienzeit', ["id" => $kalender->id, "id2" => $ferienzeit->id])}}" class="btn btn-danger">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>


    <div class="modal fade" id="createFerienModal" tabindex="-1" role="dialog" aria-labelledby="createFerienModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createFerienModalLabel">Ferienzeit hinzufügen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form" method="POST" action="{{route('kalender.addFerienzeit', ["id" => $kalender->id])}}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" class="form-control" id="id" name="id" value="-1">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Bezeichnung</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="begin">Beginn</label>
                            <div class="input-group date" id="datetimepicker1" data-target-input="nearest">
                                <input required name="begin" id="begin" type="text" class="form-control datetimepicker-input" data-target="#datetimepicker1"/>
                                <div class="input-group-append" data-target="#datetimepicker1" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="begin">Ende</label>
                            <div class="input-group date" id="datetimepicker2" data-target-input="nearest">
                                <input required name="end" id="end" type="text" class="form-control datetimepicker-input" data-target="#datetimepicker2"/>
                                <div class="input-group-append" data-target="#datetimepicker2" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
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

    <div class="modal fade" id="importKalenderModal" tabindex="-1" role="dialog" aria-labelledby="createFerienModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createFerienModalLabel">Import (iCal)</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form" method="POST" action="{{route('kalender.importFerien', ["id" => $kalender->id])}}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" class="form-control" id="id" name="id" value="-1">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>iCal Kalender-Datei</label>
                            <input type="file" class="form-control" id="name" name="file" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Hochladen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('additionalScript')
    <script>
        function edit(id, name, start, ende)
        {
            $('#id').val(id);
            $('#name').val(name);
            $('#begin').val(start);
            $('#end').val(ende)
            $('#createFerienModal').modal('show')
        }
    </script>
@endsection
