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
            <a class="nav-link active" href="/tasks/detail/{{ $event->id }}/handIn"><i class="fas fa-envelope-open-text"></i> Einreichung</a>
        </li>
            @else
        <li class="nav-item">
            <a class="nav-link" href="/tasks/detail/{{ $event->id }}/handInAll"><i class="fas fa-clipboard-check"></i> Einreichung der Nutzer</a>
        </li>
            @endif
        @endif
    </ul>

    @if($einreichung->stage == "draft")

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
            <form method="POST">
                @csrf
                <textarea class="editor" name="description">
                {!! $einreichung->description !!}
                </textarea>
                <button type="submit" class="btn btn-primary mt-2">Speichern</button>
            </form>
            @endif
        </div>
    </div>
    @else
        <div class="row">

            <div class="col-md-3">
            </div>
            <div class="col-md-6">
            <div class="alert alert-success mt-2" role="alert">
                Die Bewertung ist abgeschlossen!
            </div>
            </div>

                <div class="col-md-3">
                </div>

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
                        <h5 class="card-title">Bewertung</h5>
                        <h6 class="card-subtitle mb-2 text-muted">So wurde deine Aufgabe bewertet:</h6>
                        <label>Punkte: <b>{{ $einreichung->points }}</b></label>
                        <div class="card" style="background-color: white">
                            <div class="card-body">
                                {!! $einreichung->rating !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
</div>
@endsection

@section("additionalScript")

    @if($einreichung->stage == "draft")
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
    @endif
@endsection
