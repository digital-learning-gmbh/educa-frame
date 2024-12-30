@extends('verwaltung.schulerakte.main')

@section('siteContent')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header" id="fehlzeitenHeading">
                <b>Fehlzeiten bearbeiten</b>
            </div>
            <div class="card-body" id="dokumentCard">
                <form method="POST">
                    @csrf
                    <div class="form-group row">
                        <label for="staticEmail" class="col-sm-2 col-form-label">Datum</label>
                        <div class="col-sm-10">
                            <input type="text" readonly class="form-control-plaintext" id="staticEmail" value="{{ date("d.m.Y H:i", strtotime($fehlzeit->eintrag->startDate)) }} - {{ date("H:i", strtotime($fehlzeit->eintrag->endDate)) }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="staticEmail" class="col-sm-2 col-form-label">Fach</label>
                        <div class="col-sm-10">
                            <input type="text" readonly class="form-control-plaintext" id="staticEmail" value=" @if($fehlzeit->eintrag->fach != null)
                               {{ $fehlzeit->eintrag->fach->name }}
                        @else
                               Fach gelöscht oder unbekannt
@endif">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="staticEmail" class="col-sm-2 col-form-label">Fehlzeit</label>
                        <div class="col-sm-10">
                            <select class="form-control select2" name="fehlzeit_typ" required>
                                @foreach($fehlzeit_typs as $fehlzeit_typ)
                                    @if($fehlzeit_typ->aktive == 1 && $fehlzeit_typ->default != true)
                                        <option value="{{ $fehlzeit_typ->id }}" @if($fehlzeit->fehlzeit_typ_id == $fehlzeit_typ->id)  selected @endif>{{ html_entity_decode($fehlzeit_typ->name) }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Speichern</button>
                </form>
            </div>
        </div>
    </div>
@endsection

