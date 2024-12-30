@extends('beautymail::templates.widgets')

@section('content')

    @include('beautymail::templates.widgets.newfeatureStart')

    <h4 class="secondary"><strong>Hallo {{ $user->name }}</strong></h4>
    <p>Du hast ein neues Passwort für deinen Account bei educa angefordert. Nachfolgend senden wir dir die neuen Zugangsdaten.</p>
    @include('beautymail::templates.widgets.newfeatureEnd')


    @include('beautymail::templates.widgets.articleStart')

    <h4 class="secondary"><strong>Deine neuen Zugangsdaten:</strong></h4>
    <p>Benutzername: <b>{{ $user->email }}</b></p>
    <p>Passwort: <b>{{ $password }}</b></p>

    @include('beautymail::templates.widgets.articleEnd')

@stop
