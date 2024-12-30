@extends('layouts.analytics')

@section('pageContent')
    <style>
        #wrapper {
            overflow-x: hidden;
        }

        #sidebar-wrapper {
            min-height: calc(100vh - 57px);
            margin-left: -15rem;
            -webkit-transition: margin .25s ease-out;
            -moz-transition: margin .25s ease-out;
            -o-transition: margin .25s ease-out;
            transition: margin .25s ease-out;
        }

        #sidebar-wrapper .sidebar-heading {
            padding: 0.875rem 1.25rem;
            font-size: 1.2rem;
        }

        #sidebar-wrapper .list-group {
            width: 15rem;
        }

        #page-content-wrapper {
            min-width: 100vw;
        }

        #wrapper.toggled #sidebar-wrapper {
            margin-left: 0;
        }

        @media (min-width: 768px) {
            #sidebar-wrapper {
                margin-left: 0;
            }

            #page-content-wrapper {
                min-width: 0;
                width: 100%;
            }

            #wrapper.toggled #sidebar-wrapper {
                margin-left: -15rem;
            }
        }
    </style>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="bg-light border-right" id="sidebar-wrapper">
         <!--   <li class="list-group-item list-group-item-action bg-light" style="padding: 0px;">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Suche..." aria-label="Recipient's username" aria-describedby="button-addon2">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="button-addon2"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </li> -->
            <div class="list-group list-group-flush">
                @foreach($reports as $report)
                    @if($cloud_user->canViewReport($report))
                <a href="/analytics/report/{{ $report->id }}" class="list-group-item list-group-item-action bg-light">{{ $report->name }}</a>
                    @endif
                @endforeach
            </div>
        </div>
        <!-- /#sidebar-wrapper -->
        <div id="page-content-wrapper" class="mt-5">
            <div class="container-fluid">
                    @yield('cloudContent')
            </div>
        </div>
    </div>
@endsection
