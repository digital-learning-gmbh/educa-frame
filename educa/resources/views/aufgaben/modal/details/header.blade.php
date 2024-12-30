<h3>{{ $event->title }}
<h6>{{ \Carbon\Carbon::parse($event->startDate)->isoFormat('Do MMMM YYYY, HH:mm') }}
    <i class="fas fa-arrow-right"></i> {{ \Carbon\Carbon::parse($event->endDate)->isoFormat('Do MMMM YYYY, HH:mm') }} </h6>

