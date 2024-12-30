@extends('layouts.unternehmen')

@section('appContent')
    <div class="container">
        <div class="row">
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header"><b>Praxisteilnehmer</b></div>
                    <style>
                        #calendar {
                            max-width: 1500px;
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
            <div class="col-md-5" id="anwesenheitContainer">
            </div>
        </div>
    </div>
@endsection

@section('additionalScript')
    <!--suppress VueDuplicateTag -->
    <script>

        $('#calendar').fullCalendar({
            defaultDate: "{{ date("Y-m-d") }}",
            contentHeight: 'auto',
            header: {
                left : '',
                right: 'month,prev,next'
            },
            columnHeaderFormat: 'ddd, DD.MM.',
            weekends: false,
            minTime: '7:00',
            maxTime: '22:00',
            navLinks: true, // can click day/week names to navigate views
            editable: false,
            selectable: true,
            unselectAuto: true,
            selectOverlap: true,
            eventLimit: true, // allow "more" link when too many events
            nowIndicator: true,
            displayEventTime: false,
            slotLabelFormat: 'H:mm', // uppercase H for 24-hour clock
            timeFormat: 'H:mm',
            snapOnSlots: {
                snapPolicy: 'enlarge' // default: 'enlarge', could also be 'closest'
            },
            views: {
                agendaThreeDay: {
                    buttonText: '3-Tage',
                    type: 'agenda',
                    dayCount: 3
                }
            },
            defaultView: 'month',
            allDaySlot: true,
            forceEventDuration: true,
            defaultTimedEventDuration: '01:00:00',
            eventClick: function(event, info) {
                $.get("/unternehmen/teilnehmer/" + event.date, function(data){
                        $("#anwesenheitContainer").html(data);
                });
            },
            dayClick: function(date, jsEvent, view){
                //Alle Events des angeklickten Tages (sollte nur eins sein)
                var dayEvents = $('#calendar').fullCalendar('clientEvents', function(event){
                    return event.start.isSame(date, 'day');
                });
                $.get("/unternehmen/teilnehmer/" + dayEvents[0].date, function(data){
                    $("#anwesenheitContainer").html(data);
                });
            },
            events: {
                url: '/unternehmen/teilnehmerEvents'
            }
        });

        function savePraxisTeilnahme(date) {
            var anwesenheit = $("#anwesenheitForm").serializeArray();
            console.log(anwesenheit);

            postData('/unternehmen/teilnehmer', {
                date: date,
                presence: anwesenheit
            }, '{{ csrf_token() }}',(data) => {
                $('#calendar').fullCalendar('refetchEvents');
                $.toast({
                    title: 'Gespeichert!',
                    subtitle: 'jetzt',
                    content: 'Praxisteilnahme wurde gespeichert',
                    type: 'success',
                    delay: 5000
                });
            });
        }

    </script>
@endsection
