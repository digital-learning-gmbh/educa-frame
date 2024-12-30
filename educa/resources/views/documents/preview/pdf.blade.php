@extends('app')

@section('content')
    @include('documents.preview.head')
    <iframe src="/pdfjs/web/viewer.html?file=/dokument/{{ $dokument->id }}/download" style="height: calc(100vh - 55px); width: 100%; border: none;">
    </iframe>

@endsection
