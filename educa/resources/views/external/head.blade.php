<div class="row">

    <div class="col-12 col-md-6 mb-2">
        <h3><b>{{ $title }}</b></h3>

        <a href="/locale/?locale=en"><img src="/images/flags/united-kingdom.svg" width="50" height="50"></a>
        <a href="/locale/?locale=de"><img src="/images/flags/germany.svg" width="50" height="50"></a>
    </div>

    @if(!isset($hideHeader))
    <div class="col-12 col-md-6" style="min-height: 70px;">
        <div class="wizard-fuu">
            <div class="wizard-fuu-inner">
                <div class="connecting-line"></div>
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="@if($step == 1) active @else disabled @endif">
                        <a href="/external/preiskalkulator/?id=1"><span class="round-tab">1 </span> @if($step == 1)<i class="fas fa-check color-2"></i> @endif</a>
                    </li>
                    <!-- <li role="presentation" class="@if($step == 6) active @else disabled @endif">
                        <a href="/external/preiskalkulator/?id=6"><span class="round-tab">2 </span> @if($step == 6)<i class="fas fa-check color-2"></i> @endif</a>
                    </li> -->
                    <li role="presentation" class="@if($step == 2) active @else disabled @endif">
                        <a href="#"><span class="round-tab">2</span> @if($step == 2)<i class="fas fa-check color-2"></i> @endif</a>
                    </li>
                <!--  <li role="presentation" class="@if($step == 3) active @else disabled @endif">
                        <a href="/external/preiskalkulator/?id=3"><span class="round-tab">4</span> @if($step == 3)<i class="fas fa-check color-2"></i> @endif</a>
                    </li> -->
                <!--    <li role="presentation" class="@if($step == 4) active @else disabled @endif">
                        <a href="/external/preiskalkulator/?id=4"><span class="round-tab">5</span> @if($step == 4)<i class="fas fa-check color-2"></i> @endif</a>
                    </li> -->
                    <li role="presentation" class="@if($step == 5) active @else disabled @endif">
                        <a href="#"><span class="round-tab">3</span> @if($step == 5)<i class="fas fa-check color-2"></i> @endif</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    @endif
    <div class="header-line"></div>
</div>
