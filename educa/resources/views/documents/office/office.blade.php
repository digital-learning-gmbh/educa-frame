@extends('app')
@section('title')
Office - {{ $title }} - educa
@endsection
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
    <script type="text/javascript" src="{{ $host }}/web-apps/apps/api/documents/api.js"></script>
    <script type="text/javascript">

        window.docEditor = new DocsAPI.DocEditor("placeholder",
            {
                "document" : {
                    "fileType": "{{ $fileType }}",
                    "title": "{{ $title }}",
                    "url": "{{ $documentUrl }}",
                    "key": "{{ $documentToken }}"
                },
                "documentType": "{{ $documentType }}",
                "editorConfig": {
                    "lang" : "de",
                    "mode": "{{ $mode }}",
                    "user": {
                        "name": "{{ $user->name }}"
                    },
                    "customization": {
                        "customer": {
                            "address": "Am Sportplatz 4",
                            "info": "Mit educa können Sie sofort das nächste Schuljahr planen, Unterrichtseinheiten gestalten oder (auf Distanz) unterrichten und lernen.",
                            "logo": "https://educa-portal.de/wp-content/uploads/2021/01/logo_educa.png",
                            "mail": "info@educa-portal.de",
                            "name": "Digital Learning GmbH",
                            "www": "www.educa-portal.de"
                        },
                        "logo": {
                            "image": "https://educa-portal.de/wp-content/uploads/2021/01/logo_educa_inverted-1024x372.png",
                            "imageEmbedded": "https://educa-portal.de/wp-content/uploads/2021/01/logo_educa_inverted-1024x372.png",
                            "url": "https://educa-portal.de"
                        },
                        "help": false
                    },
                    "callbackUrl": "{{ $callbackUrl }}"
                },
                "height": "100%",
                "width": "100%"
            });

    </script>
@endsection
