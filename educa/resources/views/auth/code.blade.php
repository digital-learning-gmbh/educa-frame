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
            background-color: #f5f5f5;
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
            background-image: url('/images/splashscreen.jpg');
            background-size: cover;
            background-position: center center;
        }

        .digitInput {
            border: none;
            width: 9ch;
            background:
                repeating-linear-gradient(90deg,
                dimgrey 0,
                dimgrey 1ch,
                transparent 0,
                transparent 1.5ch)
                0 100%/100% 2px no-repeat;
            color: dimgrey;
            font: 5ch consolas, monospace;
            letter-spacing: .5ch;
        }
        .digitInput:focus {
            outline: none;
            color: dodgerblue;
        }
    </style>
@endsection
@section('content')

    <div class="modal fade" id="impressum" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Impressum</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @include('impressum')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container card">
        <div class="">
        <div class="row no-gutter">
            <!-- The image half -->

            <div class="col-md-6 d-none d-md-flex bg-image"></div>


            <!-- The content half -->
            <div class="col-md-6 bg-light">
                <div class="login d-flex align-items-center mx-auto">
    <form class="form-signin" method="POST">
        @csrf
        <h1 class="h3 mb-3 font-weight-normal">{{ __('Zugangscode') }}</h1>
        <h1 class="h5 mb-3 font-weight-normal">{{ __('Bitte gebe deinen Zugangscode ein, um dich auf educa zu registrieren.') }}</h1>

        @if($systemMessage != "")
        <div class="alert alert-warning" role="alert">
           {{ $systemMessage }}
        </div>
        @endif

        <label for="inputEmail" class="sr-only">{{ __('E-Mail Address') }}</label>
        <div style="width: 100%; text-align: center;" class="mb-2">
        <input maxlength='6' name="digits" type="text" id="inputEmail" class="digitInput"  required="" autofocus="">
        </div>
        @if ($errors->has('email'))
            <span class="invalid-feedback" role="alert">
   <strong>{{ $errors->first('email') }}</strong>
   </span>
        @endif

        <a href="/login" id="pwReset">Ich habe bereits einen Account</a>

        <button class="btn btn-lg btn-primary btn-block mt-2" type="submit">{{ __('Code überprüfen') }}</button>
        <p class="mt-5 mb-3 text-muted">educa © {{ date("Y") }} • <a href="#" id="actionBugReport">Support</a> • <a href="#" data-toggle="modal" data-target="#impressum">Impressum</a></p>
    </form>
                </div>
    </div>
    </div>
        </div>
@endsection