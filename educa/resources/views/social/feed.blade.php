@extends('app')

@section('content')

    {!! $html !!}
@include('social.modale.createReiter')
@endsection

@section('additionalScript2')
    {!! $additionalReiterScript !!}
@endsection


@section('fasfd')
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
            {{ $group->name }}
        </a>
        <div class="collapse navbar-collapse"  id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                @foreach($group->reiters() as $reiter)
                    <li class="nav-item">
                        <a class="nav-link" href="/social/group/{{ $group->id }}/{{ $reiter->count }}/">{!! $reiter->icon !!} {{ $reiter->name }}</a>
                    </li>
                @endforeach
                <a href="#" class="btn btn btn-outline-light mt-auto mb-auto" data-toggle="modal" data-target="#createReiter"><i class="fas fa-plus"></i></a>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="loadBBB();" ><i class="fas fa-video"></i></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/social/group/{{ $group->id }}/settings"><i class="fas fa-ellipsis-v"></i></a>
                </li>
            </ul>
        </div>
    </nav>

    </div>
@endsection
