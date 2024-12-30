<div class="card" id="detailsShow" style="display: none;">
    <div class="card-header"><b id="klassenbuch_title"></b>

        <a href="#" onclick="$('#detailsShow').hide();" class="btn btn-xs btn-secondary float-right"><i class="fas fa-times"></i></a>
        <a href="#" onclick="saveKlassenbuch();" class="btn btn-xs btn-success float-right"><i class="fas fa-check"></i></a>
    <div class="clearfix"></div>
    </div>
    <input type="hidden" value="" id="lesson_unqiue_id">
    <input type="hidden" value="" id="lesson_type">
    <input type="hidden" value="" id="fach_id">
    <input type="hidden" value="" id="lehrer_id">
    <input type="hidden" value="" id="raum_id">
    <input type="hidden" value="" id="anrechnen">
    <div class="accordion" id="accordionExample">
        <div class="card" id="lessonForm" style="display: none;">
            <div class="card-header" id="headingOne">
                <h2 class="mb-0">
                    <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne"
                            aria-expanded="true" aria-controls="collapseOne">
                        Unterrichtsinhalte
                    </button>
                </h2>
            </div>

            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">

                <input type="hidden" id="formular_revision_id">
                <h6 class="ml-3 mb-3 mt-3 text-muted">Pflichtfelder sind mit <span style="color:red"> * </span> gekennzeichnet.</h6>

                <div class="card-body" id="formular_content">

                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header" id="headingTwo">
                <h2 class="mb-0">
                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                            data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        Anwesenheit
                    </button>
                </h2>
            </div>
            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm">
                            <form id="anwesenheit">
                                <table id="anwesenheit_table" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Nachname</th>
                                            <th>Vorname</th>
                                            <th>Anwesenheit</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header" id="headingThree">
                <h2 class="mb-0">
                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                            data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        Dokumente
                    </button>
                </h2>
            </div>
            <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                <div class="card-body">
                    <p>Hier gibt es die Möglichkeit, Dokumente für diese Unterrichtsstunde bereitzustellen.</p>
                    <span id="dokumenteContainer"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    function saveKlassenbuch() {
        var unqiue_id = $('#lesson_unqiue_id').val();
        var type = $('#lesson_type').val();
        var formular_revision_id = $('#formular_revision_id').val();
        var x = $("#lessonFormular").serializeArray();
        //x.push($(".starRating").rateYo("method", "rating"));
        const star = new Object();
        star.name = $(".starRating").attr("name");
        star.value = $(".starRating").rateYo("method", "rating");
        x.push(star);
        //get signature data
        const sig = new Object();
        sig.name = $(".signature").attr("name");
        sig.value = $(".signature").jSignature("getData","svg");
        //console.log("bla", sig)
        x.push(sig);

        //console.log(document.getElementsByClassName("starRating").outerHTML);
        //console.log($(".starRating").rateYo("method", "rating"));

        var anwesenheittable = $('#anwesenheit_table').DataTable();
        var anwesenheit = anwesenheittable.$('input,select,textarea').serializeArray();

        console.log(x);
        var orginalEvent = window.selectedEntry;
        var duration = moment.duration(orginalEvent.end.diff(orginalEvent.start));

        var fach_id = $('#fach_id').val();
        var lehrer_id = $('#lehrer_id').val();
        var raum_id = $('#raum_id').val();
        //console.log($(".starRating").attr("data-rateyo-rating"));
        postData('/klassenbuch/{{ $selectedKlasse->id }}/ajax/save', {
            start: orginalEvent.start.format(),
            end: orginalEvent.end.format(),
            fach_id: fach_id,
            lehrer_id: lehrer_id,
            raum_id: raum_id,
            anrechnen: $('#anrechnen').val(),
            duration: duration.asMinutes(),
            unqiue_id: unqiue_id,
            type: type,
            formular_revision_id: formular_revision_id,
            form_data : x,
            presence: anwesenheit }, '{{ csrf_token() }}',(data) => {
            console.log(data);
            $('#createModal .close').click();
                $('#calendar').fullCalendar('refetchEvents');
                $.toast({
                title: 'Gespeichert!',
                subtitle: 'jetzt',
                content: 'Klassenbucheintrag wurde gespeichert',
                type: 'success',
                delay: 5000
            });
            $.get('/klassenbuch/{{ $selectedKlasse->id }}/ajax/dokumente?&unique_id=' + unqiue_id, function(data){
                $("#dokumenteContainer").html(data);
            });
            });
    }
</script>

<style>

    .required:after{
        content: '*';
        color: red;
        padding-left: 5px;
    }
    .tooltip-element:after{
        content: '?';
        visibility: visible;
        color: #fff;
        background: #000;
        width: 16px;
        height: 16px;
        border-radius: 8px;
        display: inline-block;
        text-align: center;
        line-height: 16px;
        margin: 0 5px;
        font-size: 12px;
        cursor: default;
    }
</style>
