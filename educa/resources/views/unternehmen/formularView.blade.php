@extends('layouts.unternehmen')
@section('additionalStyle')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">
@endsection
@section('appContent')
    <style>
        .signature {
            max-width: 500px;
        }
    </style>
    <div class="container">

        <h2>Formular: {{ $formular->name }}</h2>
        <div style="display: none;" class="alert alert-warning" role="alert" id="form_warn">
            Es wurden nicht alle Pflichtfelder ausgefüllt.
        </div>
        <div class="form-group">
            <label for="exampleFormControlSelect1">Student</label>
            <select class="form-control select2" id="student" required>
                @foreach($schuler as $schulers)
                    <option value="{{ $schulers->id }}">{{ $schulers->lastname }} {{ $schulers->firstname }}</option>
                @endforeach
            </select>
        </div>
        @foreach($html as $d)
            {!! $d !!}
        @endforeach
        <button type="button" class="btn btn-success" onclick="validateAndSubmit('lessonFormular')">Absenden</button>
    </div>
    <script>
        function validateAndSubmit(formId)
        {
            $("#" + formId).validate({
                lang: 'de',
                invalidHandler: function(event, validator) {
                    // 'this' refers to the form
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        var message = errors == 1
                            ? 'Es wurde ein Feld nicht ausgefüllt.'
                            : 'Es wurden ' + errors + ' Felder nicht ausgefüllt.';
                        $("#" + formId + "_warn").html(message);
                        $("#" + formId + "_warn").show();
                    } else {
                        $("#" + formId + "_warn").hide();
                    }
                },
                errorElement: 'span',
                errorPlacement: function (error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });
            finalSubmitForm();
            $("#" + formId).submit();
        }

        function finalSubmitForm() {
            $('<input>').attr('type', 'hidden')
                .attr('name', 'student_id')
                .attr('value', $('#student').val())
                .appendTo('#lessonFormular');

            const star = new Object();
            star.name = $(".starRating").attr("name");
            star.value = $(".starRating").rateYo("method", "rating");
            $('<input>').attr('type', 'hidden')
                .attr('name', star.name)
                .attr('value', star.value)
                .appendTo('#lessonFormular');
            //get signature data
            // Foreach sig !
            $(".signature").each(function( i ) {
                const sig = new Object();
                sig.name = $(this).attr("name");
                sig.value = $(this).jSignature("getData", "svg");
                //console.log("bla", sig)nan
                $('<input>').attr('type', 'hidden')
                    .attr('name', sig.name)
                    .attr('value', sig.value)
                    .appendTo('#lessonFormular');
            });
            $('#submit').attr('disabled','disabled');
        }
    </script>
@endsection
@section('additionalScript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>
    <script src="/js/jSignature/jSignature.min.js"></script>
    <script>
        $(".starRating").rateYo({
            rating: 0
        });
        $('.signature').jSignature();
    </script>
    @endsection
