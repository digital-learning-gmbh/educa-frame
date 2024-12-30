@extends('app')

@section('content')
    <style>
        body
        {
            margin: 0px !important;
        }
    </style>
    <div id="placeholder" style="height: 100%"></div>

@endsection
@section('additionalScript')
    <script type="text/javascript" src="https://office.schule-plus.com/web-apps/apps/api/documents/api.js"></script>
    <script type="text/javascript">
        var onRequestMailMergeRecipients = function() {
            window.docEditor.setMailMergeRecipients({
                "fileType": "xlsx",
                "url": '{{ URL::to("/analytics/report/ajax/1/excel") }}'
            });
        };
        window.docEditor = new DocsAPI.DocEditor("placeholder",
            {
                "document": {
                    "fileType": "{{ $dokument->file_type }}",
                    "key": "{{ $_SERVER['HTTP_HOST'].$dokument->id }}",
                    "title": "{{ $dokument->name }}",
                    "url": '{{ URL::to("/dokument/".$dokument->id."/download") }}'
                },
                "documentType": "text",
                "editorConfig": {
                    "lang" : "de",
                    "user" : {
                        "id": '{{ $cloud_user->id}}',
                        "name": '{{ $cloud_user->name }}'
                    },
                    "callbackUrl": '{{ URL::to("/api/dokument/". $dokument->id) }}',
                },
                "height": "100%",
                "width": "100%",
                "events": {
                    "onRequestMailMergeRecipients": onRequestMailMergeRecipients,
                },
            });

    </script>
@endsection
