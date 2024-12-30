@extends('external.main')

@section('pageContent')
    <style>
        .form-group.required .control-label:after {
            content:"*";
            color:red;
        }
    </style>
    <div class="container">
        @include('external.head',["hideHeader" => true])
        @if ($errors->any())
            <div class="alert alert-danger mt-2">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form class="mt-5" method="POST">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="form-group row required">
                        <div class="col-3">
                            <label for="exampleInputEmail1" class="control-label">{{ __("preiskalkulator.gender") }}</label>
                            <select name="gender" class="form-control select2">
                                <option value="Männlich">{{ __("preiskalkulator.male") }}</option>
                                <option value="Weiblich">{{ __("preiskalkulator.female") }}</option>
                                <option value="Divers">{{ __("preiskalkulator.genderOther") }}</option>
                            </select>
                        </div>
                       <!-- <div class="col-3">
                            <label for="exampleInputEmail1">{{ __("preiskalkulator.salutation") }}</label>
                            <input type="text" class="form-control" id="exampleInputEmail1" name="salutation">
                        </div> -->
                    </div>
                    <div class="form-group required">
                        <label class="control-label" for="exampleInputEmail1">{{ __("preiskalkulator.firstname") }}</label>
                        <input type="text" class="form-control" id="exampleInputEmail1" required name="firstname">
                    </div>
                    <div class="form-group required">
                        <label class="control-label" for="exampleInputEmail1">{{ __("preiskalkulator.lastname") }}</label>
                        <input type="text" class="form-control" id="exampleInputEmail1" required name="lastname">
                    </div>
                    <label for="exampleInputEmail1"><b>{{ __("preiskalkulator.contactDetails") }}</b></label>
                    <div class="form-group required">
                        <label for="exampleInputEmail1" class="control-label">{{ __("preiskalkulator.email") }}</label>
                        <input type="email" class="form-control" id="exampleInputEmail1" required name="email">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1"  class="control-label">{{ __("preiskalkulator.phone") }}</label>
                        <input type="tel" class="form-control" id="exampleInputEmail1" name="phone">
                    </div>
                    <label for="exampleInputEmail1"><b>{{ __("preiskalkulator.address") }}</b></label>
                    <div class="form-group required">
                        <label for="exampleInputEmail1"  class="control-label">{{ __("preiskalkulator.street") }}</label>
                        <input type="text" class="form-control" id="exampleInputEmail1" required name="street">
                    </div>
                    <div class="form-group row required">
                        <div class="col-4">
                            <label for="exampleInputEmail1"  class="control-label">{{ __("preiskalkulator.zip") }}</label>
                            <input type="text" class="form-control" id="exampleInputEmail1" required name="plz">
                        </div>
                        <div class="col-4">
                            <label for="exampleInputEmail1"  class="control-label">{{ __("preiskalkulator.city") }}</label>
                            <input type="text" class="form-control" id="exampleInputEmail1" required name="city">
                        </div>
                        <div class="col-4">
                            <label for="exampleInputEmail1"  class="control-label">{{ __("preiskalkulator.country") }}</label>
                            <input type="text" class="form-control" id="exampleInputEmail1" required name="country">
                        </div>
                    </div>
                    <label for="exampleInputEmail1"><b>{{ __("preiskalkulator.moreInformation") }} </b></label>
                    <div class="form-group row required">
                        <div class="col-6">
                            <label for="exampleInputEmail1"  class="control-label">{{ __("preiskalkulator.birthday") }}</label>
                            @if(\Illuminate\Support\Facades\App::getLocale() == "en")
                                <div class="input-group date" id="datepicker7_en" data-target-input="nearest">
                                    <input required name="birthdate" type="text"
                                           class="form-control datetimepicker-input" data-target="#datepicker7_en" placeholder="MM-DD-YYYY" />
                                    <div class="input-group-append" data-target="#datepicker7_en"
                                         data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                                @else
                            <div class="input-group date" id="datepicker7" data-target-input="nearest">
                                <input required name="birthdate" type="text"
                                       class="form-control datetimepicker-input" data-target="#datepicker7"  placeholder="DD.MM.YYYY"/>
                                <div class="input-group-append" data-target="#datepicker7"
                                     data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                                @endif
                        </div>
                        <div class="col-6">
                            <label for="exampleInputEmail1"  class="control-label">{{ __("preiskalkulator.nationality") }}</label>
                            <input type="text" class="form-control" id="exampleInputEmail1" required name="nation">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col">
                        <label for="exampleInputEmail1">{{ __("preiskalkulator.messages") }}</label>
                        <textarea rows="3" class="form-control" name="message"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            @if(strpos($firstKalkulator[1]->name,"Individual") !== false)
                <div class="card mt-2">
                    <div class="card-body">
                        <label for="exampleInputEmail1"><b>{{ __("preiskalkulator.additionalInformationIndividual") }}</b></label>
                        <div class="form-group row">
                            <div class="col-4">
                                <label for="exampleInputEmail1">{{ __("preiskalkulator.additionalInformationIndividualCount") }}</label>
                                <input type="text" class="form-control" id="exampleInputEmail1" required name="individual_hours_week">
                            </div>
                            <div class="col-4">
                                <label for="exampleInputEmail1">{{ __("preiskalkulator.additionalInformationIndividualEachSession") }}</label>
                                <input type="text" class="form-control" id="exampleInputEmail1" required name="individual_2">
                            </div>
                            <div class="col-4">
                                <label for="exampleInputEmail1">{{ __("preiskalkulator.additionalInformationIndividualPossibleSession") }}</label>
                                <input type="text" class="form-control" id="exampleInputEmail1" required name="individual_possi">
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        <!--      <div class="card mt-2">
                <div class="card-body">
                    <label for="exampleInputEmail1"><b>{{ __("preiskalkulator.course") }}</b></label>
                    <div class="form-group row required">

                        <div class="col-4">
                            <label for="exampleInputEmail1">{{ __("preiskalkulator.level") }}</label>
                            <input class="form-control" name="level" type="text" value="{{ \Illuminate\Support\Facades\Session::get("level","") }}" disabled>
                        </div>
                        <div class="col-4">
                            <label for="exampleInputEmail1">{{ __("preiskalkulator.courseStart") }}</label>
                            <input class="form-control" name="startDate" type="text" value="{{ \Illuminate\Support\Facades\Session::get("startDate","") }}" disabled>
                        </div>
                        <div class="col-4">
                            <label for="exampleInputEmail1"  class="control-label">{{ __("preiskalkulator.courseFrom") }}</label>
                            <select class="form-control select2" name="course_type" required>
                                 <option value="Hybridkurse">{{ __("preiskalkulator.hybridCourse") }}</option>
                                <option value="Online">{{ __("preiskalkulator.onlineCourse") }}</option>
                                <option value="Präsenz">{{ __("preiskalkulator.presenceCourse") }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>  -->

            @if(\Illuminate\Support\Facades\Session::has("preiskalkulator_2"))
         <!--   <div class="card mt-2">
                <div class="card-body">
                    <label for="exampleInputEmail1"><b>{{ __("preiskalkulator.accommodation") }}</b></label>
                    <div class="form-group">
                        <label for="exampleInputEmail1">{{ __("preiskalkulator.fellowTravelers") }}</label>
                        <input type="text" class="form-control" id="exampleInputEmail1" name="other_persons">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">{{ __("preiskalkulator.accommodationNotes") }}</label>
                        <input type="text" class="form-control" id="exampleInputEmail1" name="import_notes">
                    </div>
                </div>
            </div> -->
            @endif

            <div class="card mt-2">
                <div class="card-body">
                    <p>{{ __("preiskalkulator.textZusatzleistungen") }}</p>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck6" name="zusatz_pruefung">
                        <label class="form-check-label" for="exampleCheck6">{{ __("preiskalkulator.pruefungInteresse") }}</label>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck5" name="zusatz_transfer">
                        <label class="form-check-label" for="exampleCheck5">{{ __("preiskalkulator.transferInteresse") }}</label>
                    </div>
                   <!-- <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck4" name="zusatz_zusatz">
                        <label class="form-check-label" for="exampleCheck4">{{ __("preiskalkulator.zusatzInteresse") }}</label>
                    </div> -->
                </div>
            </div>

            <div class="card mt-2">
                <div class="card-body">
                    <p>{{ __("preiskalkulator.agreeTerms") }}</p>
                    <div class="form-group form-check required">
                        <input type="checkbox" class="form-check-input" id="exampleCheck1" name="agb" required>
                        <label class="form-check-label control-label" for="exampleCheck1"><a target="_blank"
                                                                               href="https://www.academy-languages.de/agb">{{ __("preiskalkulator.agb") }}</a></label>
                    </div>
                  <!--    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck1" name="widerruf" required>
                        <label class="form-check-label" for="exampleCheck1"><a target="_blank"
                                                                               href="http://www.fuu-heidelberg-languages.com/widerrufsbelehrung/">{{ __("preiskalkulator.withdrawal") }}</a></label>
                    </div> -->
                    <div class="form-group form-check required">
                        <input type="checkbox" class="form-check-input" id="exampleCheck1" name="datenschutz" required>
                        <label class="form-check-label control-label" for="exampleCheck1"><a target="_blank"
                                                                               href="https://www.academy-languages.de/datenschutzbestimmungen">{{ __("preiskalkulator.privacypolicy") }}</a></label>
                    </div>
                </div>
            </div>
            <!-- Footer -->
            <div class="row mt-3">
                <div class="col-8">
                </div>
                <div class="col-4">
                    <button style="border: none;" type="submit" class="optionBlock secondary float-right mt-5"> {{ __("preiskalkulator.reserveNow") }}  <i class="fas fa-check"></i></button>
                </div>
            </div>
        </form>
    </div>

@endsection
