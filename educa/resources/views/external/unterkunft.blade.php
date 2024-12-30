@extends('external.main')

@section('pageContent')

    <div class="container-fluid">
        @include('external.head')
        <div class="row mt-5">
        @php
            $parent_id = 0;
            $url = "/external/preiskalkulator/?id=".$data->id;
            $selectobs = [];
        @endphp
        <!-- Step 1 -->
            @php
                $min = 3;
                $kategorie = \App\PreisKategorie::find(5);
                $selected_id = \Illuminate\Support\Facades\Request::input("kategorie_".$kategorie->id);
            @endphp
            <div
                class="selectAreaWizard @if($selected_id != null || $parent_id == -1) d-none d-md-block  @endif col-md-3">
                <div class="optionBlock @if($parent_id != -1) primary @endif">
                    {{ $kategorie->name_locale }} {{ __('preiskalkulator.select') }} <i class="fas fa-sort-down"></i>
                </div>
                @foreach($kategorie->auswahl($parent_id) as $auswahl)
                    <div class="optionBlock mt-2  @if($selected_id == $auswahl->id) @php
                        $selectobs[] = $auswahl; @endphp checkedOption @endif"
                         onclick="openUrl('{{ $url."&kategorie_".$kategorie->id."=".$auswahl->id }}')">
                        <div class="row justify-content-center">
                            <div class="col-auto mr-auto"
                                 style="width: calc(100% - 50px);">{{ $auswahl->name_locale }} </div>
                            <div class="col-auto ml-auto selectBoxBlock" style="width: 35px; height: 35px;">
                                <i class="fas fa-check color-2 checkmark"></i>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @php
                $parent_id = \Illuminate\Support\Facades\Request::input("kategorie_".$kategorie->id,-1);
                $url .= "&kategorie_".$kategorie->id."=".$parent_id;
            @endphp

        <!-- Step 1 -->
            @php
                $min = 3;
                $kategorie = \App\PreisKategorie::find(6);
                $selected_id = \Illuminate\Support\Facades\Request::input("kategorie_".$kategorie->id);
            @endphp
            <div
                class="selectAreaWizard @if($selected_id != null || $parent_id == -1) d-none d-md-block  @endif col-md-3">
                <div class="optionBlock @if($parent_id != -1) primary @endif">
                    {{ $kategorie->name_locale }} {{ __('preiskalkulator.select') }} <i class="fas fa-sort-down"></i>
                </div>
                @foreach($kategorie->auswahl($parent_id) as $auswahl)
                    <div class="optionBlock mt-2  @if($selected_id == $auswahl->id) @php
                        $selectobs[] = $auswahl; @endphp checkedOption @endif"
                         onclick="openUrl('{{ $url."&kategorie_".$kategorie->id."=".$auswahl->id }}')">
                        <div class="row justify-content-center">
                            <div class="col-auto mr-auto"
                                 style="width: calc(100% - 50px);">{{ $auswahl->name_locale }} </div>
                            <div class="col-auto ml-auto selectBoxBlock" style="width: 35px; height: 35px;">
                                <i class="fas fa-check color-2 checkmark"></i>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @php
                $parent_id = \Illuminate\Support\Facades\Request::input("kategorie_".$kategorie->id,-1);
                $url .= "&kategorie_".$kategorie->id."=".$parent_id;
            @endphp


        <!-- Step 1 -->
            @php
                $min = 3;
                $kategorie = \App\PreisKategorie::find(15);
                $selected_id = \Illuminate\Support\Facades\Request::input("kategorie_".$kategorie->id);
            @endphp
            <div
                class="selectAreaWizard @if($selected_id != null || $parent_id == -1) d-none d-md-block  @endif col-md-3">
                <div class="optionBlock @if($parent_id != -1) primary @endif">
                    {{ $kategorie->name_locale }} {{ __('preiskalkulator.select') }} <i class="fas fa-sort-down"></i>
                </div>
                @foreach($kategorie->auswahl($parent_id) as $auswahl)
                    <div class="optionBlock mt-2  @if($selected_id == $auswahl->id) @php
                        $selectobs[] = $auswahl; @endphp checkedOption @endif"
                         onclick="openUrl('{{ $url."&kategorie_".$kategorie->id."=".$auswahl->id }}')">
                        <div class="row justify-content-center">
                            <div class="col-auto mr-auto"
                                 style="width: calc(100% - 50px);">{{ $auswahl->name_locale }} </div>
                            <div class="col-auto ml-auto selectBoxBlock" style="width: 35px; height: 35px;">
                                <i class="fas fa-check color-2 checkmark"></i>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>


            @php
                $min = 3;
                $kategorie = \App\PreisKategorie::find(16);
                $selected_id = \Illuminate\Support\Facades\Request::input("kategorie_".$kategorie->id);
            @endphp
            <div
                class="selectAreaWizard @if($selected_id != null || $parent_id == -1) d-none d-md-block  @endif col-md-3"
                style="min-height: 600px">
                <div class="optionBlock @if($parent_id != -1) primary @endif">
                    {{ $kategorie->name_locale }} {{ __('preiskalkulator.select') }} <i class="fas fa-sort-down"></i>
                </div>
                @if(count($selectobs) > 2)

                    @php
                        $selectobs[3] = \App\PreisAuswahl::find(\Illuminate\Support\Facades\Session::get("preiskalkulator_2",""));

                    @endphp
                    <form method="GET">
                        <input type="hidden" name="id" value="2">
                        <input type="hidden" name="kategorie_5" value="{{ $selectobs[0]->id }}">
                        <input type="hidden" name="kategorie_6" value="{{ $selectobs[1]->id }}">
                        <input type="hidden" name="kategorie_15" value="{{ $selectobs[2]->id }}">
                        @csrf
                        <div class="optionBlock mt-2">
                            <div class="input-group date" id="datepicker14" data-target-input="nearest">
                                <input value="{{ \Illuminate\Support\Facades\Session::get("start","") != "" ? \Illuminate\Support\Facades\Session::get("start","") : (\Illuminate\Support\Facades\Session::get("startDate","") != "" ?
                    \Carbon\Carbon::parse(\Illuminate\Support\Facades\Session::get("startDate",""))->previous("Sunday")->format("d.m.Y") : \Carbon\Carbon::now()->next('Sunday')->format("d.m.Y") ) }}"
                                       id="datepicker14" required name="start" type="text"
                                       placeholder="DD.MM.YYYY"
                                       class="form-control datetimepicker-input" data-target="#datepicker14"/>
                                <div class="input-group-append" data-target="#datepicker14"
                                     data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        </div>

                        <div class="optionBlock mt-2">
                            <div class="input-group date" id="datepicker15" data-target-input="nearest">
                                <input value="{{ \Illuminate\Support\Facades\Session::get("end","") != "" ? \Illuminate\Support\Facades\Session::get("end","") : $courseEnde->format("d.m.Y") }}"
                                       id="datepicker15" required name="end" type="text"
                                       placeholder="DD.MM.YYYY"
                                       class="form-control datetimepicker-input" data-target="#datepicker15"/>
                                <div class="input-group-append" data-target="#datepicker15"
                                     data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="optionBlock secondary mt-2" style="border: none;"
                                href="/external/preiskalkulator?id=3"> {{ __('preiskalkulator.calculate') }}</button>
                    </form>
                @endif
            </div>


        </div>

    </div>
    <!-- Footer -->
    <div class="modal" id="resultModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>{{ __('preiskalkulator.myAccommodation') }}</h4>
                </div>
                <div class="modal-body">
                    <div class="optionBlock">
                        <div>
                            <div class="row">
                                @php
                                    $kategorie = \App\PreisKategorie::find(5);

                                @endphp
                                <label class="col-6"><b>{{ $kategorie->name_locale }}:</b></label>
                                <label
                                    class="col-6">@if(count($selectobs) > 0) {{ $selectobs[0]->name_locale }} @endif</label>
                            </div>
                            <div class="row">
                                @php
                                    $kategorie = \App\PreisKategorie::find(6);

                                @endphp
                                <label class="col-6"><b>{{ $kategorie->name_locale }}:</b></label>
                                <label
                                    class="col-6">@if(count($selectobs) > 1) {{ $selectobs[1]->name_locale }} @endif</label>
                            </div>
                            <div class="row">
                                @php
                                    $kategorie = \App\PreisKategorie::find(15);

                                @endphp
                                <label class="col-6"><b>{{ $kategorie->name_locale }}:</b></label>
                                <label
                                    class="col-6">@if(count($selectobs) > 2) {{ $selectobs[2]->name_locale }} @endif</label>
                            </div>
                            <div class="row">
                                @php
                                    $kategorie = \App\PreisKategorie::find(16);
                                @endphp
                                <label class="col-6"><b>{{ $kategorie->name_locale }}:</b></label>
                                <label
                                    class="col-6">{{ \Illuminate\Support\Facades\Session::get('start') }}-{{ \Illuminate\Support\Facades\Session::get("end") }} <br> @if(count($selectobs) > 3 && $selectobs[3] != null) {{ $selectobs[3]->name_locale }} @endif Woche(n)</label>
                            </div>
                        </div>
                    </div>
                    <div class="optionBlock mt-2">
                        @if(count($selectobs) > 2 && $selectobs[3] != null)
                            <div class="row">
                            <label class="col-6">{{ __('preiskalkulator.price') }}:</label>
                                <label class="col-6">{{ $selectobs[3]->preis }}€ </label>
                            </div>
                            <div class="row">
                                <label class="col-6">{{ __('preiskalkulator.administrationFee') }}:</label>
                                <label class="col-6">{{ $selectobs[0]->preis_1 }}€ </label>
                            </div>
                            <div class="row">
                                <label class="col-6">{{ __('preiskalkulator.deposit') }}:</label>
                                <label class="col-6">{{ $selectobs[0]->preis_2 }}€ </label>
                            </div>
                            <div class="row" style="border-top: 1px solid #000;">
                                <label class="col-6">{{ __('preiskalkulator.total') }}:</label>
                                <label class="col-6">{{ $selectobs[0]->preis_2 +  $selectobs[0]->preis_1  + $selectobs[3]->preis }}€ </label>
                            </div>
                        @else
                            <label><i>Keine Preisinformation für diese Dauer für diese Unterkunft gefunden. </i></label>
                            <button type="button" class="optionBlock primary" data-dismiss="modal" aria-label="Close">
                                <div class="float-left m-1">
                                    {{ __('preiskalkulator.differentDate') }}
                                </div>
                            </button>
                            <div class="clearfix"></div>
                        @endif

                            @if((count($selectobs) > 2 && $selectobs[3] != null) || count($selectobs) == 0)

                                <div class="col-12">
                                    <label
                                        for="exampleInputEmail1">{{ __("preiskalkulator.questionLikeToBookAccomodation") }}</label>
                                    <div class="float-right">
                                        <a href="/external/preiskalkulator?id=5">
                                            <div class="optionBlock primary m-1" style="display: inline-block">
                                                {{ __('preiskalkulator.yes') }} <i
                                                    class="fas fa-arrow-right"></i>
                                            </div>
                                        </a>
                                        <a href="/external/preiskalkulator?id=2&clearRedirect">
                                            <div class="optionBlock secondary m-1" style="display: inline-block">
                                                {{ __('preiskalkulator.noAccomodation') }} <i
                                                    class="fas fa-arrow-right"></i>
                                            </div>
                                        </a></div>
                                </div>
                                <div class="clearfix"></div>
                            @endif
                    </div>
                </div>

                <div class="modal-footer">
                    @if((count($selectobs) > 2 && $selectobs[3] != null) || count($selectobs) == 0)
                        <a href="#" data-dismiss="modal" style="color: #000 !important;">
                            <div class="float-left m-1">
                                {{ __('preiskalkulator.return') }} <i
                                    class="fas fa-redo"></i>
                            </div>
                        </a>
                    @endif
                </div>

            </div>
        </div>
    </div>


    <div class="modal" id="showNote">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>{{ __('preiskalkulator.myAccommodation') }}</h4>
                </div>
                <div class="modal-body">
                    <p>{{ __('preiskalkulator.wouldYouLikeToAddAccommodatioh') }}</p>
                </div>
                <div class="modal-footer">
                        <a href="#" data-dismiss="modal" >
                            <div class="optionBlock secondary float-right m-1">
                                {{ __('preiskalkulator.yes') }} <i
                                    class="fas fa-arrow-right"></i>
                            </div>
                        </a>
                        <a class="optionBlock  float-right m-1" style="color: #000 !important;"
                           href="/external/preiskalkulator?id=5"> {{ __('preiskalkulator.no') }} <i
                                class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openUrl(url) {
            window.location.href = url;
        }
    </script>

    <style>
        .bootstrap-datetimepicker-widget table td.disabled, .bootstrap-datetimepicker-widget table td.disabled:hover {
            color: #e9e9e9 !important;
        }
        @if(\Illuminate\Support\Facades\Session::get("startDate","") != "")
        @php
            $courseStart = \Carbon\Carbon::parse(\Illuminate\Support\Facades\Session::get("startDate",""));
        @endphp
        @while($courseStart->isBefore($courseEnde))
        .bootstrap-datetimepicker-widget table td.disabled[data-day="{{ $courseStart->format("d.m.Y") }}"] {
            background-color: #faa763 !important;
            color: #fff !important;
        }
        @php
            $courseStart->addDay();
            @endphp
        @endwhile
        @endif
    </style>

@endsection
@section('additionalScript')
    <script>
        @if( count($selectobs) == 0)
            $('#showNote').modal({
            backdrop: 'static',
            keyboard: false
        });
        @endif

        @if((count($selectobs) > 2 && ($selectobs[3] != null || \Illuminate\Support\Facades\Request::has("start") && \Illuminate\Support\Facades\Request::has("end"))))
            $('#resultModal').modal({
            backdrop: 'static',
            keyboard: false
        });
        @endif
    </script>
@endsection
