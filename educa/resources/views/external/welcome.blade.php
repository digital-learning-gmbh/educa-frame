@extends('external.main')

@section('pageContent')
    <div class="container">
        <div class="row">

            <div class="col-12 col-md-6">
                <h3><b>{{ __('preiskalkulator.welcome') }}</b></h3>
            </div>
            <div class="header-line"></div>
        </div>

        <div class="mt-5">
            <a href="/locale/?locale=en"><img src="/images/flags/united-kingdom.svg" width="50" height="50"></a>
            <a href="/locale/?locale=de"><img src="/images/flags/germany.svg" width="50" height="50"></a>
            <h2 class="mt-2">{{ __('preiskalkulator.selectCustomerType') }}</h2>
            <div class="mt-2 row justify-content-between">
                <div class="col-4">
                    <a href="/external/preiskalkulator/?id=0&auswahl=neu"><div class="optionBlock primary">
                            {{ __('preiskalkulator.newCustomer') }} <i class="fas fa-arrow-right"></i>
                        </div></a>
                </div>
                <div class="col-4">
                    <a href="/external/preiskalkulator/?id=0&auswahl=bestand"> <div class="optionBlock secondary">
                            {{ __('preiskalkulator.existingCustomer') }} <i class="fas fa-arrow-right"></i>
                        </div></a>
                </div>
            </div>
        </div>
    </div>

@endsection
