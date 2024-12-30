@extends('layouts.loggedIn')

@section('appContent')
    <style>
        .list-group-item2 {
            position: relative;
            display: block;
            padding: 0.75rem 1.25rem;
            margin-left: 2.25rem;
            background-color: #fff;
            border: 1px solid rgba(0, 0, 0, 0.125);
        }
    </style>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/verwaltung">Verwaltung</a></li>
            <li class="breadcrumb-item active" aria-current="page">Webseiten-Chat</li>
        </ol>
    </nav>
    <div class="container-fluid subpage-main">
        <div class="row">
            <div class="col-2">
                <h3>Webseiten-Chat <a href="http://chat.academy-languages.de/" target="_blank"><i class="fas fa-external-link-alt"></i></a></h3>
                <ul class="list-group">
                    <li class="list-group-item"><a href="/verwaltung/chat"
                                                   class="text-reset">{{ __('Beispiel') }}</a></li>
                </ul>
            </div>
            <div class="col">
                <p class="alert alert-info">Klicken Sie unten rechts auf das Symbol um den Chat zu starten</p>
            </div>
        </div>


        <!-- Start of Rocket.Chat Livechat Script -->
        <script type="text/javascript">
            (function(w, d, s, u) {
                w.RocketChat = function(c) { w.RocketChat._.push(c) }; w.RocketChat._ = []; w.RocketChat.url = u;
                var h = d.getElementsByTagName(s)[0], j = d.createElement(s);
                j.async = true; j.src = 'https://chat.academy-languages.de/livechat/rocketchat-livechat.min.js?_=201903270000';
                h.parentNode.insertBefore(j, h);
            })(window, document, 'script', 'https://chat.academy-languages.de/livechat');
        </script>

@endsection
