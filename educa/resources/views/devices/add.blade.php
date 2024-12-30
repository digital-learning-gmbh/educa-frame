@extends('layouts.devices')

@section('pageContent')
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h5>Gerät hinzufügen</h5>
            </div>
        <div class="card-body">
            @foreach($errors->all() as $error)
                <p class="alert alert-danger">{{ $error }}</p>
            @endforeach
            <form class="form-horizontal" role="form" method="post" action="/devices/ressource/add">
                {{ csrf_field() }}

                <div class="form-group row">
                    <label for="name" class="col-sm-3 control-label">Anzeigename</label>
                    <div class="col-sm-9">
                        <input name="name" class="form-control" id="name" placeholder="Beamer, Moderationskoffer, ...">
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
                    <label class="col-sm-3 control-label">Beschreibung</label>
                    <div class="col-sm-9">
                        <textarea name="text" class="form-control" rows="5"></textarea>
                    </div>
                </div>
                <div class="form-group row margin-none">
                    <div class="col-sm-offset-3 col-sm-9">
                        <a href="/devices" class="btn btn-white">Abbrechen</a>
                        <button type="submit" class="btn btn-primary">Erstellen</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    </div>
@endsection
