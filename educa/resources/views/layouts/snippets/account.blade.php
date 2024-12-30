
<a class="navbar-brand" href="#" style="margin-right: 0px;">
    <img src="/api/image/cloud/?cloud_id={{ $cloud_user->id }}&name={{ $cloud_user->image }}" width="30" height="30" class="d-inline-block align-top rounded-circle" alt="">
</a>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle dropright" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        {{ $cloud_user->name }}
    </a>
    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
        <h6 class="dropdown-header">{{ $cloud_user->email }}</h6>
        <a class="dropdown-item" href="/settings"><i class="fas fa-tools"></i> Einstellungen</a>
        <a class="dropdown-item" href="/logout"><i class="fas fa-sign-out-alt"></i> Abmelden</a>
        @if(\Illuminate\Support\Facades\Session::has('cloud_user_old'))
        <a class="dropdown-item" href="/cloud/logoutSecond"><i class="fas fa-user-times"></i> Benutzeransicht beenden</a>
        @endif
    </div>
</li>
