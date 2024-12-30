@extends('app')

@section("content")
    <style>
        h3 {
            color: #00468e;
        }
        h4 {
            color: #00468e;
        }
        .color-2 {
            color: #fd7e14 !important;
        }

        .header-line {
            height: 2px;
            width: 100%;
            background: #e0e0e0;
            position: relative;
            margin: 0 auto;
            left: 0;
            right: 0;
        }

        .wizard-fuu .nav-tabs {
            position: relative;
            margin-bottom: 0;
            border-bottom-color: transparent;
        }

        .wizard-fuu > div.wizard-fuu-inner {
            position: relative;
            top: 25px;
            text-align: center;
        }

        .connecting-line {
            height: 20px;
            background: #e0e0e0;
            position: absolute;
            width: 85%;
            margin: 0 auto;
            left: 10px;
            right: 0;
            top: 5px;
            z-index: 1;
        }

        .wizard-fuu .nav-tabs > li.active > a, .wizard-fuu .nav-tabs > li.active > a:hover, .wizard-fuu .nav-tabs > li.active > a:focus {
            color: #555555;
            cursor: default;
            border: 0;
            border-bottom-color: transparent;
        }

        span.round-tab {
            width: 30px;
            height: 30px;
            line-height: 30px;
            display: inline-block;
            border-radius: 50%;
            background: #fff;
            z-index: 2;
            position: absolute;
            left: 0;
            text-align: center;
            font-size: 16px;
            color: #0e214b;
            font-weight: 500;
            border: 1px solid #ddd;
        }
        span.round-tab i{
            color:#555555;
        }
        .wizard-fuu li.active span.round-tab {
            background: #00468e;
            color: #fff;
            border-color: #00468e;
        }
        .wizard-fuu li.active span.round-tab i{
            color: #5bc0de;
        }
        .wizard-fuu .nav-tabs > li.active > a i{
            color: #fd7e14;
            font-size: 36px;
            left: 16px;
        }

        .wizard-fuu .nav-tabs > li {
            width: 33%;
        }

        .wizard-fuu li:after {
            content: " ";
            position: absolute;
            left: 46%;
            opacity: 0;
            margin: 0 auto;
            bottom: 0px;
            border: 5px solid transparent;
            border-bottom-color: red;
            transition: 0.1s ease-in-out;
        }



        .wizard-fuu .nav-tabs > li a {
            width: 30px;
            height: 30px;
            margin: 20px auto;
            border-radius: 100%;
            padding: 0;
            background-color: transparent;
            position: relative;
            top: 0;
        }
        .wizard-fuu .nav-tabs > li a i{
            position: absolute;
            top: -15px;
            font-style: normal;
            font-weight: 400;
            white-space: nowrap;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 12px;
            font-weight: 700;
            color: #000;
        }

        .wizard-fuu .nav-tabs > li a:hover {
            background: transparent;
        }

        .wizard-fuu .tab-pane {
            position: relative;
            padding-top: 20px;
        }


        .wizard-fuu h3 {
            margin-top: 0;
        }


        .optionBlock {
            min-height: 50px;
            padding: 10px;
            font-size: 0.9rem;
            background-color: #e0e0e0;
        }

        .optionBlock.primary {
            background-color: #00468e;
            color: #fff;
        }
        .optionBlock.secondary {
            background-color: #fd7e14;
            color: #fff;
        }
        .optionBlock.clearBackground {
            background-color: transparent;
            text-align: center;
            color: #00468e;
        }

        .selectAreaWizard {
            max-height: calc(100vh - 300px);
            overflow: auto;
        }

        .selectBoxBlock {

            min-height: 20px;
            background: white;
            padding: 10px;
            margin-right: 10px;

        }
        .checkedOption {
            border: 2px solid #fd7e14;
        }
        .checkmark {
            visibility: hidden;
        }
        .checkedOption .checkmark {
            visibility: visible;
        }
    </style>
    @yield('pageContent')
@endsection
