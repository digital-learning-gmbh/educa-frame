<html lang="de"><head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Benjamin Ledel">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ \App\Providers\AppServiceProvider::getTenant()->name }}</title>
    <link href="/css/app_branded.css" rel="stylesheet">

    <script type="text/javascript" src="https://oss.sheetjs.com/sheetjs/xlsx.full.min.js"></script>
    <link rel="apple-touch-icon" sizes="57x57" href="/icon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/icon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/icon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/icon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/icon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/icon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/icon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/icon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/icon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="/icon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/icon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/icon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/icon/favicon-16x16.png">
    <link rel="manifest" href="/icon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/icon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <style>
        body {
            background-color: #fff !important;
        }
    </style>
    @yield('additionalStyle')
    @viteReactRefresh
    @vite('resources/js/app-react.js')
</head>
<body class="@yield("bodyClass")">
<div class="modal fade" id="reportBug" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Neue Supportanfrage</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <label><b>Um welchen Typ von Supportanfrage handelt es sich?</b></label>
                <div class="custom-control custom-radio">
                    <input type="radio" id="customRadio1" value="support" name="bugType" class="custom-control-input" checked>
                    <label class="custom-control-label" for="customRadio1" >Rückfrage / Unklarheit</label>
                </div>
                <div class="custom-control custom-radio">
                    <input type="radio" id="customRadio2" value="error" name="bugType" class="custom-control-input">
                    <label class="custom-control-label" for="customRadio2">Fehler entdeckt</label>
                </div>
                <br>
                <label><b>Beschreibung</b></label>
                <div class="form-group">
                    <textarea required id="bugText" class="form-control" placeholder="Beschreib kurz, was deine Anfrage ist"></textarea>
                </div>
                <label><b>Screenshot</b></label>
                <div id="bugImage">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                <button type="button" id="sendBugReport" class="btn btn-primary">Erstellen</button>
            </div>
        </div>
    </div>
</div>
@if(isset($current_rcUser))
    <img src="https://analytics.educa-portal.de/tr.php?rc_token={{ $current_rcUser->getAuthToken() }}&rc_uid={{ $current_rcUser->getId() }}" width="1px" height="1px">
@endif
@yield("content")
@yield('additionalScript')
@yield('additionalScript2')
</body>
</html>
