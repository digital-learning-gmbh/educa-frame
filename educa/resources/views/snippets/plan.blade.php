@section('additionalScript')
    <script src='/js/fullcalender/fullcalendar.js'></script>
    <script>
        $(document).ready(function() {

            $('#calendar').fullCalendar({
                header: {
                    right: 'agendaWeek,agendaDay, prev,next'
                },
                columnHeaderFormat: 'ddd',
                weekends: false,
                minTime: '7:35',
                maxTime: '17:35',
                navLinks: true, // can click day/week names to navigate views
                editable: true,
                eventLimit: true, // allow "more" link when too many events
                nowIndicator: true,
                slotLabelFormat: 'H:mm', // uppercase H for 24-hour clock
                timeFormat: 'H:mm',
                snapOnSlots: {
                    snapPolicy: 'enlarge' // default: 'enlarge', could also be 'closest'
                },
                defaultView: 'agendaWeek', // Only show week view
                allDaySlot: false,
                defaultTimedEventDuration: '01:00:00',
                contentHeight: 700,
                slots: [
                    {start: '07:35', end: '08:20'},
                    {start: '08:25', end: '09:10'},
                    {start: '09:15', end: '10:00'},
                    {start: '10:15', end: '11:00'},
                    {start: '11:05', end: '11:50'},
                    {start: '11:55', end: '12:40'},
                    {start: '12:40', end: '13:25'},
                    {start: '13:30', end: '14:15'},
                    {start: '14:20', end: '15:05'},
                    {start: '15:10', end: '15:55'},
                    {start: '16:00', end: '16:20'},
                    {start: '16:25', end: '17:35'}
                ],
                events: {
                    url: '/api/unterricht',
                    data: function () { // a function that returns an object
                        return {
                            id: $('#personDropDown').val(),
                            type: '{{ $type }}',
                            entwurf_id: '{{ $global_entwurf->id }}'
                        };

                    }
                }
            });

        });

        function changeID() {

        }
    </script>
@endsection

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
</style>
<div id='calendar'></div>
