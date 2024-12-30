@extends('layouts.notLoggedIn')

@section('title', 'Meeting beitreten')

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
            background-image: url('/images/loading.png');
            background-size: cover;
            background-position: center center;
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
            <div class="col-md-6">
                <div class="login d-flex align-items-center mx-auto">
    <form class="form-signin" method="POST">
        @csrf
        <h1 class="h3 mb-3 font-weight-normal">{{ $meeting->name }}</h1>
        <h1 class="h5 mb-3 font-weight-normal">{{ __('Bitte vergib einen Anzeigenamen, um dem Meeting beizutreten.') }}</h1>

        @if($systemMessage != "")
        <div class="alert alert-warning" role="alert">
           {{ $systemMessage }}
        </div>
        @endif

        <input type="hidden" name="pin" value="{{ \Illuminate\Support\Facades\Request::input("pin") }}">
        <input type="hidden" name="model_id" value="{{ \Illuminate\Support\Facades\Request::input("model_id") }}">
        <input type="hidden" name="model_type" value="{{ \Illuminate\Support\Facades\Request::input("model_type") }}">
        <label for="inputEmail" class="sr-only">{{ __('Anzeigename') }}</label>
        <input name="name" type="text" id="inputEmail" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="Anzeigename" value="{{ old('email') }}" required="" autofocus="">

        <button class="btn btn-lg btn-primary btn-block mt-2" type="submit">{{ __('Meeting beitreten') }}</button>
      </form>
                </div>
    </div>
    </div>
        </div>
@endsection
