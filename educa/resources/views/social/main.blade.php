@extends('layouts.social')

@section('pageContent')
<style>
    #wrapper {
        overflow-x: hidden;
    }

    #sidebar-wrapper {
        min-height: calc(100vh - 57px);
        margin-left: -25rem;
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
        width: 25rem;
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
            margin-left: -25rem;
        }
    }

    a.bg-grey {
        background-color: #d4d3d3 !important;
    }

    .chatBar .list-group-item
    {
        display: flex;
    }
    .chatBar  .list-group-item.open-chat .users-list-body p {
        font-weight: 600;
        color: #646464;
    }

    .chatBar .list-group-item figure {
        margin-right: 1rem;
    }

    .chatBar  .list-group-item .users-list-body {
        -webkit-box-flex: 1;
        -webkit-flex: 1;
        -moz-box-flex: 1;
        -ms-flex: 1;
        flex: 1;
        position: relative;
        min-width: 0px;
        display: -webkit-box;
        display: -webkit-flex;
        display: -moz-box;
        display: -ms-flexbox;
        display: flex;
    }

    .chatBar  .list-group-item .users-list-body > div:first-child {
        min-width: 0;
        display: -webkit-box;
        display: -webkit-flex;
        display: -moz-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -webkit-flex-direction: column;
        -moz-box-orient: vertical;
        -moz-box-direction: normal;
        -ms-flex-direction: column;
        flex-direction: column;
        -webkit-box-flex: 1;
        -webkit-flex: 1;
        -moz-box-flex: 1;
        -ms-flex: 1;
        flex: 1;
    }

    .chatBar  .list-group-item .users-list-body h5 {
        white-space: nowrap;
        -o-text-overflow: ellipsis;
        text-overflow: ellipsis;
        overflow: hidden;
        font-size: 16px;
        font-weight: 500;
        margin-bottom: .2rem;
    }

    .chatBar   .list-group-item .users-list-body p {
        white-space: nowrap;
        -o-text-overflow: ellipsis;
        text-overflow: ellipsis;
        overflow: hidden;
        margin-bottom: 0;
        color: #969696;
    }

    .chatBar .list-group-item .users-list-body .users-list-action {
        padding-left: 15px;
    }

    .chatBar .list-group-item .users-list-body .users-list-action [data-toggle="dropdown"] i {
        font-size: 18px;
    }

    .chatBar .list-group-item .users-list-body .users-list-action .new-message-count {
        width: 23px;
        display: -webkit-box;
        display: -webkit-flex;
        display: -moz-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -webkit-align-items: center;
        -moz-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: center;
        -webkit-justify-content: center;
        -moz-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        line-height: 0;
        font-size: 13px;
        height: 23px;
        background-color: #db074d;
        color: white;
        -webkit-border-radius: 50%;
        -moz-border-radius: 50%;
        border-radius: 50%;
        margin-left: auto;
    }

    .chatBar  .list-group-item .users-list-body .users-list-action .action-toggle {
        opacity: 0;
        text-align: right;
    }

    .chatBar  figure.avatar {
        display: inline-block;
        margin-bottom: 0;
        height: 2.3rem;
        width: 2.3rem;
        -webkit-border-radius: 50%;
        -moz-border-radius: 50%;
        border-radius: 50%;
    }

    .chatBar  figure.avatar .avatar-title {
        color: rgba(255, 255, 255, 0.8);
        background: #d7d7d7;
        width: 100%;
        height: 100%;
        display: -webkit-box;
        display: -webkit-flex;
        display: -moz-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -webkit-align-items: center;
        -moz-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: center;
        -webkit-justify-content: center;
        -moz-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        text-transform: uppercase;
        font-size: 19px;
    }

    .chatBar figure.avatar > a {
        width: 100%;
        height: 100%;
        display: block;
        -webkit-transition: color .3s;
        -o-transition: color .3s;
        -moz-transition: color .3s;
        transition: color .3s;
        color: #0a80ff;
    }

    .chatBar  figure.avatar > a > img, figure.avatar > img {
        width: 100%;
        height: 100%;
        -o-object-fit: cover;
        object-fit: cover;
    }

    .chatBar figure.avatar.avatar-sm {
        height: 1.3rem;
        width: 1.3rem;
    }

    .chatBar figure.avatar.avatar-sm .avatar-title {
        font-size: 14px;
    }

    .chatBar figure.avatar.avatar-sm.avatar-state-primary:before, figure.avatar.avatar-sm.avatar-state-success:before, figure.avatar.avatar-sm.avatar-state-danger:before, figure.avatar.avatar-sm.avatar-state-warning:before, figure.avatar.avatar-sm.avatar-state-info:before, figure.avatar.avatar-sm.avatar-state-secondary:before, figure.avatar.avatar-sm.avatar-state-light:before, figure.avatar.avatar-sm.avatar-state-dark:before {
        width: .8rem;
        height: .8rem;
    }

    .chatBar  figure.avatar.avatar-lg {
        height: 3.8rem;
        width: 3.8rem;
    }

    .chatBar figure.avatar.avatar-lg .avatar-title {
        font-size: 29px;
    }

    .chatBar figure.avatar.avatar-lg.avatar-state-primary:before, figure.avatar.avatar-lg.avatar-state-success:before, figure.avatar.avatar-lg.avatar-state-danger:before, figure.avatar.avatar-lg.avatar-state-warning:before, figure.avatar.avatar-lg.avatar-state-info:before, figure.avatar.avatar-lg.avatar-state-secondary:before, figure.avatar.avatar-lg.avatar-state-light:before, figure.avatar.avatar-lg.avatar-state-dark:before {
        width: 1.2rem;
        height: 1.2rem;
        right: -1px;
    }

    .chatBar figure.avatar.avatar-xl {
        height: 6.1rem;
        width: 6.1rem;
    }

    .chatBar figure.avatar.avatar-xl .avatar-title {
        font-size: 39px;
    }

    .chatBar figure.avatar.avatar-xl.avatar-state-primary:before, figure.avatar.avatar-xl.avatar-state-success:before, figure.avatar.avatar-xl.avatar-state-danger:before, figure.avatar.avatar-xl.avatar-state-warning:before, figure.avatar.avatar-xl.avatar-state-info:before, figure.avatar.avatar-xl.avatar-state-secondary:before, figure.avatar.avatar-xl.avatar-state-light:before, figure.avatar.avatar-xl.avatar-state-dark:before {
        width: 1.2rem;
        height: 1.2rem;
        top: 2px;
        right: 7px;
    }

    .chatBar  figure.avatar.avatar-state-primary, figure.avatar.avatar-state-success, figure.avatar.avatar-state-danger, figure.avatar.avatar-state-warning, figure.avatar.avatar-state-info, figure.avatar.avatar-state-secondary, figure.avatar.avatar-state-light, figure.avatar.avatar-state-dark {
        position: relative;
    }

    .chatBar  figure.avatar.avatar-state-primary:before, figure.avatar.avatar-state-success:before, figure.avatar.avatar-state-danger:before, figure.avatar.avatar-state-warning:before, figure.avatar.avatar-state-info:before, figure.avatar.avatar-state-secondary:before, figure.avatar.avatar-state-light:before, figure.avatar.avatar-state-dark:before {
        content: "";
        position: absolute;
        display: block;
        width: .8rem;
        height: .8rem;
        -webkit-border-radius: 50%;
        -moz-border-radius: 50%;
        border-radius: 50%;
        top: -2px;
        right: -2px;
        border: 3px solid white;
    }

    .chatBar .avatar-group {
        display: -webkit-inline-box;
        display: -webkit-inline-flex;
        display: -moz-inline-box;
        display: -ms-inline-flexbox;
        display: inline-flex;
        margin-right: 2.2rem;
    }

    .chatBar .avatar-group figure.avatar {
        border: 2px solid white;
        margin-right: -1rem !important;
    }

    .chatBar .avatar-group figure.avatar:last-child {
        margin-right: 0;
    }

    .chatBar .avatar-group figure.avatar:hover {
        position: relative;
        z-index: 1;
    }
    .chatBar .list-group-item.open-chat {
        background-color: #f0f0f0;
    }

    #sidebar-wrapper .form-control {
        border-radius: 5px;
        height: auto;
        border: 1px solid #e6e6e6;
        padding: 10px 15px;
    }

    #sidebar-wrapper header {
        display: -webkit-box;
        display: -webkit-flex;
        display: -moz-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -webkit-align-items: center;
        -moz-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: justify;
        -webkit-justify-content: space-between;
        -moz-box-pack: justify;
        -ms-flex-pack: justify;
        justify-content: space-between;
        padding: 0 30px;
        height: 80px;
        font-weight: 600;
        -webkit-border-radius: 0;
        -moz-border-radius: 0;
        border-radius: 0;
    }
    #sidebar-wrapper {
        border-right: 1px solid #e6e6e6;
    }
</style>
<!--
<div class="d-flex" id="wrapper">
    Sidebar -->
<!-- <div id="sidebar-wrapper">

   <ul class="list-group list-group-flush">
         <li class=" d-flex" style="height: 55px;">
             <a style="padding-top: 15px;  color: white;"  class="list-group-item btn bg-primary btn-primary flex-fill" href="#" onclick="createGroupView();" >
                 <i class="fas fa-plus"></i> Gruppe erstellen
             </a>
             <a style="padding-top: 15px; border-left: 1px solid white;  color: white;" data-toggle="modal" class="list-group-item btn bg-primary btn-primary flex-fill" data-target="#createMessage">
                 <i class="fas fa-comment-dots"></i> Nachricht senden
             </a>
         </li>
     </ul>
     <form style="padding: 5px;margin-bottom: 0px;">
         <input type="text" class="form-control" placeholder="Nachrichten durchsuchen ... ">
     </form>
     <ul class="list-group mt-2 list-group-flush chatBar " id="listMenu">
         <li class="text-center">Lade Gruppen und Nachrichten ...</li>
     </ul>
 </div>
/#sidebar-wrapper -->
    <div id="page-content-wrapper">
        <div id="mainContent">
            @yield('contentViewFeed')

        </div>
        <div id="react-root"> </div>
        <div id="createGroup" style="display: none;">
            @include('social.modale.createGroup')
        </div>
    </div>

    @include('social.modale.createMessage')
</div>

@endsection


@section('additionalScript')
    <script>
        function createGroupView() {
            $('#mainContent').hide();
            $('#createGroup').show();
        }

        $('#searchUser').each(function(){
           window.searchUserTable = $(this).DataTable({
                dom: 'frtp',
                buttons: [      ],
                language : {
                    url: "/js/german.json",
                    searchPanes: {
                        clearMessage: 'Zurücksetzen',
                        collapse: {0: 'Suchoptionen', _: 'Suchoptionen (%d)'}
                    }
                },
                columnDefs: [ {
                    orderable: false,
                    className: 'select-checkbox',
                    targets:   0
                } ],
                select: {
                    style:    'multi',
                    selector: 'td:first-child'
                },
                order: [[ 2, 'asc' ]],
               rowId: 'ID'
            });
        });
    </script>
    <script>
        function updateMenu()
        {
            postDataHtml('/social/ajax/menu', { }, '{{ csrf_token() }}',(data) => {
               // console.log(data);
                var response = JSON.parse(data);
                $('#listMenu').html(response.html);
                var chatWindow = document.getElementById('chatWindow');
                if(chatWindow != null) {
                    console.log("update token " + response.token);
                    chatWindow.contentWindow.postMessage({
                        externalCommand: 'login-with-token',
                        token: response.token
                    }, '*');
                }
            });
        }
        updateMenu();
        setInterval(function(){
            //updateMenu();
        }, 5000);
    </script>
    <script>
        $("#teilnehmer").select2({
            minimumInputLength: 0,
            theme: 'bootstrap4',
            ajax: {
                url: "/api/search/clouduser",
                dataType: 'json',
                data: function (params) {
                    return {
                        q: params.term, // search term
                        rcNeed: "true"
                    };
                }
            },
        });
    </script>
    <script>
        function loadBBB()
        {
            $.toast({
                title: 'Information',
                subtitle: 'jetzt',
                content: 'Das Videokonferenz-Modul ist nicht aktiviert.',
                type: 'info',
                delay: 5000
            });
        }
    </script>
    @yield('additionalScript2')
@endsection

