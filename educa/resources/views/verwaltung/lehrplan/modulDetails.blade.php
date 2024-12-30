@extends('verwaltung.main')

@section('siteContent')
    <h3>{{ \App\Providers\AppServiceProvider::schoolTranslation('modul', 'Modul') }}: {{ $lehrplan->name }} / {{ $modul->name }}</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/verwaltung/lehrplan">{{ __('Curriculum') }}</a></li>
            <li class="breadcrumb-item"><a href="/verwaltung/lehrplan/{{ $lehrplan->id }}">{{ $lehrplan->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Modul</li>
        </ol>
    </nav>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ \App\Providers\AppServiceProvider::schoolTranslation('modul', 'Modul') }} bearbeiten</h5>
            <h6 class="card-subtitle mb-2 text-muted">Ein {{ \App\Providers\AppServiceProvider::schoolTranslation('modul', 'Modul') }} ist ein Bestandteil einer Prüfungsordnung.</h6>

            <form method="POST">
                @csrf
                <div class="form-group">
                    <label for="exampleInputEmail1">Name</label>
                    <input name="name" type="text" class="form-control" id="exampleInputEmail1" placeholder="Name des {{ \App\Providers\AppServiceProvider::schoolTranslation('modul', 'Modul') }}"
                           value="{{ $modul->name }}" required>
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail1">Beschreibung</label>
                    <textarea name="description" type="text" class="form-control">{{ $modul->description }}</textarea>
                </div>
                <div class="row">
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">Position</label>
                        <input name="position" type="number" class="form-control" value="{{ $modul->position }}" required>
                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">gehört zu dem Profil</label>
                        <select class="select2" name="profil_id">
                            <option value="-1">Kein Profil</option>
                            @foreach($lehrplan->groups as $group)
                                <option value="{{ $group->id }}" @if($modul->profil_id == $group->id) selected @endif>{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                <div class="form-group col-6">
                    <label for="exampleInputEmail1">Teil von</label>
                    <select class="select2" name="modul">
                        <option value="-1">Kein Teil-{{ \App\Providers\AppServiceProvider::schoolTranslation('modul', 'Modul') }}</option>
                        @foreach($lehrplan->lehreinheiten() as $currentModul)
                            @if($currentModul->id != $modul->id && !$modul->isSubModul($currentModul))
                        <option value="{{ $currentModul->id }}" @if($currentModul->id == $modul->lehrplan_einheit_id) selected @endif>{{ $currentModul->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">Typ des {{ \App\Providers\AppServiceProvider::schoolTranslation('modul', 'Modul') }}</label>
                        <select class="select2" name="type">
                            <option value="pflicht" @if($modul->type== "pflicht") selected @endif>Pflicht</option>
                            <option value="wahlpflicht" @if($modul->type== "wahlpflicht") selected @endif>Wahlpflicht</option>
                            <option value="wahl" @if($modul->type== "wahl") selected @endif>Wahl</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-4">
                    <label for="exampleInputEmail1">{{ \App\Providers\AppServiceProvider::schoolTranslation('fach', 'Fach') }}, welches das {{ \App\Providers\AppServiceProvider::schoolTranslation('modul', 'Modul') }} abbildet</label>
                    <select class="select2" name="fach" id="fach_id" onchange="fachAuswahl()">
                        <option value="-1">Kein {{ \App\Providers\AppServiceProvider::schoolTranslation('fach', 'Fach') }} / Überbegriff </option>
                        <option value="-2" @if($modul->form == "praxis") selected @endif>Praxiseinsatz</option>
                    @foreach($facher as $fach)
                                <option value="{{ $fach->id }}" @if($modul->fach != null && $fach->id == $modul->fach->id) selected @endif>{{ $fach->name }}</option>
                        @endforeach
                    </select>
                </div>
                    @if($modul->form == "theorie" && $modul->fach != null)
                    <div class="form-group col-4">
                            <label for="exampleInputEmail1">{{ \App\Providers\AppServiceProvider::schoolTranslation('fach', 'Fach') }} mit umbenennen</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="defaultCheck1" name="fach_rename">
                                <label class="form-check-label" for="defaultCheck1">
                                    {{ \App\Providers\AppServiceProvider::schoolTranslation('fach', 'Fach') }} umbenennen
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-4">
                            <label for="exampleInputEmail1">Fach öffnen</label><br>
                            <a href="/verwaltung/stammdaten/fach/{{ $modul->fach->id }}" class="btn btn-outline-dark">{{ \App\Providers\AppServiceProvider::schoolTranslation('fach', 'Fach') }} anzeigen</a>
                        </div>
                        @endif
                </div>
                <div class="row">
                @if($modul->form == "praxis")
                        <div class="form-group col-6">
                            <label for="anzahl">Anzahl an notwendiger Stunden in der Praxis</label>
                            <input name="anzahl" type="text" class="form-control" id="anzahl" placeholder="Anzahl der Stunden (keine UE)"
                                   value="{{ $modul->anzahl }}" disabled="true" required>
                        </div>
                        <div class="form-group col-6">
                            <label>Farbe</label>
                            <div class="input-group  colorpicker-bl" title="Using input value">
                                <input type="text" name="color" class="form-control input-lg" value="{{ $modul->color }}"/>
                                <span class="input-group-append">
    <span class="input-group-text colorpicker-input-addon"><i></i></span>
  </span>
                            </div>
                        </div>
                @elseif($modul->fach != null)
                <div class="form-group col-6">
                    <label for="anzahl">Anzahl an notwendiger Unterrichtseinheiten / Stunden</label>
                    <input name="anzahl" type="text" class="form-control" id="anzahl" placeholder="Anzahl der UE"
                           value="{{ $modul->anzahl }}" disabled="true" required>
                </div>
                <div class="form-group col-3">
                    <label for="credits">Credits / Wertungsfaktor</label>
                    <input name="credits" type="text" class="form-control" id="credits" placeholder="Credits / Multiplikator"
                           value="{{ $modul->credits }}" disabled="true" required>
                </div>
                        <div class="form-group col-3">
                            <label for="credits">davon bereits anerkannnte Credits</label>
                            <input name="an_credits" type="text" class="form-control" id="an_credits" placeholder="Credits / Multiplikator"
                                   value="{{ $modul->an_credits }}" disabled="true" required>
                        </div>
                @else
                    <div class="alert alert-info">Dieses {{ \App\Providers\AppServiceProvider::schoolTranslation('modul', 'Modul') }} hat kein {{ \App\Providers\AppServiceProvider::schoolTranslation('fach', 'Fach') }}, daher werden Credits und UEs berechnet.</div>
                @endif
                </div>
                <button type="submit" class="btn btn-success">Speichern</button>
            </form>


    </div>
    </div>

@endsection

@section('additionalScript')
    <script>
        function fachAuswahl() {
            var fach_id = $('#fach_id').val();

            $('#anzahl').attr("disabled", false);
            $('#credits').attr("disabled", false);
            $('#an_credits').attr("disabled", false);
            if(fach_id == "-1" || $('#fach_id').val() == undefined)
            {
                $('#anzahl').val(0);
                $('#anzahl').attr("disabled", true);
                $('#credits').val(0);
                $('#credits').attr("disabled", true);
                $('#an_credits').val(0);
                $('#an_credits').attr("disabled", true);
            }
            if(fach_id == "-2" || $('#fach_id').val() == undefined)
            {
                $('#anzahl').attr("disabled", false);
                $('#credits').val(0);
                $('#credits').attr("disabled", true);
                $('#an_credits').val(0);
                $('#an_credits').attr("disabled", true);
            }
        }

        fachAuswahl();
    </script>
@endsection

