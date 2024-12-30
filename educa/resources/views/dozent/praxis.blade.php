@extends('layouts.klassenbuch')

@section('additionalStyle')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">
@endsection

@section('appContent')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header"><b>Praxisbesuche</b></div>
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
                    <div id='calendar' style="margin-left: 5px; margin-right: 5px;"></div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card">
                    @if(!$empty)
                    <div class="card-header">
                        <b>Besuch bei {{ $termin->schulername }}, am {{ date("d.m.Y", strtotime($termin->startDate)) }} von {{ date("H:i", strtotime($termin->startDate)) }} bis {{ date("H:i", strtotime($termin->endDate)) }}</b>
                    </div>
                    <div class="accordion" id="accordionExample">
                        <div class="card">
                            <div class="card-header" id="headingOne">
                                <h2 class="mb-0">
                                    <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        Allgemeines
                                    </button>
                                    <a href="#" onclick="saveBesuchFormular();" class="btn btn-xs btn-success float-right"><i class="fas fa-check"></i></a>
                                </h2>
                            </div>

                            <div id="collapseOne" class="card-body collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                                {!! $formular !!}
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="headingThree">
                                <h2 class="mb-0">
                                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                        Dokumente
                                    </button>
                                </h2>
                            </div>
                            <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                                <div class="card-body">
                                    <p>Hier gibt es die Möglichkeit, Dokumente für diesen Praxisbesuch bereitzustellen.</p>
                                    @component('documents.list',[ "model" => $termin, "type" => "praxisbesuch"])
                                    @endcomponent
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('additionalScript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>

    <script src="/js/fullcalendar_vanilla.js" type="text/javascript"></script>
    <!--suppress VueDuplicateTag -->
    <script>
        FullCalendar.ReactContentRenderer.render(
            FullCalendar.FullCalendar,
            {
                locale: 'de',
                plugins: [FullCalendar.dayGridPlugin, FullCalendar.timeGridPlugin],
                initialView: "timeGridWeek",
                headerToolbar: {
                    start: 'title',
                    end: 'dayGridMonth,timeGridWeek,prev,next'
                },
                initialDate: "{{ $defaultDate }}",
                eventSources: {
                    url: '/api/praxisbesuche',
                    extraParams: { // a function that returns an object
                            lehrer_id: {{ $lehrer->id }}
                    },
                    method: 'GET',
                },
                eventTimeFormat: { // like '14:30'
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: false
                },
                slotLabelFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: false
                },
                dayHeaderFormat: {
                    weekday: 'short',
                    day: 'numeric',
                    month: 'numeric'
                },
                views: {
                    agendaThreeDay: {
                        buttonText: '3-Tage',
                        type: 'timeGrid',
                        duration: {days: 4}
                    }
                },
                snapOnSlots: {
                    snapPolicy: 'enlarge' // default: 'enlarge', could also be 'closest'
                },
                weekends: {{ $global_year->getEinstellungen("showWeekend", "false") }},
                slotMinTime: '8:00',
                slotMaxTime: '16:00',
                slotDuration: '00:10:00',
                allDaySlot: true,
                allDayText: "Ganzer Tag",
                nowIndicator: false,
                contentHeight: 'auto',// Only show week view
                eventContent: function (arg) {
                    var event = arg.event;
                    return {
                            html: '<div style="display: flex; flex-direction: column; height: 100%;"><div>' + moment(arg.event.start).format("HH:mm") +' - ' + moment(arg.event.end).format("HH:mm") +'</div><h5>' + event.extendedProps.unternehmenname +'</h5><i><i class="fa fa-user-tie mr-1"></i>'+ event.title +'</i></div>'
                    }
                },
                eventClick: function (args) {
                    console.log( args.event);
                    window.location.href = "/dozent/praxis/" + args.event.extendedProps.termin_id;
                }
            },
            document.getElementById("calendar")
        );



        @if(!$empty)
        function saveBesuchFormular() {
            var x = $("#praxisFormular").serializeArray();
            postData('/dozent/praxis/{{ $termin->id }}/savePraxisFormular', { form_data : x }, '{{ csrf_token() }}',(data) => {
                console.log(data);
            });
        }
        @endif
    </script>
@endsection
