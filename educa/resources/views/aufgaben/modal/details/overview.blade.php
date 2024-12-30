@extends('layouts.aufgaben')

@section('pageContent')
    <style>
        .card {
            background-color: #f7f7f7;
        }
    </style>
<div class="container-fluid" style="margin-top: 10px;">
    @include('aufgaben.modal.details.header')
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link active" href="/tasks/detail/{{ $event->id }}"><i class="fas fa-calendar-week"></i>  Aufgabenstellung</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/tasks/detail/{{ $event->id }}/files"><i class="fas fa-folder-open"></i> Dateien</a>
        </li>
        @if($event->handIn != "no")
            @if($event->cloudid != $cloud_user->id)
        <li class="nav-item">
            <a class="nav-link" href="/tasks/detail/{{ $event->id }}/handIn"><i class="fas fa-envelope-open-text"></i> Einreichung</a>
        </li>
            @else
        <li class="nav-item">
            <a class="nav-link" href="/tasks/detail/{{ $event->id }}/handInAll"><i class="fas fa-clipboard-check"></i> Einreichung der Nutzer</a>
        </li>
            @endif
        @endif
    </ul>
    <div class="card" style="margin-top: 20px;">
        <div class="card-body">
    <form method="POST">
        @csrf
        <div class="form-group row">
            <label for="staticEmail" class="col-sm-2 col-form-label"><i class="fas fa-pencil-alt"></i> Titel</label>
            <div class="col-sm-10">
                <input value="{{ $event->title }}" name="title" type="text" class="form-control" placeholder="Titel hinzufügen" required>
            </div>
        </div>
        <div class="form-group row">
            <label for="staticEmail" class="col-sm-2 col-form-label"><i class="fas fa-list-ul"></i> Aufgabenstellung</label>
            <div class="col-sm-10">
                 <textarea class="editor" name="description">
                {!! $event->description !!}
                </textarea>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label"><i class="fas fa-calendar-week"></i> Zeitbegrenzung</label>
            <div class="col-sm-4">
                <div class="input-group date" id="datepicker11" data-target-input="nearest">
                    <input value="{{ date("d.m.Y H:i",strtotime($event->startDate)) }}" id="besuchsdatum" required name="begin" type="text" class="form-control datetimepicker-input" data-target="#datepicker11"/>
                    <div class="input-group-append" data-target="#datepicker11" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-2" style="text-align: center;">
                <i class="fas fa-arrow-right" style="font-size: 26px;"></i>
            </div>
            <div class="col-sm-4">
                <div class="input-group date" id="datetimepicker2" data-target-input="nearest">
                    <input value="{{ date("d.m.Y H:i",strtotime($event->startDate)) }}" required name="end" type="text" class="form-control datetimepicker-input" data-target="#datetimepicker2"/>
                    <div class="input-group-append" data-target="#datetimepicker2" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label"><i class="fas fa-users"></i> Gruppe(n)</label>
            <div class="col-sm-10">
                <select class="select2" multiple name="gruppe[]" id="gruppe" required>
                    @foreach($event->gruppen as $gruppe)
                        <option value="{{ $gruppe->id }}" selected>{{ $gruppe->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label"><i class="fas fa-clipboard-check"></i> Abgabeformat</label>
            <div class="col-sm-10">
                <select class="select2" name="format">
                    <option value="no" selected>Keine Abgabe erforderlich</option>
                    <option value="text">Antworttext</option>
                    <option value="file">Dokumente</option>
                </select>
            </div>
        </div>
        <a href="/tasks" class="btn btn-secondary">Zurück</a>

        @if($event->cloudid == $cloud_user->id)
        <button type="submit"  class="btn btn-primary">Aktualisieren</button>
        @endif
    </form>
        </div>
    </div>
</div>
@endsection

@section("additionalScript")
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
    <script>
        $("#tags").select2({
            tags: true,
            theme: 'bootstrap4',
        });
        $("#teilnehmer").select2({
            minimumInputLength: 0,
            theme: 'bootstrap4',
            ajax: {
                url: "/api/search/clouduser",
                dataType: 'json',
                data: function (params) {
                    return {
                        q: params.term, // search term
                    };
                }
            },
        });
        $("#gruppe").select2({
            minimumInputLength: 0,
            theme: 'bootstrap4',
            ajax: {
                url: "/api/search/group",
                dataType: 'json',
                data: function (params) {
                    return {
                        q: params.term, // search term
                    };
                }
            },
        });
    </script>
@endsection
