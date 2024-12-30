@extends('layouts.klassenbuch')

@section('appContent')
    <div class="container">
            <div class="card">
                <div class="card-header"><b>Abwesenheit</b></div>
                <div class="card-body">
                    <div style="margin-bottom: 3px;">
                        <div class="float-right">
                            <a href="#" data-toggle="modal" data-target="#abwesenheitModal" class="btn btn-primary">Antrag anlegen</a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <table id="table_id" class="data-table table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>Datum (von)</th>
                            <th>Datum (bis)</th>
                            <th>Beantragt am</th>
                            <th>Grund</th>
                            <th>Status</th>
                            <th>Bemerkung</th>
                            <th>Aktion</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($lehrer->abwesenheit as $fehlzeit)
                        <tr>
                            <td>{{date('d.m.Y', strtotime($fehlzeit->startDate))}}</td>
                            <td>{{date('d.m.Y', strtotime($fehlzeit->endDate))}}</td>
                            <td>{{date('d.m.Y', strtotime($fehlzeit->requestDate))}}</td>
                            <td>{{$fehlzeit->reason}}</td>
                            <td>{{$fehlzeit->status}}</td>
                            <td>{{$fehlzeit->comment}}</td>
                            <td>
                                <a href="/dozent/abwesenheit/delete/{{$fehlzeit->id}}" class="btn btn-xs btn-danger"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
    </div>

    <div class="modal fade" id="abwesenheitModal" role="dialog" aria-labelledby="abwesenheitModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="abwesenheitModalLabel">Antrag anlegen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="abwesenheit_form" class="form" method="POST" action="/dozent/abwesenheit/create" enctype = "multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Start</label>
                            <div class="input-group date" id="datetimepicker1" data-target-input="nearest">
                                <input value="{{ date("d.m.Y H:i") }}" id="inputDate" name="start" type="text" class="form-control datetimepicker-input tobeHidden" data-target="#datetimepicker1" required/>
                                <div class="input-group-append" data-target="#datetimepicker1" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>

                            <label>Ende</label>
                            <div class="input-group date" id="datetimepicker2" data-target-input="nearest">
                                <input value="{{ date("d.m.Y H:i") }}" id="inputDate2" name="end" type="text" class="form-control datetimepicker-input tobeHidden" data-target="#datetimepicker2" required/>
                                <div class="input-group-append" data-target="#datetimepicker2" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>

                            <label>Grund</label>
                            <div class="input-group text">
                                <input class="form-control" type="text" name="reason" required>
                            </div>

                            <label>Bemerkung</label>
                            <div class="input-group text">
                                <input class="form-control" type="text" name="comment" required>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Anlegen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
