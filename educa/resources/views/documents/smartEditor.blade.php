@extends('layouts.loggedIn')

@section('appContent')
    <style>
        body {
            padding-top: 100px;
            margin-left: 0px;
            margin-right: 0px;
            margin-bottom: 0px;
        }
    </style>
    <div class="document-editor">
        <div class="document-editor__toolbar"></div>
        <div class="document-editor__editable-container">
            <div class="document-editor__editable editor">
                <p>The initial editor data.</p>
            </div>
        </div>
    </div>

@endsection
