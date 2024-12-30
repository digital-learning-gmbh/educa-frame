
@section('pageContent')
    <div id='calendar'></div>
@endsection


@section('additionalScript')
    <style>
        .fc-toolbar {
            background-color:  #343a40 !important;
            margin-bottom: 0px !important;
            padding: 0.5rem 1rem;
        }
        .fc-button {
            color: white !important;
            text-shadow: none !important;
        }
        .fc-left {
            color: white !important;
        }
        .fc-myCustomButton-button {
            color: white !important;
            background-color: #38c172;
            border-color: #38c172;
        }
    </style>
    <script>
        $('#calendar').fullCalendar({
            header: {
                left : 'title, myCustomButton',
                right: 'month, agendaWeek,agendaThreeDay,agendaDay, prev,next',
            },
            timeFormat: 'HH:mm',
            buttonText: {
                today:    'Heute',
                month:    'Monat',
                week:     'Woche',
                day:      'Tag',
                list:     'Terminliste'
            },
            slotLabelFormat: 'HH:mm',
            columnHeaderFormat: 'DD.MM.',
            allDayText: "Ganztägig",
            views: {
                agendaThreeDay: {
                    buttonText: '3-Tage',
                    type: 'agenda',
                    dayCount: 3
                }
            },
            events: {
                url: '/calendar/ajax',
                data: function () { // a function that returns an object
                    var group_ids = [];
                    group_ids.push('{{ $group->id }}');
                    return {
                        direct: false,
                        groups: group_ids,
                    };

                }
            },
            allDaySlot: true,
            minTime: '6:00',
            maxTime: '24:00',
            slotDuration: '00:15:00',
            forceEventDuration: true,
            defaultTimedEventDuration: '01:00:00',
            defaultView: 'agendaWeek',
            contentHeight: 'auto',// Only show week view
            // put your options and callbacks here
        });
    </script>
@endsection
