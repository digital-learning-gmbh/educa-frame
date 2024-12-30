@extends('external.main')

@section('pageContent')

    <div class="container-fluid">
        @include('external.head')
        <div class="row mt-5">
            @php
                $parent_id = 0;
                $url = "/external/preiskalkulator/?id=6";
                $selectobs = [];
            @endphp
            @foreach($data->kategorien as $kategorie)
                @php
                    $selected_id = \Illuminate\Support\Facades\Request::input("kategorie_".$kategorie->id);
                @endphp
            <div class="selectAreaWizard col-md-4 @if($selected_id != null || $parent_id == -1) d-none d-md-block  @endif">
                <div class="optionBlock @if($parent_id != -1) primary @endif">
                    {{ $kategorie->name }} <i class="fas fa-sort-down"></i>
                </div>
                @foreach($kategorie->auswahl($parent_id) as $auswahl)
                    @if($kategorie->id != 14 || \Carbon\Carbon::parse($auswahl->preis_datum2)->isAfter(\Carbon\Carbon::now()))
                    <div class="optionBlock mt-2  @if($selected_id == $auswahl->id) @php
                        $selectobs[] = $auswahl; @endphp checkedOption @endif" onclick="openUrl('{{ $url."&kategorie_".$kategorie->id."=".$auswahl->id }}')">
                        <div class="row justify-content-center">
                            <div class="col-auto mr-auto">{{ $auswahl->name }} </div>
                            <div class="col-auto selectBoxBlock">
                                <i class="fas fa-check color-2 checkmark"></i>
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
                @php
                    $parent_id = \Illuminate\Support\Facades\Request::input("kategorie_".$kategorie->id,-1);
                    $url .= "&kategorie_".$kategorie->id."=".$parent_id;
                @endphp
            @endforeach
            @if(count($selectobs) > 2)
                <div class="col-12 d-block d-md-none">
                    <a class="optionBlock primary float-right" href="/external/preiskalkulator?id=6"> {{ __('preiskalkulator.restart') }} <i class="fas fa-redo"></i></a>

                </div> @endif
            <div class="header-line mt-3"></div>
        </div>
        <!-- Footer -->
        <div class="row mt-3">

            <div class="col-5">
            </div>
            <div class="col-7">
                <h4>{{ __('preiskalkulator.myExam') }}</h4>
                <div class="optionBlock">
                    <div class="row justify-content-between">
                        <label class="col">@if(count($selectobs) > 0) {{ $selectobs[0]->name }} @endif</label>
                        <label class="col">@if(count($selectobs) > 1) {{ $selectobs[1]->name }} @endif</label>
                        <label class="col">@if(count($selectobs) > 2) {{ $selectobs[2]->name }} @endif</label>
                    </div>
                </div>
                <div class="optionBlock mt-2">
                    <label>@if(count($selectobs) > 2){{ __('preiskalkulator.price') }}: {{ $selectobs[2]->time_preis }}€ @endif</label>
                    @if(count($selectobs) > 2 || count($selectobs) == 0)
                    <a class="optionBlock secondary float-right" href="/external/preiskalkulator?id=2"> {{ __('preiskalkulator.next') }} <i class="fas fa-arrow-right"></i></a>
                    @endif
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function openUrl(url) {
            window.location.href = url;
        }
    </script>
@endsection
