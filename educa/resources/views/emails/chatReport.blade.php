@extends('beautymail::templates.widgets')

@section('content')

    @include('beautymail::templates.widgets.newfeatureStart')

    <h4 class="secondary"><strong>Sehr geehrte Damen und Herren</strong></h4>
    <p>Es wurde eine Chat-Meldung über educa geschickt. Sie sehen die Details unten.</p>
    <p><b>Zusätzliche Informationen</b></p>
    <p>{{ $object["additionalInfo"] }}</p>
    @include('beautymail::templates.widgets.newfeatureEnd')


    @include('beautymail::templates.widgets.articleStart')

    <h4 class="secondary"><strong>Protokoll:</strong></h4>
    @foreach($object["msgChunk"]["messages"] as $message)
    <p><b>{{ $message["u"]["name"] }}</b> {{ $message["msg"] }}</p>
    @endforeach

    @include('beautymail::templates.widgets.articleEnd')

@stop
