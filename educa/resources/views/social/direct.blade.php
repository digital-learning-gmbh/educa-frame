@extends('social.main')

@section('contentViewFeed')
    <style>
        .h7 {
            font-size: 0.8rem;
        }

        .gedf-wrapper {
            margin-top: 0.97rem;
        }

        @media (min-width: 992px) {
            .gedf-main {
                padding-left: 4rem;
                padding-right: 4rem;
            }
            .gedf-card {
                margin-bottom: 2.77rem;
            }
        }

        /**Reset Bootstrap*/
        .dropdown-toggle::after {
            content: none;
            display: none;
        }

        .group-comment {
            margin-top: 15px;
        }
        .group-footer .group-comment img {
            width: 32px;
            margin-right: 10px;
        }
        .media-body {
            overflow: hidden;
        }
        .group-footer {
            padding: 10px;
        }
    </style>
    <nav class="navbar navbar-dark bg-dark navbar-expand-lg">

        <a class="navbar-brand" href="#">
            <i class="fas fa-comment-dots"></i> {{ \App\Http\Controllers\Social\SocialController::getDisplayForDirectMessage($selected) }}
        </a>
        <div class="collapse navbar-collapse"  id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="loadBBB();"  id="actionBugReport"><i class="fas fa-phone"></i></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="loadBBB();" id="actionBugReport"><i class="fas fa-video"></i></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="openSettings();"><i class="fas fa-ellipsis-v"></i></a>
                </li>
            </ul>
        </div>
    </nav>


@endsection
@section('additionalScript2')
    <script>

        window.onload = function () {
            document.getElementById('chatWindow').contentWindow.postMessage({
                externalCommand: 'login-with-token',
                token: '{{ $token }}'
            }, '*');
        };
        window.addEventListener('message', function(e) {
            console.log(e.data.eventName); // event name
            console.log(e.data.data); // event data
            if(e.data.eventName == "status-changed" && e.data.data == "online")
            {
                document.getElementById('chatWindow').contentWindow.postMessage({
                    externalCommand: 'go',
                    path: 'direct/{{ $id }}'
                }, '*');
            }
        });

        //
        function openSettings()
        {

        }
    </script>
@endsection
