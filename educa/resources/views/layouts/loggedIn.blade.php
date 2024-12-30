@extends('app')

@section("content")
    <style>
        body {
         /*   padding-top: 120px;*/
            background-color: #f2f3f5!important;
        }
        .navbar-dark .navbar-nav .nav-link {
            color: #fff;
        }
    </style>
    <div id="react-administration-main-hook"></div>


@yield('appContent')
@endsection


@section('title')
educa - Campus Management System
@endsection
