<style>
    .appIcon {
        border-radius: {{ $radius }} !important;
        height: {{ $iconSize }}px;
        width: {{ $iconSize }}px;
    }
    .notify-badge{
        position: absolute;
        right:60px;
        top:-12px;
        background:red;
        text-align: center;
        border-radius: 30px 30px 30px 30px;
        color:white;
        padding:5px 15px;
        font-size:20px;
    }
</style>
<div class="row mt-5">

    @if($home)
    <a href="/appswitcher" class="col-6 col-sm-4 col-md-3 text-center text-decoration-none text-dark padding-apps">
        <img src="/images/home.png" class="rounded img-fluid appIcon">
        <div class="card-body" style="padding-left: 0px; padding-right: 0px;">
            <h5 class="card-title">Home</h5>
        </div>
    </a>
    @endif
    @foreach($cloud_user->getApps() as $app)
    <a href="/appswitcher/switch/{{ $app["appName"] }}" class="col-6 col-sm-4 col-md-3 text-center text-decoration-none text-dark padding-apps">

            <!-- <div class="notify-badge">Neu</div> -->
        <img src="{{ $app["icon"] }}" class="rounded img-fluid appIcon">
        <div class="card-body" style="padding-left: 0px; padding-right: 0px;">
            <h5 class="card-title">{{ $app["name"] }}</h5>
        </div>
    </a>@endforeach

</div>
