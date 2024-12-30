@extends('layouts.devices')

@section('pageContent')
    <div class="container-fluid mt-2">
        <div class="row">
            <div class="col-8">
        <div class="card">
            <div class="card-header">
                <h5>Alle Geräte</h5>
            </div>
<table class="table table-striped data-table" cellspacing="0" width="100%">
    <thead>
    <tr>
        <th>Anzeigename</th>
        <th>Beschreibung</th>
        <th>Aktion</th>
    </tr>
    </thead>
    <tfoot>
    <tr>
        <th>Anzeigename</th>
        <th>Beschreibung</th>
        <th>Aktion</th>
    </tr>
    </tfoot>
    <tbody>
    @foreach($ressourcen as $resource)
        <tr>
            <td>{{ $resource->name }}</td>
            <td>{{ $resource->text }}</td>
            <td><a href="/ressourcen/ressource/view/{{ $resource->id }}" class="btn btn-default btn-xs" data-toggle="tooltip" data-placement="top" title="" data-original-title="Bearbeiten"><i class="fa fa-pencil"></i></a>
                <a href="/ressourcen/ressource/view/{{ $resource->id }}" class="btn btn-default btn-xs" data-toggle="tooltip" data-placement="top" title="" data-original-title="Ansehen"><i class="fa fa-eye"></i></a>
                <a href="/ressourcen/ressource/delete/{{ $resource->id }}" class="btn btn-default btn-xs" data-toggle="tooltip" data-placement="top" title="" data-original-title="Löschen"><i class="fa fa-trash"></i></a></td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>
            </div>
            <div class="col-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Termine blockieren</h5>
                    </div>
    <div class="card-body">
        <form class="form-horizontal" role="form" method="post" action="/devices/ressource/block">
            {{ csrf_field() }}

            <div class="form-group row">
                <label for="fach" class="col-sm-3 control-label">Beginn</label>

                <div class="col-sm-5">
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
            <div class="form-group row">
                <label for="fach" class="col-sm-3 control-label">Ende</label>
                <div class="col-sm-5">
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
            <div class="form-group row">
                <label for="fach" class="col-sm-3 control-label">Gerät</label>
                <div class="col-sm-9">
                    <select name="resource" style="width: 100%;" class="select2">
                        @foreach($ressourcen as $resource)
                            <option value="{{ $resource->id }}">{{ $resource->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="fach" class="col-sm-3 control-label">Wiederholung</label>
                <div class="col-sm-4">
                    <select name="wiederholung" style="width: 100%;" class="select2">
                        <option value="none">keine</option>
                        <option value="eachDay">jeden Tag</option>
                        <option value="eachWeek">jede Woche</option>
                        <option value="eachMonth">jeden Monat</option>
                    </select>
                </div>
                <div class="col-sm-5">
                    <div class="input-group date" id="datepicker3" data-target-input="nearest">
                        <input name="realend" id="datepicker3" type="text" class="form-control datetimepicker-input" autocomplete="off" data-target="#datepicker3">
                        <div class="input-group-append" data-target="#datepicker3" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label for="fach" class="col-sm-3 control-label">Bemerkung</label>
                <div class="col-sm-9">
                    <textarea class="form-control" name="bemerkung" style="width: 100%;" data-toggle="select2" rows="3" ></textarea>
                </div>
            </div>
            <div class="form-group row margin-none">
                <div class="col-sm-offset-3 col-sm-9">
                    <button type="submit" class="btn btn-danger">Blockieren</button>
                </div>
            </div>
        </form>
    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
