@php
$events = \App\Http\Controllers\API\EventController::loadEventsForRange($cloud_user->gruppen(), true, $selectedDay);
@endphp
@if(count($events) > 0)
<div class="pt-2">
    <div class="card" style="background-color: #f8f9fa; border: none;">
        <div class="card-body">
            <h5 class="card-title">Termine</h5>
            <h6 class="card-subtitle mb-2 text-muted">Ein Überblick an Terminen, welche an dem ausgewählten Termin relevant sind.</h6>
            <ul>
            @foreach($events as $event)
                    <li><a href="#">{{ date("H:i",strtotime($event->startDate)) }} - {{ date("H:i",strtotime($event->endDate)) }}: {{ $event->title }}</a></li>
            @endforeach
            </ul>
        </div>
    </div>
</div>
@endif
