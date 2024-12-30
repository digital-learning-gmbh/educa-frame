@extends('emails.bootstrap')

@section('additionalStyle')
    <style>
        h3 {
            color: #00468e;
        }
        h4 {
            color: #00468e;
        }
        .color-2 {
            color: #fd7e14 !important;
        }

        .header-line {
            height: 2px;
            width: 100%;
            background: #e0e0e0;
            position: relative;
            margin: 0 auto;
            left: 0;
            right: 0;
        }

        .wizard-fuu .nav-tabs {
            position: relative;
            margin-bottom: 0;
            border-bottom-color: transparent;
        }

        .wizard-fuu > div.wizard-fuu-inner {
            position: relative;
            top: 25px;
            text-align: center;
        }

        .connecting-line {
            height: 20px;
            background: #e0e0e0;
            position: absolute;
            width: 85%;
            margin: 0 auto;
            left: 10px;
            right: 0;
            top: 5px;
            z-index: 1;
        }

        .wizard-fuu .nav-tabs > li.active > a, .wizard-fuu .nav-tabs > li.active > a:hover, .wizard-fuu .nav-tabs > li.active > a:focus {
            color: #555555;
            cursor: default;
            border: 0;
            border-bottom-color: transparent;
        }

        span.round-tab {
            width: 30px;
            height: 30px;
            line-height: 30px;
            display: inline-block;
            border-radius: 50%;
            background: #fff;
            z-index: 2;
            position: absolute;
            left: 0;
            text-align: center;
            font-size: 16px;
            color: #0e214b;
            font-weight: 500;
            border: 1px solid #ddd;
        }
        span.round-tab i{
            color:#555555;
        }
        .wizard-fuu li.active span.round-tab {
            background: #00468e;
            color: #fff;
            border-color: #00468e;
        }
        .wizard-fuu li.active span.round-tab i{
            color: #5bc0de;
        }
        .wizard-fuu .nav-tabs > li.active > a i{
            color: #fd7e14;
            font-size: 36px;
            left: 16px;
        }

        .wizard-fuu .nav-tabs > li {
            width: 33%;
        }

        .wizard-fuu li:after {
            content: " ";
            position: absolute;
            left: 46%;
            opacity: 0;
            margin: 0 auto;
            bottom: 0px;
            border: 5px solid transparent;
            border-bottom-color: red;
            transition: 0.1s ease-in-out;
        }



        .wizard-fuu .nav-tabs > li a {
            width: 30px;
            height: 30px;
            margin: 20px auto;
            border-radius: 100%;
            padding: 0;
            background-color: transparent;
            position: relative;
            top: 0;
        }
        .wizard-fuu .nav-tabs > li a i{
            position: absolute;
            top: -15px;
            font-style: normal;
            font-weight: 400;
            white-space: nowrap;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 12px;
            font-weight: 700;
            color: #000;
        }

        .wizard-fuu .nav-tabs > li a:hover {
            background: transparent;
        }

        .wizard-fuu .tab-pane {
            position: relative;
            padding-top: 20px;
        }


        .wizard-fuu h3 {
            margin-top: 0;
        }


        .optionBlock {
            min-height: 50px;
            padding: 10px;
            font-size: 0.9rem;
            background-color: #e0e0e0;
        }

        .optionBlock.primary {
            background-color: #00468e;
            color: #fff;
        }
        .optionBlock.secondary {
            background-color: #fd7e14;
            color: #fff;
        }
        .optionBlock.clearBackground {
            background-color: transparent;
            text-align: center;
            color: #00468e;
        }

        .selectAreaWizard {
            max-height: calc(100vh - 300px);
            overflow: auto;
        }

        .selectBoxBlock {

            min-height: 20px;
            background: white;
            padding: 10px;
            margin-right: 10px;

        }
        .checkedOption {
            border: 2px solid #fd7e14;
        }
        .checkmark {
            visibility: hidden;
        }
        .checkedOption .checkmark {
            visibility: visible;
        }
    </style>
@endsection

@section('content')


    <h4 class="secondary"><strong>Sehr geehrte Damen und Herren,</strong></h4>
    <p>der Preiskalkulator wurde von einer/einem Interessent/in ausgefüllt. Unten finden Sie die erfassten Daten.</p>
    <p>Diese E-Mail wurde automatisch erzeugt.</p>

    <h4 class="secondary"><strong>Auswahl:</strong></h4>
    <h5>Berechneter Preis:  <strong>{{ $calculatedPrice }}</strong> @if(session()->has("discountCode")) mit Rabatt-Code {{ session()->get("discountCode")->code }} @endif </h5>
    <div class="row mt-5">
        <div class="col-4">
            <div class="optionBlock clearBackground">
                <b>Sprachkurs</b>
            </div>
            @if(!\Illuminate\Support\Facades\Session::has("preiskalkulator_1"))
                <h5 class="text-center">Keine Auswahl</h5>
            @else
                @foreach($firstKalkulator as $kalkoption)
                    <div class="optionBlock text-center mt-2">
                      @if($kalkoption->course_id != null)  <b>#{{ $kalkoption->course_id }}</b>  @endif {{ $kalkoption->name }}
                    </div>
                @endforeach
                <div class="optionBlock text-center mt-4">
                    <b>{{ $kalkoption->preis }}€</b>
                </div>
            @endif

        </div>
        <div class="col-4">
            <div class="optionBlock clearBackground">
                <b>Sprachprüfung</b>
            </div>
            @if(!\Illuminate\Support\Facades\Session::has("preiskalkulator_5"))
                <h5 class="text-center">Keine Auswahl</h5>
            @else
                @foreach($fiveKalkulator as $kalkoption)
                    <div class="optionBlock text-center mt-2">
                        {{ $kalkoption->name }}
                    </div>
                @endforeach
                <div class="optionBlock text-center mt-4">
                    <b>{{ $kalkoption->time_preis }}€</b>
                </div>
            @endif

        </div>
        <div class="col-4">
            <div class="optionBlock clearBackground">
                <b>Unterkunft</b>
            </div>
            @if(!\Illuminate\Support\Facades\Session::has("preiskalkulator_2"))
                <h5 class="text-center">Keine Auswahl</h5>
            @else
                @foreach($secondKalkulator as $kalkoption2)
                    <div class="optionBlock text-center mt-2">
                        {{ $kalkoption2->name }}
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
        <div class="col-4">
            <div class="optionBlock clearBackground">
                <b>Transfer</b>
            </div>
            @if(!\Illuminate\Support\Facades\Session::has("preiskalkulator_3"))
                <h5 class="text-center">Keine Auswahl</h5>
            @else
                @foreach($thirdKalkulator as $kalkoption3)
                    <div class="optionBlock text-center mt-2">
                        {{ $kalkoption3->name }}
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

        <div class="col-4">
            <div class="optionBlock clearBackground">
                <b>Zusatzleistungen</b>
            </div>
            @if(!\Illuminate\Support\Facades\Session::has("preiskalkulator_4"))
                <h5 class="text-center">Keine Auswahl</h5>
            @else
                @foreach($fourthKalkulator as $kalkoption4)
                    <div class="optionBlock text-center mt-2">
                        {{ $kalkoption4->name }}
                    </div>
                @endforeach
                <div class="optionBlock clearBackground text-center mt-2">
                </div>
                <div class="optionBlock text-center mt-4">
                    <b>{{ $kalkoption4->preis }}€</b>
                </div>

                @php $kalkoption4 = $kalkoption4->preis; @endphp
            @endif
        </div>


    </div>

    <div class="card">
        <div class="card-body">
            <div class="form-group row">
                <div class="col-3">
                    <label for="exampleInputEmail1">Geschlecht: </label>
                    {{ $data->input("gender") }}
                </div>
                <div class="col-3">
                    <label for="exampleInputEmail1">Anrede: </label>
                    {{ $data->input("salutation") }}
                </div>
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Vorname: </label>
                {{ $data->input("firstname") }}
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Nachname: </label>
                {{ $data->input("lastname") }}
            </div>
            <label for="exampleInputEmail1"><b>Kontaktdaten</b></label>
            <div class="form-group">
                <label for="exampleInputEmail1">E-Mail Adresse: </label>
                {{ $data->input("email") }}
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Telefon: </label>
                {{ $data->input("phone") }}
            </div>
            <label for="exampleInputEmail1"><b>Adresse</b></label>
            <div class="form-group">
                <label for="exampleInputEmail1">Straße: </label>
                {{ $data->input("street") }}
            </div>
            <div class="form-group row">
                <div class="col-4">
                    <label for="exampleInputEmail1">PLZ: </label>
                    {{ $data->input("plz") }}
                </div>
                <div class="col-4">
                    <label for="exampleInputEmail1">Stadt / Ort: </label>
                    {{ $data->input("city") }}
                </div>
                <div class="col-4">
                    <label for="exampleInputEmail1">Land: </label>
                    {{ $data->input("country") }}
                </div>
            </div>
            <label for="exampleInputEmail1"><b>Weitere Angaben</b></label>
            <div class="form-group row">
                <div class="col-6">
                    <label for="exampleInputEmail1">Geburtsdatum: </label>
                    {{ $data->input("birthdate") }}
                </div>
                <div class="col-6">
                    <label for="exampleInputEmail1">Nationalität: </label>
                    {{ $data->input("nation") }}
                </div>
            </div>
        </div>
    </div>


    <div class="card mt-2">
        <div class="card-body">
            <label for="exampleInputEmail1"><b>Nachricht:</b></label>
            {{ $data->input("message") }}
        </div>
    </div>


    <div class="card mt-2">
        <div class="card-body">
            <p>{{ __("preiskalkulator.textZusatzleistungen") }}</p>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="exampleCheck6" name="zusatz_pruefung" @if($data->input("zusatz_pruefung") == "on") checked @endif>
                <label class="form-check-label" for="exampleCheck6">{{ __("preiskalkulator.pruefungInteresse") }}</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="exampleCheck5" name="zusatz_transfer" @if($data->input("zusatz_transfer") == "on") checked @endif>
                <label class="form-check-label" for="exampleCheck5">{{ __("preiskalkulator.transferInteresse") }}</label>
            </div>
        </div>
    </div>

    @if(strpos($firstKalkulator[1]->name,"Individual") !== false)
    <div class="card mt-2">
        <div class="card-body">
            <label for="exampleInputEmail1"><b>Zusätzliche Informationen zum Individualunterricht</b></label>
            <div class="form-group row">
                <div class="col-4">
                    <label for="exampleInputEmail1">Anzahl der Stunden pro Woche / pro Tag: </label>
                    {{ $data->input("individual_hours_week") }}
                </div>
                <div class="col-4">
                    <label for="exampleInputEmail1">Umfang der Sitzung: </label>
                    {{ $data->input("individual_2") }}
                </div>
                <div class="col-4">
                    <label for="exampleInputEmail1">mögliche Unterrichtszeiten: </label>
                    {{ $data->input("individual_possi") }}
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="card mt-2">
        <div class="card-body">
            <label for="exampleInputEmail1"><b>Sprachkurs</b></label>
            <div class="form-group row">
                <div class="col-6">
                    <label for="exampleInputEmail1">Gewünschter Beginn: </label>
                    {{ $validatedData["startDate"] }}
                </div>
                <div class="col-6">
                    <label for="exampleInputEmail1">Niveau: </label>
                    {{ $validatedData["level"] }}
                </div>
                <div class="col-6">
                    <label for="exampleInputEmail1">Unterrichtsform: </label>
                    {{ $data->input("course_type") }}
                </div>
            </div>
        </div>
    </div>


    @if(\Illuminate\Support\Facades\Session::has("preiskalkulator_2"))
    <div class="card mt-2">
        <div class="card-body">
            <label for="exampleInputEmail1"><b>Unterkunft</b></label>
            <div class="form-group">
                <label for="exampleInputEmail1">Mitreisende: </label>
                {{ $data->input("other_persons") }}
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Besonderheiten (Allergien o.Ä.): </label>
                {{ $data->input("import_notes") }}
            </div>
            <div class="form-group row">
                <div class="col-6">
                    <label for="exampleInputEmail1">Einzug: </label>
                    {{ $data->input("start") }}
                </div>
                <div class="col-6">
                    <label for="exampleInputEmail1">Auszug: </label>
                    {{ $data->input("end") }}
                </div>
            </div>
        </div>
    </div>
    @endif

@stop
