@extends('klassenbuch.basic')

@section('siteContent')
    <style>
        .list-group-item ul{
            margin-top: 10px;
            margin-right: -15px;
            margin-bottom: -10px;
        }
        .list-group-item li{
            padding: 10px 15px 10px 3em;
            border: none;
            border-top: 1px solid #ddd;
        }
         .list-group-item li:before{
            content: '';
            display: block;
            position: absolute;
            left: 0;
            width: 100%;
            height: 1px;
            margin-top: -11px;
            background: #ddd;
        }
    </style>
    <div class="col-md-10">
        <div class="card">
            <div class="card-header"><b>Lernfortschritt der Klasse</b></div>
            <div class="accordion" id="accordionExample">
                <div class="card" id="lernfortschrittklassen_content">
                    <h6 class="text-center mt-1">Lade Fortschritt...</h6>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('additionalScript')
    <script>
        function updateKlasseLernfortschritt()
        {
            postDataHtml('/board/ajax/lernfortschrittKlasse', { klasse_id : '{{ $selectedKlasse->id }}', stichtag : '{{ date("Y-m-d H:i") }}' }, '{{ csrf_token() }}',(data) => {
                $('#lernfortschrittklassen_content').html(data);
            });
        }
        updateKlasseLernfortschritt();
    </script>
@endsection
