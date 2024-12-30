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
                $kategorie = \App\PreisKategorie::find(1);
                $selected_id = \Illuminate\Support\Facades\Request::input("kategorie_".$kategorie->id);
            @endphp
            <div
                class="selectAreaWizard @if($selected_id != null || $parent_id == -1) d-none d-md-block  @endif col-md-2">
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

        <!-- Step 2 -->
            @php
                $kategorie = \App\PreisKategorie::find(2);
                $selected_id = \Illuminate\Support\Facades\Request::input("kategorie_".$kategorie->id);
            @endphp
            <div
                class="selectAreaWizard @if($selected_id != null || $parent_id == -1) d-none d-md-block  @endif col-md-2">
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

        <!-- Step 3 -->
            @php
                $kategorie = \App\PreisKategorie::find(3);
                $selected_id = \Illuminate\Support\Facades\Request::input("kategorie_".$kategorie->id);
            @endphp
            <div
                class="selectAreaWizard @if($selected_id != null || $parent_id == -1) d-none d-md-block  @endif col-md-2">
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

        <!-- Step 4 -->
            @php
                $kategorie = \App\PreisKategorie::find(4);
                $selected_id = \Illuminate\Support\Facades\Request::input("kategorie_".$kategorie->id);
            @endphp
            <div
                class="selectAreaWizard @if($selected_id != null || $parent_id == -1) d-none d-md-block  @endif col-md-2">
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

        <!-- Step 5 -->
            @if(count($selectobs) > 1 && $selectobs[1] != null && strpos($selectobs[1]->name,"Individual") !== false)
                @php
                    $min = 4;
                    $kategorie = \App\PreisKategorie::find(1000);
                    $selected_id = \Illuminate\Support\Facades\Request::input("kategorie_".$kategorie->id);
                @endphp
                <div
                    class="selectAreaWizard @if($selected_id != null || $parent_id == -1) d-none d-md-block  @endif col-md-2">
                    <div class="optionBlock @if($parent_id != -1) primary @endif">
                        {{ $kategorie->name_locale }} {{ __('preiskalkulator.select') }} <i
                            class="fas fa-sort-down"></i>
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

            @endif

            @if(count($selectobs) > $min)
                <div class="col-12 d-block d-md-none">
                    <a class="optionBlock primary float-right"
                       href="/external/preiskalkulator?id=1"> {{ __('preiskalkulator.restart') }} <i
                            class="fas fa-redo"></i></a>

                </div> @endif
            <div class="header-line mt-3"></div>
        </div>

    </div>
        <!-- Footer -->
    <div class="modal" id="resultModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>{{ __('preiskalkulator.myPersonalLanguageCourse') }}</h4>
                </div>
                <div class="modal-body">
                        <div class="optionBlock">
                            <div>
                                <div class="row">
                                    @php
                                        $kategorie = \App\PreisKategorie::find(1);

                                    @endphp
                                    <label class="col-6"><b>{{ $kategorie->name_locale }}:</b></label>
                                    <label
                                        class="col-6">@if(count($selectobs) > 0) {{ $selectobs[0]->name_locale }} @endif</label>
                                </div>
                                <div class="row">
                                    @php
                                        $kategorie = \App\PreisKategorie::find(2);

                                    @endphp
                                    <label class="col-6"><b>{{ $kategorie->name_locale }}:</b></label>
                                    <label
                                        class="col-6">@if(count($selectobs) > 1) {{ $selectobs[1]->name_locale }} @endif</label>
                                </div>
                                <div class="row">
                                    @php
                                        $kategorie = \App\PreisKategorie::find(3);

                                    @endphp
                                    <label class="col-6"><b>{{ $kategorie->name_locale }}:</b></label>
                                    <label
                                        class="col-6">@if(count($selectobs) > 2) {{ $selectobs[2]->name_locale }} @endif</label>
                                </div>
                                <div class="row">
                                    @php
                                        $kategorie = \App\PreisKategorie::find(4);

                                    @endphp
                                    <label class="col-6"><b>{{ $kategorie->name_locale }}:</b></label>
                                    <label
                                        class="col-6">@if(count($selectobs) > 3) {{ $selectobs[3]->name_locale }} @endif</label>
                                </div>
                                <div class="row">
                                    @if(count($selectobs) > $min)
                                    <label class="col-6"><b>{{ __('preiskalkulator.coursePrice') }}:</b></label>
                                    <label
                                        class="col-6">{{ $selectobs[count($selectobs)-1]->preis }}€ </label>

                                        <label class="offset-6 col-6" style="font-size: 0.8rem;">{{ __('preiskalkulator.noteBookPrice') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="optionBlock mt-2">

                            @if(count($selectobs) > $min)
                                @php
                                    $level = "-1";
                                    if(\Illuminate\Support\Facades\Session::has("level"))
                {
                    $level = \Illuminate\Support\Facades\Session::get("level","");
                }
                                @endphp
                                <div class="col-12">
                                    <label for="exampleInputEmail1">{{ __("preiskalkulator.chooseLevel") }}</label>
                                    <select class="form-control select2" name="examlevel"
                                            onchange="openUrl('{{ $url."&level=" }}' + this.value)">
                                        <option value="-1">{{ __("preiskalkulator.pleaseSelect") }}</option>
                                        @php
                                            $levels = \App\Http\Controllers\Verwaltung\Tools\PreiskalkulatorController::$levels;
                                        @endphp
                                        @foreach($levels as $auswahl)
                                            <option value="{{ $auswahl }}"
                                                    @if($level == $auswahl) selected @endif>{{ $auswahl }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @php
                                    $url .= "&level=".$level;
                                @endphp
                                @php
                                    $startDateSession = "-1";
                                    if(\Illuminate\Support\Facades\Session::has("startDate"))
                {
                    $startDateSession = \Illuminate\Support\Facades\Session::get("startDate","");
                }
                                @endphp
                                @if($level != "-1")
                                    @if(count($selectobs) >= 4)
                                        <div class="col-12">
                                            <label
                                                for="exampleInputEmail1">{{ __("preiskalkulator.courseStartQuestion") }}</label>
                                            <select class="form-control select2" name="s"
                                                    onchange="if(this.value != '-1') { openUrl('{{ $url."&startDate=" }}' + this.value) }">
                                                <option value="-1">{{ __("preiskalkulator.pleaseSelect") }}</option>
                                                <optgroup label="{{ __("preiskalkulator.chooseCourseStart") }}">
                                                    @foreach($courseStart as $startDate)
                                                        <option
                                                            value="{{ date("d.m.Y",strtotime($startDate->startDate)) }}"
                                                            @if(date("d.m.Y",strtotime($startDate->startDate)) == $startDateSession) selected @endif>{{ date("d.m.Y",strtotime($startDate->startDate)) }}</option>
                                                    @endforeach
                                                </optgroup>
                                                <optgroup label="{{ __("preiskalkulator.flexStart")  }}">
                                                    @php
                                                        $start = \Carbon\Carbon::now()->next('Monday');
                                                    @endphp
                                                    @for($i = 0; $i < 60; $i++)
                                                        <option value="{{ $start->format("d.m.Y") }}"
                                                                @if($start->format("d.m.Y") == $startDateSession) selected @endif>{{ $start->format("d.m.Y") }}</option>
                                                        @php
                                                            $start = $start->addWeeks(1);
                                                        @endphp
                                                    @endfor
                                                </optgroup>
                                            </select>
                                        </div>
                                    @endif
                                @endif
                            @endif
                                @if(\Illuminate\Support\Facades\Session::has("startDate"))

                                    <div class="col-12">
                                    <label
                                    for="exampleInputEmail1">{{ __("preiskalkulator.newCustomerQuestion") }}</label>
                                        <div class="float-right">
                                <a href="/external/preiskalkulator/?id=0&auswahl=neu">
                                    <div class="optionBlock primary m-1" style="display: inline-block">
                                        {{ __('preiskalkulator.yes') }} <i
                                            class="fas fa-arrow-right"></i>
                                    </div>
                                </a>
                                <a href="/external/preiskalkulator/?id=0&auswahl=bestand">
                                    <div class="optionBlock secondary m-1" style="display: inline-block">
                                        {{ __('preiskalkulator.no') }} <i
                                            class="fas fa-arrow-right"></i>
                                    </div>
                                </a></div>
                                    </div>
                                @endif
                            <div class="clearfix"></div>
                        </div>
                    </div>
                <div class="modal-footer">
                    @if(\Illuminate\Support\Facades\Session::has("startDate"))
                        <a href="#"  data-dismiss="modal" style="color: #000 !important;">
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

    <script>
        function openUrl(url) {
            window.location.href = url;
        }
    </script>

@endsection
@section('additionalScript')
    <script>

        @if(count($selectobs) > $min)
        $('#resultModal').modal({
            backdrop: 'static',
            keyboard: false
        });
         @endif
    </script>
@endsection
