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

        .social-comment {
            margin-top: 15px;
        }
        .social-footer .social-comment img {
            width: 32px;
            margin-right: 10px;
        }
        .media-body {
            overflow: hidden;
        }
        .social-footer {
            padding: 10px;
        }
    </style>

<div class="container gedf-wrapper text-center" style="padding-top: 60px;">
    <a href="#" class="col-6 col-sm-4 col-md-3 text-center text-decoration-none text-dark padding-apps">

        <img src="/images/social_launcher.png" class="rounded img-fluid appIcon">
        <div class="card-body" style="padding-left: 0px; padding-right: 0px;">
            <h4 class="card-title font-weight-light text-muted">Es ist so leer hier ... Erstelle eine Gruppe!</h4>
        </div>
    </a>
</div>
@endsection
