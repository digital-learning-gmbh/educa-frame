@extends('layouts.help')

@section('pageContent')
    <style>
        body {
            padding-top: 0px;
            margin-left: 0px;
            margin-right: 0px;
            margin-bottom: 0px;
        }
    </style>
    <nav class="navbar navbar-dark bg-dark navbar-expand-lg">

        <a class="navbar-brand" href="#">
            {{ $artikel->title }}
        </a>
        <div class="collapse navbar-collapse"  id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="btn btn-sm btn-light" href="/help/edit/{{ $artikel->id }}">Bearbeiten</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="editor">
        {!! $artikel->content !!}
    </div>

@endsection
@section('additionalScript')
<<<<<<< HEAD

=======
>>>>>>> bugfix2
    <script src="{{ mix('/js/ckeditor.js') }}"></script>
    <script src="{{ mix('/js/editor.js') }}"></script>
    <script>
        $(document).ready(function() {
            window.editor.isReadOnly = true;
        });
    </script>
@endsection
