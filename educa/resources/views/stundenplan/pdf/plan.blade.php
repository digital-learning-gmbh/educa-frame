<html lang="de"><head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Benjamin Ledel">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <link href="/css/app.css" rel="stylesheet">
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

<style>
    @media print {
        body{
            margin: 30mm 45mm 30mm 45mm;
            color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }
    }
    @if($orientation == "landscape")

@media print {
        @page {
            size: landscape;
        }
    }
    @else

@media print {
        @page {
            size: portrait;
        }
        body {
            width: 29.7cm;
            height: 21cm;
        }
    }
    @endif

        div.cut-text {
        text-overflow: ellipsis;
        overflow: hidden;
        width: 100px;
        height: 1.2em;
        white-space: nowrap;
        display: block;
    }
    body {
        background-color: white !important;
        font-family: sans-serif;
        width: 100%;
        height: 100%;
        margin: 0;
    }
    #container {
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: space-between;
        flex-direction: column;
    }

    #kalender-header {
        width: 100%;
        display: flex;
        justify-content: space-between;
        border-right: 2px solid black;
    }

    #kalender {
        height: 100%;
        width: 100%;
        display: flex;
        justify-content: space-between;
        border-right: 2px solid black;

        background: linear-gradient(180deg,
                @for($i = 1; $i <= sizeof($uhrzeit_slots); $i++)
                rgba(0,0,0,0) calc( {{ $i * 100 / sizeof($uhrzeit_slots) }}% - 1px),
        grey calc({{ $i * 90 / sizeof($uhrzeit_slots) }}%),
        rgba(0,0,0,0) calc({{ $i * 100 / sizeof($uhrzeit_slots) }}%)@if($i < sizeof($uhrzeit_slots)),@endif
                @endfor
            );
    }

    .spalte {
        vertical-align: top;
        display: inline-block;
        border-left: 2px solid black;
        border-bottom: 2px solid black;
        width: 100%;
    }
    .spalte.uhrzeit {
        width: 10%;
        min-width: 90px;
    }
    .zelle {
        box-sizing: border-box;
        padding-top: 4px;
        padding-bottom: 4px;
        padding-left: 4px;
        padding-right: 4px;
        overflow-wrap: break-word;
        overflow: hidden;
    }

    .zelle.header {
        font-size: 15px;
        font-weight: bold;
        text-align: center;
        border-top: 2px solid black;
    }
    .zelle.uhrzeit {
        font-size: 10px;
        text-align: center;
        vertical-align: middle;
    }
    .zelle.leer {
        display: flex;
        justify-content: space-between;
        padding-top: 0px;
        padding-bottom: 0px;
        padding-left: 1px;
        padding-right: 1px;
    }
    .zelle.early {
        border-radius: 0 0 3px 3px !important;
        border-top: none !important;
    }
    .zelle.late {
        border-radius: 3px 3px 0 0 !important;
        border-bottom: none !important;
    }
    .zelle.early.late {
        border-radius: 0 !important;
    }
    .zelle.slot {
        border-radius: 3px;
        margin-left: 3px;
        margin-right: 3px;
        @if(!$color)
border: 1px solid grey;
    @endif
}
    .zelle.slot.multi {
        margin-left: 2px;
        margin-right: 2px;
    }
    .zelle .titel {
        font-weight: bold;
        font-size: 11px;
        display: inline;
    }
    .zelle .zeitraum {
        font-size: 11px;
        float: right;
    }

    .zelle .info {
        font-size: 8px;
        float: left;
    }

    .subspalte {
        vertical-align: top;
        display: inline-block;
    }
</style>
<script>
    (function() {
        window.print();
    })();
</script>
<div id="container">
    @include(config('stupla.timetable.'.$school->id.'.print.customHeader', config('stupla.timetable.print.customHeader' ,'stundenplan.snippets.header')))
    <div id="kalender-header">
        <div class="uhrzeit spalte">
            <div class="zelle header">
                Uhrzeit
            </div>
        </div>@for($i=0; $i<sizeof($headers); $i++)<div class="spalte">
            <div class="zelle header">
                {!! $headers[$i] !!}
            </div>
        </div>@endfor

    </div>
    <div id="kalender">
        <div class="uhrzeit spalte">
            @foreach($uhrzeit_slots as $uhrzeit_slot)
                <div class="zelle uhrzeit" style="height: {{ 100 / sizeof($uhrzeit_slots) }}%">
                    {{ $uhrzeit_slot }}
                </div>
            @endforeach
        </div>@for($i=0; $i<sizeof($headers); $i++)<div class="spalte">
            @foreach($slots[$i] as $slot)
                @if($slot["is_slot"])
                    @if($color)
                        <div class="zelle slot {{ $slot["additional_classes"] }}" style="color: {{ $slot["eventTextColor"] }}; background-color: {{ $slot["color"] }}; height: {{ $slot["height"] }}%;">
                            @else
                                <div class="zelle slot {{ $slot["additional_classes"] }}" style="height: {{ $slot["height"] }}%; background-color: white;">
                                    @endif
                                    <div class="titel">
                                        {{ $slot["title"] }}
                                    </div>
                                    <div class="zeitraum">
                                        {{ $slot["start_time"] }} - {{ $slot["end_time"] }}
                                    </div>
                                    <div class="info">
                                        <i class="fa fa-user-tie mr-1"></i>{{ $slot["dozent"] }}<br>
                                        <i class="fa fa-home mr-1"></i>{{ $slot["raum"] }}<br>
                                        <i class="fa fa-home mr-1"></i>{{ $slot["klassen_name"]->implode(", ") }}
                                    </div>
                                </div>
                                @else
                                    <div class="zelle leer" style="height: {{ $slot["height"] }}%;">
                                        @if($slot["is_multi"])
                                            @foreach($slot["spalten"] as $subspalte)
                                                <div class="subspalte">
                                                    @foreach($subspalte as $subslot)
                                                        @if($subslot["is_slot"])
                                                            @if($color)
                                                                <div class="zelle slot multi {{ $subslot["additional_classes"] }}" style="color: {{ $subslot["eventTextColor"] }}; background-color: {{ $subslot["color"] }}; height: {{ $subslot["height"] }}%;">
                                                                    @else
                                                                        <div class="zelle slot multi {{ $subslot["additional_classes"] }}" style="height: {{ $subslot["height"] }}%; background-color: white;">
                                                                            @endif
                                                                            <div class="titel">
                                                                                {{ $subslot["title"] }}
                                                                            </div>
                                                                            <div class="zeitraum">
                                                                                {{ $subslot["start_time"] }} - {{ $subslot["end_time"] }}
                                                                            </div>
                                                                            <div class="info">
                                                                                <i class="fa fa-user-tie mr-1"></i>{{ $subslot["dozent"] }}<br>
                                                                                <i class="fa fa-home mr-1"></i>{{ $subslot["raum"] }}<br>
                                                                                <i class="fa fa-home mr-1"></i>{{ $subslot["klassen_name"]->implode(", ") }}
                                                                            </div>
                                                                        </div>
                                                                        @else
                                                                            <div class="zelle leer" style="height: {{ $subslot["height"] }}%;">
                                                                            </div>
                                                                        @endif
                                                                        @endforeach
                                                                </div>
                                                                @endforeach
                                                            @endif
                                                </div>
                                                @endif
                                            @endforeach
                                    </div>@endfor
                        </div>

                        @include(config('stupla.timetable.'.$school->id.'.print.customFooter', config('stupla.timetable.print.customFooter' ,'stundenplan.snippets.footer')))
        </div>

<script src="/js/app.js"></script>
<script src="/js/react/app-react.js"></script>
<script src="/js/datatables.min.js"></script>
<script src="/js/jquery-ui.min.js"></script>
<script src="/js/jquery.ui-contextmenu.min.js"></script>
<script src="/js/bootbox.all.min.js"></script>

<script type="module">
    // Import the functions you need from the SDKs you need
    import { initializeApp } from "https://www.gstatic.com/firebasejs/9.6.1/firebase-app.js";
    import { getAnalytics } from "https://www.gstatic.com/firebasejs/9.6.1/firebase-analytics.js";
    // TODO: Add SDKs for Firebase products that you want to use
    // https://firebase.google.com/docs/web/setup#available-libraries

    // Your web app's Firebase configuration
    // For Firebase JS SDK v7.20.0 and later, measurementId is optional
    const firebaseConfig = {
        apiKey: "AIzaSyBZdcpYQSM23r1VOCc_-6FF0m5sUok33oc",
        authDomain: "myiba-a3780.firebaseapp.com",
        projectId: "myiba-a3780",
        storageBucket: "myiba-a3780.appspot.com",
        messagingSenderId: "794494371737",
        appId: "1:794494371737:web:d798120b48f49c9d0ef508",
        measurementId: "G-EJDDN1PGPD"
    };

    // Initialize Firebase
    const app = initializeApp(firebaseConfig);
    const analytics = getAnalytics(app);
</script>

@yield('additionalScript')
<script>
    $(document).ready(function() {
        $('.data-table').each(function(){
            $(this).DataTable({
                colReorder: true,
                dom: 'fBrtlip',
                buttons: [ 'print','excel', 'pdf', 'colvis'
                ],
                language : {
                    url: "/js/german.json"
                },
                pageLength: 50
            });
        });
    });

</script>
@yield('additionalScript2')
</body>
</html>
