@section('pageContent')
    <div class="container gedf-wrapper">
        <div class="row">
            <div class="col-12 gedf-main" id="accordion">
                <h2>{{ $group->name }}</h2>

                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">Allgemein</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="members-tab" data-toggle="tab" href="#members" role="tab" aria-controls="members" aria-selected="false">Mitglieder</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tabs-tab" data-toggle="tab" href="#tabs" role="tab" aria-controls="tabs" aria-selected="false">Registerkarten</a>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                        @if($isAdmin)
                            <form action="/social/group/{{ $group->id }}/settings/changeGeneral" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label>Gruppenname</label>
                                    <input name="groupname" type="text" id="groupname" class="form-control input-lg" value="{{ $group->name }}">
                                </div>
                                <div class="form-group">
                                    <label>Gruppenbild</label>
                                    <div class="d-flex">
                                        @if(\Illuminate\Support\Facades\Storage::disk("public")->exists("group_".$group->id.".png"))
                                            <div class="mr-2">
                                                <img class="rounded-circle" width="45" src="/storage/group_{{ $group->id }}.png" alt="">
                                            </div>
                                        @endif
                                        <div class="ml-2">
                                            <input name="groupimage" type="file" id="groupimage" class="form-control-file input-lg">
                                        </div>
                                    </div>

                                </div>
                                <div class="form-group">
                                    <label>Farbe</label>
                                    <div class="input-group  colorpicker-bl" title="Using input value">
                                        <input type="text" name="color" class="form-control input-lg" value="{{ $group->color }}"/>
                                        <span class="input-group-append">
                                            <span class="input-group-text colorpicker-input-addon"><i></i></span>
                                        </span>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success float-right">Speichern</button>
                            </form>
                        @else
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Gruppenname</label>
                                <div class="col-sm-10">
                                    <input type="text" readonly value="{{ $group->name }}" class="form-control-plaintext input-lg">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Farbe</label>
                                <div class="col-sm-10">
                                    <button class="btn input-lg" style="background: {{ $group->color }}" disabled>　</button>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="tab-pane fade" id="members" role="tabpanel" aria-labelledby="members-tab">
                        <h5 class="mb-0">

                        </h5>
                        <table class="data-table table table-striped table-bordered" width="100%">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Rolle</th>
                                @if($isAdmin)
                                    <th>Aktion</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($group->members(false, true) as $member)
                                <tr>
                                    <td>{{ $member->displayName }}</td>
                                    <td>
                                        @if($isAdmin)
                                            <select class="form-control roleselect" name="role_{{ $member->id }}">
                                                @if($roleFound = false) @endif
                                                @foreach($roles as $role)
                                                    @if($group->getRole($member->id) >= $role->limit && !$roleFound)
                                                        @if($roleFound = true) @endif
                                                        <option value="{{$role->limit}}" selected>{{$role->name}}</option>
                                                    @else
                                                        <option value="{{$role->limit}}">{{$role->name}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        @else
                                            @foreach($roles as $role)
                                                @if($group->getRole($member->id) >= $role->limit)
                                                    {{$role->name}}
                                                    @break
                                                @endif
                                            @endforeach
                                        @endif
                                    </td>
                                    @if($isAdmin)
                                        <td>
                                            <a href="/social/group/{{ $group->id }}/settings/removeMember/{{ $member->id }}" class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></a>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        @if($isAdmin)
                            <a href="#" data-toggle="modal" data-target="#addMembersModal" class="btn btn-xs btn-primary"><i class="fas fa-plus"></i> Mitglieder hinzufügen</a>
                        @endif
                    </div>

                    <div class="tab-pane fade" id="tabs" role="tabpanel" aria-labelledby="tabs-tab">
                        @if($isAdmin)
                        <form action="/social/group/{{ $group->id }}/settings/setTabNames" method="POST">
                        @csrf
                        @endif

                        <table class="data-table table table-striped table-bordered" width="100%">
                            <thead><tr>
                                <td>Name</td>
                                <td>Typ</td>
                                @if($isAdmin)
                                    <td>Aktion</td>
                                @endif
                            </tr></thead>
                            <tbody>
                            @foreach($group->reiters() as $reiter)
                                <tr>
                                    <td>
                                        @if($isAdmin)
                                            <input class="form-control" name="tabname_{{ $reiter->count }}" value="{{ $reiter->name }}">
                                        @else
                                            {{ $reiter->name }}
                                        @endif
                                    </td>
                                    <td>{{ $reiter->typeName }}</td>
                                    @if($isAdmin && $reiter->can_delete)
                                        <td>
                                            <a href="/social/group/{{ $group->id }}/settings/removeReiter/{{ $reiter->count }}" class="btn btn-xs btn-danger dangeralert"><i class="fas fa-trash"></i></a>
                                        </td>
                                    @else
                                    <td></td>
                                    @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @if($isAdmin)
                            <button type="submit" id="savenames" class="btn btn-success">Reiternamen speichern</button>
                        </form>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
    @include('social.modale.addMembers')
@endsection
@section('additionalScript')
    <script>

        @if($isAdmin)
            $(".roleselect").change(function(){
                var memberid = $(this).attr("name").split("_")[1];
                var role = $(this).val();
                postData('/social/group/{{ $group->id }}/settings/updateRole', {memberid: memberid, role: role}, '{{ csrf_token() }}',(data) => {
                    console.log(data);
                    if(data.status)
                    {
                        $.toast({
                            title: 'Rolle geändert!',
                            subtitle: 'jetzt',
                            content: data.message,
                            type: 'success',
                            delay: 5000
                        });
                    }
                    else
                    {
                        $.toast({
                            title: 'Fehler!',
                            subtitle: 'jetzt',
                            content: data.message,
                            type: 'failure',
                            delay: 5000
                        });
                    }
                });
            });
            $('#groupname').on('keyup paste', function(e){
                if (e.keyCode == 13) {
                    saveGroupName();
                }
                else
                {
                    $("#savegroupname").css('visibility','visible');
                }
            });

            function saveGroupName()
            {
                var groupname = $("#groupname").val();
                postData('/social/group/{{ $group->id }}/settings/changeName', {name: groupname}, '{{ csrf_token() }}',(data) => {
                    if(data.status)
                    {
                        $.toast({
                            title: 'Gruppenname geändert!',
                            subtitle: 'jetzt',
                            content: data.message,
                            type: 'success',
                            delay: 5000
                        });
                        $(".navbar-brand").html(groupname);
                        $("#savegroupname").css('visibility','hidden');
                    }
                    else
                    {
                        $.toast({
                            title: 'Fehler!',
                            subtitle: 'jetzt',
                            content: data.message,
                            type: 'failure',
                            delay: 5000
                        });
                    }
                });
            }
        @endif

        $(".dangeralert").click(function(e){
            e.preventDefault();
            if(confirm("Diese Aktion kann nicht rückgängig gemacht werden. Fortfahren?"))
            {
                window.location = $(this).attr("href");
            }

        });
        $('#searchUserAdd').each(function(){
            window.searchUserAddTable = $(this).DataTable({
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
@endsection
