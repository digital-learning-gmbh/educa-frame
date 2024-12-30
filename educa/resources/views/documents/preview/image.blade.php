@extends('app')

@section('content')
    @include('documents.preview.head')
    <div class=" text-center">
    <img src="/dokument/{{ $dokument->id }}/download" class="img-fluid"/>
    </div>
@endsection
