@extends('layouts.notLoggedIn')

@section('title', 'Login')

@section('additionalStyle')
    <style>
        html,
        body {
            height: 100%;
        }

        body {
            display: -ms-flexbox;
            display: flex;
            -ms-flex-align: center;
            align-items: center;
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #f2f3f5!important;
            margin: 0px;
        }

        .form-signin {
            width: 100%;
            max-width: 440px;
            padding: 15px;
            margin: auto;
        }
        .form-signin .checkbox {
            font-weight: 400;
        }
        .form-signin .form-control {
            position: relative;
            box-sizing: border-box;
            height: auto;
            padding: 10px;
            font-size: 16px;
        }
        .form-signin .form-control:focus {
            z-index: 2;
        }
        .form-signin input[type="email"] {
            margin-bottom: -1px;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
        }
        .form-signin input[type="password"] {
            margin-bottom: 10px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }

        .login {
            height: 500px;
        }
        .bg-image {
            background-image: url('/images/loading.gif');
            background-size: auto;
            background-position: center center;
        }
    </style>
@endsection
@section('content')

    <script>
        function insertToken(token, educa_rc_token, educa_rc_uid)
        {
            localStorage.setItem('jwt', token);
            localStorage.setItem('educa_rc_token', educa_rc_token);
            localStorage.setItem('educa_rc_uid', educa_rc_uid);
            window.location = "/";
        }
    </script>
    <div class="container card">
        <div class="">
        <div class="row no-gutter">
            <!-- The image half -->
            <div class="col-md-6 d-none d-md-flex bg-image"></div>


            <!-- The content half -->
            <div class="col-md-6">
                <div class="login d-flex align-items-center mx-auto">
    <div class="form-signin">
        @if($failed)
            <h1 class="h3 mb-3 font-weight-normal"><i class="fab fa-microsoft"></i> {{ __('educa konnte nicht mit Office 365 verbunden werden') }}</h1>
            <h1 class="h5 mb-3 font-weight-normal">Ihr Account {{ $msGraph["userPrincipalName"] }} ist nicht für die Anmeldung in educa berechtigt.</h1>
            @else
        <h1 class="h3 mb-3 font-weight-normal"><i class="fab fa-microsoft"></i> {{ __('educa verbunden mit Office 365') }}</h1>
        <h1 class="h5 mb-3 font-weight-normal">Du bist angemeldet als {{ $msGraph["userPrincipalName"] }}.</h1>
        <h1 class="h6 mb-3 font-weight-normal">Vielen Dank für die Verbindung deines Office 365 Accounts mit educa. Klick auf "Weiter", um educa zu öffnen</h1>

        <button class="btn btn-lg btn-primary btn-block mt-2" onclick='insertToken("{{ $jwt_token }}","{{ $educa_rc_token }}","{{ $educa_rc_uid }}");'>{{ __('Weiter') }}</button>

        @endif
        <div class="mt-5 mb-3 text-muted">educa © {{ date("Y") }} • made with <i class="fa fa-heart"></i> in Göttingen.</div>
    </div>
                </div>
    </div>
    </div>
        </div>
@endsection
