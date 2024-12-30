<div class="form-group">
    <div class="row">
        <div class="col-2">
            <label>Titel</label>
            <input type="text" class="form-control" id="title" name="title" value="{{$schuler->getAddInfo()->title}}" onchange ="setAnrede()" @if(isset($readonly) && $readonly == true) readonly @endif>
        </div>
        <div class="col-2">
            <label>Anrede</label>
            <select class="form-control" id="anrede" name="anrede" @if(isset($readonly) && $readonly == true) readonly @endif>
                <option value="herr" @if($schuler->getAddInfo()->anrede == "herr")selected="selected"@endif >Herr</option>
                <option value="frau" @if($schuler->getAddInfo()->anrede == "frau")selected="selected"@endif >Frau</option>
                <option value="divers" @if($schuler->getAddInfo()->anrede == "divers")selected="selected"@endif >Divers</option>
                <option value="na" @if($schuler->getAddInfo()->anrede == "na" || $schuler->getAddInfo()->anrede == null)selected="selected"@endif >keine Angabe</option>
            </select>
        </div>
    </div>
    <label>Vorname</label>
    <input type="text" class="form-control" id="firstname" name="firstname" value="{{$schuler->firstname}}" onchange ="setAnrede()" @if(isset($readonly) && $readonly == true) readonly @endif>
    <label>Nachname</label>
    <input type="text" class="form-control" id="lastname" name="lastname" value="{{$schuler->lastname}}" onchange ="setAnrede()" @if(isset($readonly) && $readonly == true) readonly @endif>
    <label>Schüler-ID:</label>
    <input type="text" class="form-control" name="personalnummer" value="{{$schuler->getAddInfo()->personalnummer}}" @if(isset($readonly) && $readonly == true) readonly @endif>
    <label>Schule</label>
    <select class="select2" name="school[]" multiple="multiple">
        @foreach($schulen as $schule)
            <option value="{{$schule->id}}" @if($schuler->schulen->contains($schule->id)) selected @endif>{{$schule->name}}</option>
        @endforeach
    </select>
    <label>Praxispartner</label>
    <select class="select2" name="praxisPartner">
        <option value="-1" selected>Kein Praxispartner</option>
        @php $praxisPartner = $schuler->getMerkmal("praxisPartner", "-1") @endphp
        @foreach($schuler->schulen as $schule)
        @foreach($schule->unternehmen as $unternehmen)
            <optgroup label="{{ $unternehmen->name }}">
            <option value="{{$unternehmen->id}}" @if($praxisPartner == $unternehmen->id) selected @endif>{{$unternehmen->name}}</option>
            @foreach($unternehmen->getKontakte() as $kontakt)
                    <option value="{{$kontakt->id}}" @if($praxisPartner == $kontakt->id) selected @endif>{{$kontakt->name}}</option>
                @endforeach
            </optgroup>
            @endforeach
        @endforeach
    </select>
</div>
<h5>Anschrift</h5>
<div class="form-group">
    <label>Straße</label>
    <input type="text" class="form-control" name="street" value="{{$schuler->getAddInfo()->street}}" @if(isset($readonly) && $readonly == true) readonly @endif>
    <label>PLZ</label>
    <input type="text" class="form-control" name="plz" value="{{$schuler->getAddInfo()->plz}}" @if(isset($readonly) && $readonly == true) readonly @endif>
    <label>Ort</label>
    <input type="text" class="form-control" name="city" value="{{$schuler->getAddInfo()->city}}" @if(isset($readonly) && $readonly == true) readonly @endif>
</div>

<h5>Kontaktdaten</h5>
<div class="form-group">
    <label>Telefon, geschäftlich</label>
    <input type="text" class="form-control" name="tel_business" value="{{$schuler->getAddInfo()->tel_business}}" @if(isset($readonly) && $readonly == true) readonly @endif>
    <label>Telefon, privat</label>
    <input type="text" class="form-control" name="tel_private" value="{{$schuler->getAddInfo()->tel_private}}" @if(isset($readonly) && $readonly == true) readonly @endif>
    <label>Telefon, andere</label>
    <input type="text" class="form-control" name="tel_other" value="{{$schuler->getAddInfo()->tel_other}}" @if(isset($readonly) && $readonly == true) readonly @endif>
    <label>Handy, Mobil</label>
    <input type="text" class="form-control" name="mobile" value="{{$schuler->getAddInfo()->mobile}}" @if(isset($readonly) && $readonly == true) readonly @endif>
    <label>Fax</label>
    <input type="text" class="form-control" name="fax" value="{{$schuler->getAddInfo()->fax}}" @if(isset($readonly) && $readonly == true) readonly @endif>
    <label>Email, geschäftlich</label>
    <input type="email" class="form-control" name="email" value="{{$schuler->getAddInfo()->email}}" @if(isset($readonly) && $readonly == true) readonly @endif>
    <label>Email, privat</label>
    <input type="email" class="form-control" name="email_private" value="{{$schuler->getAddInfo()->email_private}}" @if(isset($readonly) && $readonly == true) readonly @endif>
    <label>Email, sonstige</label>
    <input type="email" class="form-control" name="email_other" value="{{$schuler->getAddInfo()->email_other}}" @if(isset($readonly) && $readonly == true) readonly @endif>
    <label>Homepage</label>
    <input type="text" class="form-control" name="homepage" value="{{$schuler->getAddInfo()->homepage}}" @if(isset($readonly) && $readonly == true) readonly @endif>
</div>
