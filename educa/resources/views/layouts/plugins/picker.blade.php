<div class="text-center">
    <div class="btn-group" role="group" aria-label="Button group with nested dropdown" style="width: 100%;">
        @php $firstDayOfWeek->subDay(); @endphp
        <button onclick="changeDate('{{ $firstDayOfWeek->format("d.m.Y")  }}');" type="button" class="btn btn-light"><i class="fas fa-arrow-left"></i></button>

        @php $firstDayOfWeek->addDay(); @endphp
        @for($i = 0; $i < 7; $i++)
            <button onclick="changeDate('{{ $firstDayOfWeek->format("d.m.Y")  }}');" type="button" class="btn @if($firstDayOfWeek == $selectedDay) btn-primary @else btn-light @endif"><div class="@if($firstDayOfWeek == $selectedDay)  text-light @else text-primary @endif">{{ $firstDayOfWeek->getTranslatedMinDayName("ddd") }}</div>{{ $firstDayOfWeek->format("d.m.") }}</button>
            @php $firstDayOfWeek->addDay(); @endphp
        @endfor
        <button onclick="changeDate('{{ \Illuminate\Support\Carbon::today()->format("d.m.Y")  }}');" type="button" class="btn btn-light">Heute</button>

        <!--    <div class="btn-group" role="group">
                <div class="input-group date" id="datepicker2" data-target-input="nearest">
                    <input id="besuchsdatum" required name="begin" type="hidden" class="form-control datetimepicker-input" data-target="#datepicker2"/>
                        <button id="btnGroupDrop1" type="button" class="btn btn-light dropdown-toggle" data-target="#datepicker2" data-toggle="datetimepicker" aria-haspopup="true" aria-expanded="false">
    <i class="fa fa-calendar"></i>
                        </button>
                </div>

            </div> -->
        <button onclick="changeDate('{{ $firstDayOfWeek->format("d.m.Y")  }}');" type="button" class="btn btn-light"><i class="fas fa-arrow-right"></i></button>
    </div>
</div>

<script>
    function changeDate(newDate) {
        window.location.href = "/appswitcher?date=" + newDate;
    }
</script>
