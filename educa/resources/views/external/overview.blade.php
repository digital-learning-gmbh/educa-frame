@extends('external.main')

@section('pageContent')

    <div class="container-fluid">
        @include('external.head')
        <div class="row mt-5">
            <div class="col-4">
                <div class="optionBlock clearBackground">
                    <b>{{ __('preiskalkulator.course') }}</b>
                </div>
                @if(!\Illuminate\Support\Facades\Session::has("preiskalkulator_1"))
                    <h5 class="text-center">{{ __('preiskalkulator.noChoice') }}</h5>
                 @else
                    @foreach($firstKalkulator as $kalkoption)
                    <div class="optionBlock text-center mt-2">
                        {{ $kalkoption->name_locale }}
                    </div>
                    @endforeach

                        <div class="optionBlock text-center mt-4">
                            <b>{{ $kalkoption->preis }}€</b>
                        </div>
                @php
                $bookPrice = 0; //;$firstKalkulator[2]->preis_1;
                @endphp
                    @if($bookPrice != 0)
                        <div class="optionBlock text-center mt-2">
                            <b>{{ __('preiskalkulator.bookPrice') }} {{ $bookPrice }}€</b>
                        </div>
                        @else
                            <div class="optionBlock text-center mt-2">
                                <b>{{ __('preiskalkulator.noteBookPrice') }}</b>
                            </div>
                        @endif
                @endif

            </div>
            <!-- <div class="col-2">
                <div class="optionBlock clearBackground">
                    <b>{{ __('preiskalkulator.languageTest') }}</b>
                </div>
                @if(!\Illuminate\Support\Facades\Session::has("preiskalkulator_5"))
                    <h5 class="text-center">{{ __('preiskalkulator.noChoice') }}</h5>
                @else
                    @foreach($fiveKalkulator as $kalkoption5)
                        <div class="optionBlock text-center mt-2">
                            {{ $kalkoption5->name_locale }}
                        </div>
                    @endforeach

                        <div class="optionBlock clearBackground text-center mt-2">
                        </div>
                    <div class="optionBlock text-center mt-4">
                        <b>{{ $kalkoption5->time_preis }}€</b>
                    </div>

                        @php $kalkoption5 = $kalkoption5->time_preis; @endphp
                @endif

            </div> -->
            <div class="col-4">
                <div class="optionBlock clearBackground">
                    <b>{{ __('preiskalkulator.accommodation') }}</b>
                </div>
                @if(!\Illuminate\Support\Facades\Session::has("preiskalkulator_2"))
                    <h5 class="text-center">{{ __('preiskalkulator.noChoice') }}</h5>
                @else
                    @foreach($secondKalkulator as $kalkoption2)
                        <div class="optionBlock text-center mt-2">
                            {{ $kalkoption2->name_locale }}
                        </div>
                    @endforeach
                    <div class="optionBlock text-center mt-4">
                        <b>{{ $kalkoption2->preis }}€</b>
                    </div>
                        <div class="optionBlock text-center mt-2">
                            {{ __('preiskalkulator.administrationFee') }}:<b>  {{ $secondKalkulator[0]->preis_1 }}€</b>
                        </div>
                        <div class="optionBlock text-center mt-2">
                            {{ __('preiskalkulator.deposit') }}:<b> {{ $secondKalkulator[0]->preis_2  }}€</b>
                            @php $kalkoption2 = $kalkoption2->preis + $secondKalkulator[0]->preis_2 + $secondKalkulator[0]->preis_1; @endphp
                        </div>
                @endif

            </div>
        <!--  <div class="col-2">
                <div class="optionBlock clearBackground">
                    <b>{{ __('preiskalkulator.transfer') }}</b>
                </div>
                @if(!\Illuminate\Support\Facades\Session::has("preiskalkulator_3"))
                    <h5 class="text-center">{{ __('preiskalkulator.noChoice') }}</h5>
                @else
                    @foreach($thirdKalkulator as $kalkoption3)
                        <div class="optionBlock text-center mt-2">
                            {{ $kalkoption3->name_locale }}
                        </div>
                    @endforeach
                    <div class="optionBlock clearBackground text-center mt-2">
                    </div>
                    <div class="optionBlock text-center mt-4">
                        <b>{{ $kalkoption3->preis }}€</b>
                    </div>

                        @php $kalkoption3 = $kalkoption3->preis; @endphp
                @endif
            </div>
            <div class="col-2">
                <div class="optionBlock clearBackground">
                    <b>{{ __('preiskalkulator.additionalServices') }}</b>
                </div>
                @if(!\Illuminate\Support\Facades\Session::has("preiskalkulator_4"))
                    <h5 class="text-center">{{ __('preiskalkulator.noChoice') }}</h5>
                @else
                    @foreach($fourthKalkulator as $kalkoption4)
                        <div class="optionBlock text-center mt-2">
                            {{ $kalkoption4->name_locale }}
                        </div>
                    @endforeach
                    <div class="optionBlock clearBackground text-center mt-2">
                    </div>
                    <div class="optionBlock text-center mt-4">
                        <b>{{ $kalkoption4->preis }}€</b>
                    </div>

                    @php $kalkoption4 = $kalkoption4->preis; @endphp
                @endif
            </div> -->


        </div>
        <!-- Footer -->
        <div class="row mt-3">

            <div class="col-8">
            </div>
            <div class="col-4">
                @php $extra = 0; @endphp
                @if(session()->get('kunde_type') == 'neu')
                <div class="optionBlock text-center">
                    @php $extra += \App\KalkulatorSettings::getValue('initial_amount',0); @endphp
                    <b>{{ __('preiskalkulator.registrationFee') }}: {{ \App\KalkulatorSettings::getValue('initial_amount',0) }}€</b>
                </div>
                @endif
                <div class="mt-1 optionBlock text-center">
                    <b>{{ __('preiskalkulator.sum') }}: {{ $kalkoption->preis  + $bookPrice + $kalkoption2 + $kalkoption3 + $kalkoption4 + $kalkoption5 + $extra}}€</b>
                </div>
                @if($discountCode != null)
                    <div class="mt-1 optionBlock text-center">
                        <label>{{ __('preiskalkulator.insertDiscountCode') }}</label><br>
                        <label><b>{{ $discountCode->code }}</b></label>
                        <a href="/external/preiskalkulator/?id=5&delDiscountCode" class="btn btn-danger" style="color: white;"><i class="fas fa-trash-alt"></i></a>
                    </div>
                    <div class="mt-1 optionBlock text-center">
                        <b>{{ __('preiskalkulator.sumWithDiscount') }}: {{ round(($kalkoption->preis*(100-$discountCode->percent))/100,2) + $kalkoption5 + $bookPrice + $kalkoption2 + $kalkoption3 + $kalkoption4 + $extra - $discountCode->amount }}€</b>
                    </div>
                @else
                <!-- <div class="mt-1 optionBlock text-center">
                    <label>{{ __('preiskalkulator.insertDiscountCode') }}</label>
                    <form>
                        @csrf
                    <div class="input-group mb-3">
                        <input name="id" value="5" type="hidden">
                        <input name="discountCode" type="text" class="form-control" placeholder="{{ __('preiskalkulator.insertDiscountCode') }}" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn  secondary" type="submit">{{ __('preiskalkulator.check') }}</button>
                        </div>
                    </div>
                    </form>
                </div> -->
                @endif
                <a href="/external/preiskalkulator/reserve" class="optionBlock secondary float-right mt-5"> {{ __('preiskalkulator.reserveNow') }} <i class="fas fa-check"></i></a>
            </div>
        </div>
    </div>

@endsection
