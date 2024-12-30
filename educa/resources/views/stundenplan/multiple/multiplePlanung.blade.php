@extends('layouts.loggedIn')

@section('additionalScript')
    <script>
        function openPrint(urlBase)
        {
            var win = window.open(urlBase + "&start=" + window.stundenplaner.fullCalendar("getView").start.format(), '_blank');
            win.focus();
        }
        function changeUnterrichtsform()
        {
            if($('#merkmal_form').val() != "praesenz")
            {
                $('#formgroup_program').show();
                $('#formgroup_id').show();
            } else {
                $('#formgroup_program').hide();
                $('#formgroup_id').hide();
            }
        }
        $(document).ready(function() {

            var slots = [
                    @php $useSlotsCalender = false; @endphp
                    @foreach($global_year->getTimeslots as $timeslot)
                    @if($timeslot->useable)
                    @php $useSlotsCalender = true; @endphp
                {start: '{{ $timeslot->begin }}', end: '{{ $timeslot->end }}'},
                @endif
                @endforeach
            ];

            $('#detailsShow').on('hide.bs.modal', function (e) {
                $("#updateplan").unbind( "click" );
                $("#deletePlan").unbind( "click" );
                $('#calendar').fullCalendar( 'refetchEvents' );
            });

            function showEventDetails(event)
            {
                $("#inputRoom").select2({
                    minimumInputLength: 0,
                    theme: 'bootstrap4',
                    ajax: {
                        url: "/api/search/room",
                        dataType: 'json',
                        data: function (params) {
                            return {
                                q: params.term, // search term
                                schoolclass: {{ $selectKlasse->id }},
                                school_id: {{ $global_school->id }}
                            };
                        }
                    },
                });
                $("#inputSubject").select2({
                    minimumInputLength: 0,
                    theme: 'bootstrap4',
                    ajax: {
                        url: "/api/search/subject",
                        dataType: 'json',
                        data: function (params) {
                            return {
                                q: params.term, // search term
                                schoolclass: {{ $selectKlasse->id }},
                                dozent: $("#inputDozent").val(),
                                school_id: {{ $global_school->id }}
                            };
                        }
                    },
                });
                $("#inputDozent").select2({
                    minimumInputLength: 0,
                    theme: 'bootstrap4',
                    ajax: {
                        url: "/api/search/teacher",
                        dataType: 'json',
                        data: function (params) {
                            return {
                                q: params.term, // search term
                                schoolclass: {{ $selectKlasse->id }},
                                fach: $("#inputSubject").val(),
                                school_id: {{ $global_school->id }}
                            };
                        }
                    },
                });
                $("#inputSchoolClass").select2({
                    minimumInputLength: 0,
                    theme: 'bootstrap4',
                    ajax: {
                        url: "/api/search/classes",
                        dataType: 'json',
                        data: function (params) {
                            return {
                                q: params.term, // search term
                                schuljahr: {{ $global_year->id }}
                            };
                        }
                    },
                });
                $("#merkmal_device").select2({
                    minimumInputLength: 0,
                    theme: 'bootstrap4',
                    ajax: {
                        url: "/api/search/devices",
                        dataType: 'json',
                        data: function (params) {
                            return {
                                q: params.term, // search term
                                start : moment($('#inputStartFirst').val(),"DD.MM.YYYY").format("YYYY-MM-DD") + "T" + $('#inputStart').val() + ":00",
                                end:  moment($('#inputStartFirst').val(),"DD.MM.YYYY").format("YYYY-MM-DD") + "T" + $('#inputEnd').val() + ":00",
                            };
                        }
                    },
                });
                postData('/stundenplan/ajax/lessonplan/edit', { event_id: event.plan_id }, '{{ csrf_token() }}',(data) => {
                    console.log(data);
                    var newOption = new Option(data.raum_name, data.raum_id, false, false);
                    $('#inputRoom').empty().append(newOption).change();
                    $('#inputDay').val(event.start.format('ddd'));
                    $("#inputStart").val(moment(new Date(data.startDate)).format('H:mm'));
                    $("#inputEnd").val(moment(new Date(data.endDate)).format('H:mm'));
                    var newOption = new Option(data.fach_name, data.fach_id, false, false);
                    $("#inputSubject").empty().append(newOption).change();
                    var newOption = new Option(data.lehrer_name, data.lehrer_id, false, false);
                    $("#inputDozent").empty().append(newOption).change();
                    $("#inputDesc").val(data.description);
                    $("#inputSubtitle").val(data.subtitle);
                    $("#repeatType").val(data.recurrenceType);
                    $("#intervall").val(data.recurrenceTurnus);
                    $("#inputStartFirst").val((moment(new Date(data.startDate)).format('DD.MM.YYYY')));
                    $("#inputEndLast").val((moment(new Date(data.recurrenceUntil)).format('DD.MM.YYYY')));

                    $("#inputSchoolClass").empty().change();
                    var klassen = data.klassen;
                    for (index = 0; index < klassen.length; ++index) {
                        var klasse = klassen[index];
                        newOption = new Option(klasse.name, klasse.id, true, true);
                        $("#inputSchoolClass").append(newOption).change();
                    }

                    // defualt load
                    $("#merkmal_form").val("praesenz").change();
                    $("#merkmal_programm").val("-1").change();
                    $("#merkmal_meeting_id").val("");
                    $("#merkmal_device").empty().change();
                    $("#merkmal_anrechnen").val("1").change();

                    if(data.hasOwnProperty("merkmal")) {
                        if(data.merkmal.hasOwnProperty("form")) {
                            $("#merkmal_form").val(data.merkmal.form).change();
                        }
                        if(data.merkmal.hasOwnProperty("programm")) {
                            $("#merkmal_programm").val(data.merkmal.programm).change();
                        }
                        if(data.merkmal.hasOwnProperty("meeting_id")) {
                            $("#merkmal_meeting_id").val(data.merkmal.meeting_id);
                        }
                        if(data.merkmal.hasOwnProperty("device")) {
                            var newOption = new Option(data.merkmal.device_name, data.merkmal.device, false, false);
                            $("#merkmal_device").empty().append(newOption).change();
                        }
                        if(data.merkmal.hasOwnProperty("anrechnen")) {
                            $("#merkmal_anrechnen").val(data.merkmal.anrechnen).change();
                        }
                    }
                    changeUnterrichtsform();

                    $('#detailsShow').modal('show');
                });


                $("#updateplan").click( function()
                    {
                        postData('/stundenplan/ajax/lessonplan/update', {
                            event_id: event.plan_id ,
                            start : moment($('#inputStartFirst').val(),"DD.MM.YYYY").format("YYYY-MM-DD") + "T" + $('#inputStart').val() + ":00",
                            end:  moment($('#inputStartFirst').val(),"DD.MM.YYYY").format("YYYY-MM-DD") + "T" + $('#inputEnd').val() + ":00",
                            raum_id: $('#inputRoom').val(),
                            fach_id: $("#inputSubject").val(),
                            lehrer_id: $("#inputDozent").val(),
                            schoolclasses: $('#inputSchoolClass').val(),
                            description: $("#inputDesc").val(),
                            subtitle: $("#inputSubtitle").val(),
                            recurrenceType: $('#repeatType').val(),
                            recurrenceTurnus: $('#intervall').val(),

                            merkmal_form: $('#merkmal_form').val(),
                            merkmal_programm: $('#merkmal_programm').val(),
                            merkmal_meeting_id: $('#merkmal_meeting_id').val(),
                            merkmal_device: $('#merkmal_device').val(),
                            merkmal_device_name: $('#merkmal_device option:selected').text(),
                            merkmal_anrechnen: $('#merkmal_anrechnen').val(),


                        }, '{{ csrf_token() }}',(data) => {
                            $("#updateplan").unbind( "click" );
                            $('#deletePlan').unbind("click");
                            $('#detailsShow .close').click();
                            console.log('debug');
                            console.log(data);
                        });
                    }
                );


                $('#deletePlan').click(function () {
                    postData('/stundenplan/ajax/lessonplan/delete', {
                        event_id: event.plan_id ,
                        start : event.start.format(),
                        end: event.end.format(),
                    }, '{{ csrf_token() }}',(data) => {
                        $("#updateplan").unbind( "click" );
                        $('#deletePlan').unbind("click");
                        $('#detailsShow .close').click();
                    });
                });
            }
            var cal = document.getElementById("calendar");
            window.stundenplaner =  new FullCalendar.Calendar(cal,{
                headerToolbar: {
                    left: 'today prev,next',
                    center: 'title',
                    right: 'resourceTimelineDay,resourceTimelineWeek'
                },
                locale: 'de',
                aspectRatio: 1.6,
                initialView: 'resourceTimelineDay',
                resourceGroupField: 'building',
                resources: [
                    { id: 'a', building: '460 Bryant', title: 'Auditorium A' },
                    { id: 'b', building: '460 Bryant', title: 'Auditorium B' },
                    { id: 'c', building: '460 Bryant', title: 'Auditorium C' },
                ],
                weekends: true,
                views: {
                    workWeek: {
                        type: 'agendaWeek',
                        hiddenDays: [0, 6]
                    }
                },
                resourceAreaWidth: "10%",
                defaultDate: '{{ date("Y-m-d",strtotime($startDate)) }}',
                minTime: '7:00',
                maxTime: '22:00',
                navLinks: true, // can click day/week names to navigate views
                editable: @if($editable) true @else false @endif,
                selectable: @if($editable) true @else false @endif,
                unselectAuto: true,
                selectOverlap: true,
                eventLimit: true, // allow "more" link when too many events
                nowIndicator: true,
                contentHeight: 'auto',
                snapOnSlots: {
                    snapPolicy: 'enlarge' // default: 'enlarge', could also be 'closest'
                },
                eventAfterAllRender: function() {
                    @if($type == "schoolclass")
                    updateSchulerLernfortschritt();
                    updateClassSollIst();
                    updateDozenten();
                    @endif
                    @if($type == "teacher")
                    updateSchulerLernfortschritt();
                    updateDozentenFach();
                    klasseverteilung();
                    @endif
                    @if($type == "room")
                    klasseverteilung();
                    @endif
                },
                validRange: {
                    start: '{{ date("Y-m-d",strtotime($minStart)) }}',
                    end: '{{ date("Y-m-d",strtotime("+1 day",strtotime($endDate))) }}',
                },
                allDaySlot: false,
                forceEventDuration: true,
                droppable: true, // this allows things to be dropped onto the calendar
                drop: function() {
                    $(this).remove();
                },
                eventReceive: function(event) {
                    console.log(event);

                    $('#calendar').fullCalendar('updateEvent', event);
                    postData('/stundenplan/ajax/lessonplan/create', { type: '{{ $type }}' , end_time: '{{ $endDate }}' , entwurf_id: {{ $global_entwurf->id  }}, fach_id: event.fach_id, klasse_id: {{ $selectKlasse->id }}, start : event.start.format(), end: event.end.format() }, '{{ csrf_token() }}',
                        (data) => {
                            $('#calendar').fullCalendar('removeEvents',event.id);
                            $('#calendar').fullCalendar('refetchEvents');
                        });
                },
                eventDrop: function(event, delta, revertFunc) {
                    let warn = $('#warn').prop('checked');
                    if (warn && !confirm("Möchten Sie wirklich die Zeiten dieser Stunde ändern?")) {
                        revertFunc();
                        return;
                    }
                    postData('/stundenplan/ajax/lessonplan/drop', { event_id: event.plan_id , type: '{{ $type }}', start : event.start.format(), end: event.end.format() }, '{{ csrf_token() }}',
                        (data) => {
                        });
                },
                eventResize: function(event, delta, revertFunc) {
                    let warn = $('#warn').prop('checked');
                    if (warn && !confirm("Möchten Sie wirklich die Zeiten dieser Stunde ändern?")) {
                        revertFunc();
                        return;
                    }
                    postData('/stundenplan/ajax/lessonplan/drop', { event_id: event.plan_id , start : event.start.format(), end: event.end.format() }, '{{ csrf_token() }}',
                        (data) => {
                        });

                },
                select: function(startDate, endDate, allDay, jsEvent, view ) {
                    var quickCreate = {{ $global_year->getEinstellungen("quickCreate", "true") }};
                    if(!quickCreate) {
                        $('#createModal').modal('show');
                    }
                    var startHour = new Date(startDate);
                    startHour.setHours(startHour.getHours()-1); //bug in fullcalendar showing one hour ahead
                    var endHour = new Date(endDate);
                    endHour.setHours(endHour.getHours()-1); //bug in fullcalendar showing one hour ahead
                    $('#inputStartCreate').val(moment(startDate).format('H:mm'));
                    $('#inputEndCreate').val(moment(endDate).format('H:mm'));
                    $("#inputDayCreate").val(moment(startHour).format("DD.MM.YYYY"));

                    console.log(moment(startHour).format('H:mm'));
                    console.log(moment(endHour).format('H:mm'));
                    console.log(jsEvent);

                    if(quickCreate)
                    {
                        $('#createUnterricht').click();
                    }
                    /*console.log(allDay);
                    console.log(jsEvent);
                    console.log(view);*/
                },
                eventClick: function(event, info) {
                    if (event.type === "section" || event.type === "praxisBesuch") {
                        return;
                    }
                    showEventDetails(event);
                    console.log(event);

                },
                events: {
                    url: '/api/unterricht/resource',
                    data: function () { // a function that returns an object
                        return {
                            ids: {{ $selectKlasse->id }},
                            type: '{{ $type }}',
                            entwurf_id: '{{ $global_entwurf->id }}',
                            resou
                        };

                    }
                },
                eventAfterRender: function(event, element) {
                    if (event.type === "section") {

                    } else {
                        // element.find('.fc-time').before("<div><strong style=\"font-size:14px;\">"+event.tit+"</strong></div><span><strong>"+event.fach+"</strong></span>");
                        element.find('.fc-title').after("<div class='clearfix'></div><div><span><strong>" + event.dozent + "</strong></span></div><div><span class=\"fc-dozent\">" + event.raum + "</span></div>")
                        if(event.fach_abk !== "")
                        {
                            element.find('.fc-title').html(event.fach_abk);
                        }
                    }
                },
                eventRender: function(event, element) {
                    var originalClass = element[0].className;
                    element[0].className = originalClass + ' hasmenu';
                    element.attr('data-event-id', event.unique_id);
                }
                /*dayRightclick: function(date, jsEvent, view) {
                    //alert('a day has been rightclicked!');
                    console.log(date);
                    console.log(jsEvent);
                    console.log(view);
                    // Prevent browser context menu:
                    return false;
                },
                eventRightclick: function(event, jsEvent, view) {
                    //alert('an event has been rightclicked!');
                    console.log(event);
                    console.log(jsEvent);
                    console.log(view);
                    // Prevent browser context menu:
                    return false;
                }*/
            });
            window.stundenplaner.render();

            $('#calendar').contextmenu({
                delegate: '.hasmenu',
                menu: [
                        @if($type == "schoolclass")
                    {
                        title: 'Kopieren zum nächsten Werktag',
                        cmd: 'copyTomorrow',
                        uiIcon: 'ui-icon-copy'
                    },
                    {
                        title: 'Kopieren zu nächster Woche',
                        cmd: 'copyNextWeek',
                        uiIcon: 'ui-icon-copy'
                    },
                    {
                        title: 'Kopieren zu anderer Klasse',
                        cmd: 'copyToClass',
                        uiIcon: 'ui-icon-copy'
                    },
                        @endif
                    {
                        title: 'Löschen',
                        cmd: 'deleteEvent',
                        uiIcon: 'ui-icon-trash'
                    },
                    /*{
                    title: '----'
                    },
                    {
                    title: 'Kopieren',
                        uiIcon: 'ui-icon-copy',
                        children: [{
                            title: 'zum nächsten Werktag',
                            cmd: 'copyTomorrow',
                            uiIcon: 'ui-icon-copy',

                        },
                        {
                            title: 'zu nächster Woche',
                            cmd: 'copyNextWeek'
                        }]
                    }*/
                ],
                select: function(event, ui) {
                    //console.log(event);
                    //console.log(ui);
                    switch (ui.cmd) {
                        case("copyNextWeek"):
                            postData('/stundenplan/ajax/copySingleEvent', {
                                unique_id : ui.target.closest('a').attr("data-event-id"),
                                klasse_id : {{$selectKlasse->id}},
                                toNextWeek: true,
                            }, '{{ csrf_token() }}',(data) => {
                                console.log('debug');
                                console.log(data);
                            });
                            break;
                        case("copyTomorrow"):
                            postData('/stundenplan/ajax/copySingleEvent', {
                                unique_id : ui.target.closest('a').attr("data-event-id"),
                                klasse_id : {{$selectKlasse->id}},
                                toNextWeek: false,
                            }, '{{ csrf_token() }}',(data) => {
                                console.log('debug');
                                console.log(data);
                                $('#calendar').fullCalendar('refetchEvents');

                            });
                            break;
                        case("copyToClass"):
                            $('#copyToClass').modal('show');
                            $('#hidden_unique_id').val(ui.target.closest('a').attr("data-event-id"));
                            $("#inputClass").select2({
                                minimumInputLength: 0,
                                theme: 'bootstrap4',
                                ajax: {
                                    url: "/api/search/classes",
                                    dataType: 'json',
                                    data: function (params) {
                                        return {
                                            q: '{{$schoolyear}}', // search term

                                        };
                                    }
                                },
                            });
                            break;
                        case("deleteEvent"):
                            postData('/stundenplan/ajax/lessonplan/deleteUniqueId', {
                                unique_id : ui.target.closest('a').attr("data-event-id"),
                            }, '{{ csrf_token() }}',(data) => {
                                //console.log('debug');
                                //console.log(data);
                                $('#calendar').fullCalendar('refetchEvents');

                            });
                            break;

                    }

                }

            });


            $("#createUnterricht").click( function()
            {
                console.log('{{$startDate}}');
                var start =moment($("#inputDayCreate").val(),"DD.MM.YYYY");
                var end  =moment($("#inputDayCreate").val(),"DD.MM.YYYY");

                var startHour = $("#inputStartCreate").val().split(":");
                start.hour(startHour[0]).minutes(startHour[1]);
                var endHour = $("#inputEndCreate").val().split(":");
                end.hour(endHour[0]).minutes(endHour[1]);
                console.log(start.format("YYYY-MM-DDTHH:mm:ss"));
                console.log(end.format());

                var fach = $("#inputSubjectCreate").val();
                postData('/stundenplan/ajax/lessonplan/create', { end_time: '{{ $endDate }}' , entwurf_id: {{ $global_entwurf->id  }}, fach_id: fach, klasse_id: {{ $selectKlasse->id }}, type: '{{ $type }}', start : start.format("YYYY-MM-DDTHH:mm:ss"), end: end.format("YYYY-MM-DDTHH:mm:ss") }, '{{ csrf_token() }}',
                    (data) => {
                        $('#createModal .close').click();
                        $('#calendar').fullCalendar('refetchEvents');
                        data.plan_id = data.id;
                        data.start = moment(data.start, "YYYY-MM-DDTHH:mm:ss")
                        data.end = moment(data.end, "YYYY-MM-DDTHH:mm:ss")
                        showEventDetails(data);
                    });
            });
            $("#weekCopyMenu").click( function()
            {
                var startOfWeek = $('#calendar').fullCalendar('getView').start;
                $("#copyDayStart").val(startOfWeek.format('DD.MM.YYYY'));
                var endOfWeek = new Date($('#calendar').fullCalendar('getView').end);
                endOfWeek.setDate(endOfWeek.getDate()-1);// -1 because it ends at saturday
                $("#copyDayEnd").val(moment(endOfWeek).format('DD.MM.YYYY'));
                //$("#targetDayStart").val(moment(endOfWeek).format('DD.MM.YYYY'));
            });

            $("#copyToClassBtn").click( function()
            {
                postData('/stundenplan/ajax/copyToClass', {
                    unique_id: $('#hidden_unique_id').val(),
                    copy_klasse_id : $('#inputClass').val(),
                }, '{{ csrf_token() }}',(data) => {
                    console.log('debug');
                    console.log(data);
                    $('#calendar').fullCalendar('refetchEvents');
                    $('#copyToClass').modal('hide');
                });

            });

            /*$('#datepicker101').datetimepicker({
                locale: 'de',
                format: 'DD.MM.YYYY'
            });
            $('#datepicker102').datetimepicker({
                locale: 'de',
                format: 'DD.MM.YYYY'
            });
            $('#datepicker104').datetimepicker({
                locale: 'de',
                format: 'DD.MM.YYYY'
            });
            */
            $('#datepicker103').datetimepicker({
                locale: 'de',
                format: 'DD.MM.YYYY',
                daysOfWeekDisabled: [0,2,3,4,5,6]
            });



            $("#deleteWeekUnterricht").click( function()
            {
                var startOfWeek = $('#calendar').fullCalendar('getView').start;
                var endOfWeek = $('#calendar').fullCalendar('getView').end;
                console.log(startOfWeek);
                console.log(endOfWeek);
                postData('/stundenplan/ajax/deleteWeek', {
                    start : startOfWeek,
                    end:  endOfWeek,
                    id: {{ $selectKlasse->id }},
                    type : '{{ $type }}',
                    entwurf_id: '{{ $global_entwurf->id }}'

                }, '{{ csrf_token() }}',(data) => {
                    $('#calendar').fullCalendar('refetchEvents');
                    $('#deleteWeekModal .close').click();
                    console.log('debug after delete');
                    console.log(data);
                });

            });

            $("#copyUnterricht").click( function()
            {
                var startFrom =moment($("#copyDayStart").val(),"DD.MM.YYYY");
                var endFrom  =moment($("#copyDayEnd").val(),"DD.MM.YYYY").add(23, 'hours');
                var startTo =moment($("#targetDayStart").val(),"DD.MM.YYYY").add(7, 'hours');
                //var endTo  =moment($("#targetDayEnd").val(),"DD.MM.YYYY").add(23, 'hours');//add 23 hours so you get the whole friday
                //moment($('#inputStartFirst').val(),"DD.MM.YYYY").format("YYYY-MM-DD") + "T" + $('#inputEnd').val() + ":00"
                console.log(startFrom.format());
                console.log(endFrom.format());
                console.log(startTo.format());
                postData('/stundenplan/ajax/copyWeek', {
                    start : startFrom,
                    end:  endFrom,
                    startCopy: startTo,
                    //endCopy: endTo,
                    id: {{ $selectKlasse->id }},
                    entwurf_id: '{{ $global_entwurf->id }}'

                }, '{{ csrf_token() }}',(data) => {
                    //$("#copyUnterricht").unbind( "click" );
                    //$('#deletePlan').unbind("click");
                    $('#copyWeekModal .close').click();
                    console.log('debug after copy');
                    console.log(data);
                });

            });



            $("#inputSubjectCreate").select2({
                minimumInputLength: 0,
                theme: 'bootstrap4',
                ajax: {
                    url: "/api/search/subject",
                    dataType: 'json',
                    data: function (params) {
                        return {
                            q: params.term, // search term
                            schoolclass: {{ $selectKlasse->id }},
                        };
                    }
                },
            });


            $('#printCurrentWeek').click(function () {
                html2canvas(document.getElementsByClassName('fc-view-container')[0]).then(function(canvas) {
                    $('#bugImage').empty();
                    canvas.style.height = "auto";
                    canvas.style.width = "100%";
                    canvas.id = "bugcanvas";
                    $('#bugImage').append(canvas);
                    printCanvas();
                });
            })

            function printCanvas()
            {
                var dataUrl = document.getElementById('bugcanvas').toDataURL(); //attempt to save base64 string to server using this var
                var windowContent = '<!DOCTYPE html>';
                windowContent += '<html>'
                windowContent += '<head><title>Stundenplan {{ $selectKlasse->id }}</title></head>';
                windowContent += '<body>'
                windowContent += '<img width="100%" src="' + dataUrl + '">';
                windowContent += '</body>';
                windowContent += '</html>';
                var printWin = window.open('','','');
                printWin.document.open();
                printWin.document.write(windowContent);
                printWin.document.close();
                printWin.focus();
                printWin.print();
            }
        });
    </script>

@endsection

@section('appContent')
    <div class="container-fluid subpage-main">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><b></b>
                        <nav class="navbar navbar-expand-md navbar-light bg-light" style="padding: 0px;">
                            <a class="navbar-brand" href="#">Planung für</a>
                            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                                <span class="navbar-toggler-icon"></span>
                            </button>

                            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                                <ul class="navbar-nav mr-auto">
                                    <li>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend" id="button-addon3">
                                                <a href="/stundenplan/plan?type=schoolclass" type="button" class="btn @if($type == "schoolclass") btn-secondary @else btn-outline-secondary @endif" >Klassen</a>
                                                <a href="/stundenplan/plan?type=teacher" type="button" class="btn @if($type == "teacher") btn-secondary @else btn-outline-secondary @endif">Lehrer</a>
                                                <a href="/stundenplan/plan?type=room" type="button" class="btn @if($type == "room") btn-secondary @else btn-outline-secondary @endif">Räume</a>
                                            </div>
                                            <select style="width: 200px;" multiple id="personDropDown" class="custom-select select2" onchange="this.options[this.selectedIndex].value && (window.location = '/stundenplan/plan?type={{ $type }}&id=' + this.options[this.selectedIndex].value);">
                                                @foreach($klassen as $klasse)
                                                    <option value="{{ $klasse->id }}" @if($selectKlasse->id == $klasse->id) selected @endif>{{ $klasse->displayName }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </li>
                                    <!-- <li class="nav-item active">
                                        <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#">Link</a>
                                    </li> -->
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Unterricht
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                            <a class="dropdown-item" href="#"  data-toggle="modal" data-target="#createModal">Manuell anlegen</a>
                                            @if($type == "schoolclass")
                                                <a class="dropdown-item" href="#"  data-toggle="modal" data-target="#importModal">Importieren</a>
                                                <a id="weekCopyMenu" class="dropdown-item" href="#"  data-toggle="modal" data-target="#copyWeekModal">Woche kopieren</a>
                                            @endif
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#deleteWeekModal">Unterricht löschen</a>
                                        </div>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Drucken
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                            <a class="dropdown-item" onclick="openPrint('/stundenplan/print?type={{ $type }}&id={{ $selectKlasse->id }}');" href="#" >Druckmodus öffnen</a>
                                        </div>
                                    </li>
                                </ul>
                                <ul class="navbar-nav ml-auto">
                                    <li>
                                        <div class="form-group form-check" style="float: right; display: inline-block;">
                                            <input type="checkbox" class="form-check-input" id="warn">
                                            <label class="form-check-label" for="warn" >Bei Änderungen warnen</label>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </nav>

                    </div>
                @if($selectKlasse != null)
                        <div class="card-body">
                            <div class="alert alert-info" role="alert">
                                Der Unterricht wird von {{ date("d.m.Y", strtotime($startDate)) }} bis zum {{ date("d.m.Y", strtotime($endDate)) }} geplant.
                            </div>
                            @if(!$editable)
                                <div class="alert alert-danger" role="alert">
                                    Dieser Zeitraum ist nicht mehr editierbar, da er in der Vergangheit liegt.
                                </div>
                            @endif
                            <style>
                                #calendar {
                                    margin: 0 auto;
                                }

                                .fc-day-header
                                {
                                    font-size: 16px;
                                    height: 50px;
                                    background-color: #f5f5f5;
                                    color: grey;
                                    vertical-align: middle !important;
                                    font-weight: 300;
                                }
                                .fc-toolbar.fc-header-toolbar {
                                    margin-bottom: 0px !important;
                                }
                            </style>
                            <div id='calendar'></div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('stundenplan.modale.import')
    @include('stundenplan.modale.create')
    @include('stundenplan.modale.details')
    @include('stundenplan.modale.copyWeek')
    @include('stundenplan.modale.copyToClass')
    @include('stundenplan.modale.deleteWeek')



@endsection
<style>
    .ui-contextmenu {
        z-index: 1;
    }

</style>
