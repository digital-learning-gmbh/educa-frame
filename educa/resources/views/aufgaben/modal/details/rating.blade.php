@extends('layouts.aufgaben')

@section('pageContent')
    <style>
        .card {
            background-color: #f7f7f7;
        }
        table {
            background-color: white;
        }
    </style>
<div class="container-fluid" style="margin-top: 10px;">
    @include('aufgaben.modal.details.header')
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link " href="/tasks/detail/{{ $event->id }}"><i class="fas fa-calendar-week"></i>  Aufgabenstellung</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/tasks/detail/{{ $event->id }}/files"><i class="fas fa-folder-open"></i> Dateien</a>
        </li>
        @if($event->handIn != "no")
            @if($event->cloudid != $cloud_user->id)
        <li class="nav-item">
            <a class="nav-link " href="/tasks/detail/{{ $event->id }}/handIn"><i class="fas fa-envelope-open-text"></i> Einreichung</a>
        </li>
            @else
        <li class="nav-item">
            <a class="nav-link active" href="/tasks/detail/{{ $event->id }}/handInAll"><i class="fas fa-clipboard-check"></i> Einreichung der Nutzer</a>
        </li>
            @endif
        @endif
    </ul>
    <h2 class="mt-2">Abgabe von: {{ $einreichung->ersteller->name }}</h2>
    <div class="row">
        <div class="col-md-6">
            <div class="card" style="margin-top: 20px;">
                <div class="card-body">
                    @if($event->handIn == "file")
                        <h5 class="card-title">Dateien</h5>
                        <h6 class="card-subtitle mb-2 text-muted">Bitte lade hier alle Dokumente und Dateien hoch, die die Aufgabe beantworten. Zur Struktur kannst du auch Ordner anlegen</h6>
                        @component('documents.list',[ "model" => $einreichung, "type" => "einreichung"])
                        @endcomponent
                    @elseif($event->handIn == "text")
                        <h5 class="card-title">Text</h5>
                        <h6 class="card-subtitle mb-2 text-muted">Um die Aufgabe zu beantworten, kannst du folgendes Feld benutzen:</h6>


                        <div class="card" style="background-color: white">
                            <div class="card-body">
                {!! $einreichung->description !!}
                            </div>
                        </div>
                        @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card" style="margin-top: 20px;">
                <div class="card-body">
                    <form method="POST">
                        @csrf
                    <h5 class="card-title">Bewertung</h5>
                    <h6 class="card-subtitle mb-2 text-muted">Sie haben hier die Möglichkeit die Lösung zu bewerten und ein Feedback zu geben</h6>
                   <label>Punkte / Note </label>
                    <input name="note" type="text" class="form-control" placeholder="Geben Sie hier die Punkte oder eine Note ein, optional" value="{{ $einreichung->points }}">
                        <label class="mt-2">Feedback </label>
                    <textarea class="editor " name="feedback">
                {!! $einreichung->rating !!}
                </textarea>
                        <button type="submit"  class="mt-2 btn btn-primary">Speichern</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section("additionalScript")
    @if($event->handIn == "text")
    <script src="/ckeditor_inline/ckeditor.js"></script>
    <script>ClassicEditor
            .create( document.querySelector( '.editor' ), {

                toolbar: {
                    items: [
                        'heading',
                        '|',
                        'bold',
                        'italic',
                        'link',
                        '|',
                        'fontColor',
                        'fontFamily',
                        'fontSize',
                        '|',
                        'bulletedList',
                        'numberedList',
                        '|',
                        'indent',
                        'outdent',
                        '|',
                        'imageUpload',
                        'blockQuote',
                        'insertTable',
                        'mediaEmbed',
                        'undo',
                        'redo'
                    ]
                },
                language: 'de',
                image: {
                    toolbar: [
                        'imageTextAlternative',
                        'imageStyle:full',
                        'imageStyle:side'
                    ]
                },
                table: {
                    contentToolbar: [
                        'tableColumn',
                        'tableRow',
                        'mergeTableCells'
                    ]
                },
                licenseKey: '',

            } )
            .then( editor => {
                window.editor = editor;








            } )
            .catch( error => {
                console.error( 'Oops, something went wrong!' );
                console.error( 'Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:' );
                console.warn( 'Build id: s8z9nfdk7w4t-xpu28fnnqx3z' );
                console.error( error );
            } );
    </script>
    @endif
@endsection
