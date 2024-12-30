@extends('layouts.cloud')

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
            <div class="list-group list-group-flush">
                <a href="/cloud/general" class="list-group-item list-group-item-action bg-light">Allgemein</a>
                <a href="/cloud/user" class="list-group-item list-group-item-action bg-light">Benutzer</a>

                @if($cloud_user->hasPermissionTo(\App\PermissionConstants::IS_MULTI_TENANT_USER))
                <a href="/cloud/rights" class="list-group-item list-group-item-action bg-light">Rechte und Rollen</a>
                <a href="/cloud/analytics" class="list-group-item list-group-item-action bg-light">Analytics</a>
                <a href="/cloud/groups" class="list-group-item list-group-item-action bg-light">Gruppen</a>
                <a href="/cloud/tenants" class="list-group-item list-group-item-action bg-light">Tenants</a>
                @endif
            </div>
        </div>
        <!-- /#sidebar-wrapper -->
        <div id="page-content-wrapper" class="mt-5">
            <div class="container-fluid">
               @yield('cloudContent')
            </div>
        </div>
    </div>
    <script>
        function changeUser(id)
        {

            const  getCookie = (name) =>{
                let nameEQ = name + "=";
                let ca = document.cookie.split(';');
                for (let i = 0; i < ca.length; i++) {
                    let c = ca[i];
                    while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
                }
                return null;
            }

            localStorage.setItem('educa_rc_token_user_alias', getCookie("educa_rc_token"));
            localStorage.setItem('educa_rc_uid_user_alias',  getCookie("educa_rc_uid"));

            fetch('/api/v1/administration/masterdata/users/' + id + '/jwt?token=' + localStorage.getItem('jwt'))
                .then(response => response.json())
                .then(data => {
                    localStorage.setItem('jwt_user_alias', data["payload"]["jwt"]);
                    window.location.href = "/cloud/user/" +  id + "/switch";
                });
        }
    </script>
@endsection
