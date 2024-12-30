@extends('verwaltung.main')

@section('siteContent')
    <h3>{{ __('Kalender') }}</h3>
    <div class="card mt-2">
        <div class="card-body">
            <h6 class="card-subtitle mb-2 text-muted">Liste von Kalendern, die sich im System befinden.</h6>
            <div style="margin-bottom: 3px;">
                <div class="float-right">
                    <a href="#" data-toggle="modal" data-target="#createKalenderModal" class="btn btn-primary">Hinzufügen</a>
                </div>
                <div class="clearfix"></div>
            </div>
            <table id="table_id" class="data-table table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Aktiv</th>
                    <th>Link</th>
                    <th>Aktion</th>
                </tr>
                </thead>
                <tbody>
                @foreach($kalenders as $kalender)
                    <tr>
                        <td>{{ $kalender->name }}</td>
                        <td>@if($schule->getEinstellungen("kalender", 0) == $kalender->id)<i class="fas fa-check" aria-hidden="true"></i> @else <i class="fas fa-times" aria-hidden="true"></i>@endif </td>
                       <td> <input type="text" readonly class="form-control-plaintext" id="staticEmail" value="{{ \Illuminate\Support\Facades\URL::to("/api/kalender/?id=$kalender->id") }}">
                       </td>
                        <td>
                            <a href="/verwaltung/einstellungen/kalender/{{$kalender->id}}" class="btn btn-xs btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="/verwaltung/einstellungen/kalender/{{$kalender->id}}/activate" class="btn btn-xs btn-primary">
                                <i class="fas fa-check"></i>
                            </a>
                            <a href="/verwaltung/einstellungen/kalender/{{$kalender->id}}/copy" class="btn btn-xs btn-primary">
                                <i class="fas fa-copy"></i>
                            </a>
                            <a href="{{ \Illuminate\Support\Facades\URL::to("/api/kalender/?id=$kalender->id") }}" class="btn btn-xs btn-primary">
                                <i class="fas fa-download"></i>
                            </a>
                            <a href="/verwaltung/einstellungen/kalender/{{$kalender->id}}/delete" class="btn btn-danger">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>


    <div class="modal fade" id="createKalenderModal" tabindex="-1" role="dialog" aria-labelledby="createKalenderModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createKalenderModalLabel">Kalender hinzufügen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form" method="POST" action="{{route('kalender.create')}}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-check">
                            <input name="activate" class="form-check-input" type="checkbox" id="activate">
                            <label class="form-check-label" for="activate">Auf "aktiv" setzen</label>
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
