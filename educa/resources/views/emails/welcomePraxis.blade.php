@extends('beautymail::templates.widgets')

@section('content')

    @include('beautymail::templates.widgets.newfeatureStart')

    <h4 class="secondary"><strong>Hallo {{ $kontakt->name }}</strong></h4>
    <p>Es freut uns, dass wir Sie heute kontaktieren dürfen, da {{ $user->name }} eine Account für Sie bei educa erstellt hat.</p>
    <p>Die Plattform educa ermöglicht es Praxiseinsätze zu koordinieren, Fehlzeiten auszutauschen und die Teilnehmer immer im Überblick zu behalten.</p>
    <p>Bei weiteren Fragen wenden Sie sich bitte an die Schule / Einrichtung.</p>
    @include('beautymail::templates.widgets.newfeatureEnd')


    @include('beautymail::templates.widgets.articleStart')

    <h4 class="secondary"><strong>Wie können Sie sich anmelden?</strong></h4>
    <p><a href="https://fuu.stupla.online">https://fuu.stupla.online</a></p>
    <p>Benutzername: <b>{{ $kontakt->email }}</b></p>
    <p>Passwort: <b>{{ $password }}</b></p>

    @include('beautymail::templates.widgets.articleEnd')

@stop
