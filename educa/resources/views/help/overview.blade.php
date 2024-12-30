@extends('layouts.help')

@section('pageContent')
    <style>
        .helpContainer {
            margin-top: 50px;
        }
    </style>

    <div class="modal fade" id="addBoard" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Hilfe-Artikel anlegen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="/help/add">
                    @csrf

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Name</label>
                            <input name="name" type="text" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                        <button type="submit" class="btn btn-primary">Erstellen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container helpContainer">
        <h1 class="text-center">  <img src="/images/help.png" width="120" height="120" class="rounded img-fluid appIcon" alt=""> educa - Hilfe und Anleitungen</h1>
        <form action="/help" method="POST">
            @csrf
            <div class="form-group row">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="q" placeholder="Stichwort, Suchwort ...">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="submit" id="button-addon2">Suche <i class="fas fa-search"></i></button>
                    <button class="btn btn-success" data-toggle="modal" data-target="#addBoard" type="button" id="button-addon3">Hinzufügen <i class="fas fa-plus"></i></button>
                </div>
            </div>
            </div>
        </form>


    </div>
    <div class="container">
        @foreach($artikels as $artikel)
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $artikel->title }}</h5>
                    <p class="card-text">
                        {!! strip_tags($artikel->content) !!}</p>
                    <a href="/help/edit/{{ $artikel->id }}" class="btn btn-secondary"><i class="fas fa-edit"></i> Bearbeiten</a>
                    <a href="/help/view/{{ $artikel->id }}" class="btn btn-primary"><i class="fas fa-eye"></i> Lesen</a>
                </div>
            </div>
        @endforeach
    </div>
@endsection
