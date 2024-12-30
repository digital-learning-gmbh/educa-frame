@extends('verwaltung.main')

@section('siteContent')
    <h3>{{ \App\Providers\AppServiceProvider::schoolTranslation('studiengange', 'Studiengang') }}</h3>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ $studiengang->name }}</h5>

            <form action="/verwaltung/studiengange/{{ $studiengang->id }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Name</label>
                    <input id="name" type="text" class="form-control" name="name" value="{{$studiengang->name}}">
                </div>

                <div class="form-group">
                    <label for="name_short">Kurzname</label>
                    <input id="name_short" type="text" class="form-control" name="name_short" value="{{$studiengang->name_short}}">
                </div>

                <div class="form-group">
                    <label for="teacher_id">Studiengangsleiter</label>
                    <select name="teacher_id" class="form-control select2">
                        <option value="null">Kein Studiengangsleiter</option>
                        @foreach(\App\Lehrer::all() as $lehrer)
                            <option value="{{ $lehrer->id }}" @if($studiengang->teacher_id == $lehrer->id) selected @endif>{{ $lehrer->firstname }} {{ $lehrer->lastname }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="graduation">Abschluss</label>
                    <input id="graduation" type="text" class="form-control" name="graduation" value="{{$studiengang->graduation}}">
                </div>

                <div class="form-group">
                    <label for="graduation_short">Abschluss (Abkürzung)</label>
                    <input id="graduation_short" type="text" class="form-control" name="graduation_short" value="{{$studiengang->graduation_short}}">
                </div>

                <div class="form-group">
                    <label for="normal_period">Regelstudienzeit</label>
                    <input id="normal_period" type="text" class="form-control" name="normal_period" value="{{$studiengang->normal_period}}">
                </div>

                <div class="form-group">
                    <label for="start_period">Anfangssemester</label>
                    <select id="start_period" class="form-control" name="start_period">
                        <option value="SS" @if($studiengang->start_period == "SS")selected="selected"@endif>Sommersemester</option>
                        <option value="WS" @if($studiengang->start_period == "WS")selected="selected"@endif>Wintersemester</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="valid_from">Gültig</label>
                    <input id="valid_from" type="date"  class="form-control" name="valid_from" value="{{ \Carbon\Carbon::parse($studiengang->valid_from)->format('Y-m-d')}}">
                </div>

                <div class="form-group">
                    <label for="valid_until">Gültig bis</label>
                    <input id="valid_until" type="date" class="form-control" name="valid_until" value="{{ \Carbon\Carbon::parse($studiengang->valid_until)->format('Y-m-d')}}">
                </div>

                <div class="form-group">
                    <label for="subject_direction">Bereich</label>
                    <select id="subject_direction" class="select2 form-control" name="subject_direction_id">
                        @foreach($subject_directions as $subject_direction)
                        <option value="{{ $subject_direction->id }}" @if($studiengang->subject_direction_id == $subject_direction->id)selected="selected"@endif>
                            {{ $subject_direction->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="studienorte">Studienorte</label>
                    <select id="studienorte" class="select2 form-control" name="studienorte[]" multiple>
                        @foreach(\App\Schule::all() as $schule)
                            <option value="{{ $schule->id }}" @if(in_array($schule->id, $studiengang->schulen->pluck("id")->toArray())) selected @endif>
                                {{ $schule->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-success mt-1 ">Speichern</button>
            </form>

        </div>
    </div>

@endsection

