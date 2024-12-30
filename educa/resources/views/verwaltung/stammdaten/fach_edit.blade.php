@extends('verwaltung.main')

@section('siteContent')
<h3>{{ \App\Providers\AppServiceProvider::schoolTranslation('facher', 'Fächer') }} bearbeiten</h3>
@if (session()->has('status'))
<div class="alert alert-success">
    {{ session()->get('status') }}
</div>
@endif
<!-- <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/verwaltung/schulen/{{ $schule->id }}">{{ $schule->name }}</a></li>
        <li class="breadcrumb-item"><a href="/verwaltung/schulen/{{ $schule->id }}">Fach</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $fach->name }}</li>
    </ol>
</nav> -->
<div class="card">
    <div class="card-body">
        <form method="POST">
            @csrf
        <div class="form-group">
            <label>Name</label>
            <input name="name" type="text" class="form-control" required value="{{ $fach->name }}">
        </div>
        <div class="form-group">
            <label>Abkürzung</label>
            <input name="abk" type="text" class="form-control" required value="{{ $fach->abk }}">
        </div>
            <div class="form-group">
                <label>Farbe</label>
                <div class="input-group  colorpicker-bl" title="Using input value">
                    <input type="text" name="color" class="form-control input-lg" value="{{ $fach->color }}"/>
                    <span class="input-group-append">
    <span class="input-group-text colorpicker-input-addon"><i></i></span>
  </span>
                </div>
            </div>
        <div class="form-group">
            <label>übliche Dauer (UE)</label>
            <input name="duration" type="number" class="form-control" required value="{{ $fach->duration }}">
        </div>
        <div class="form-group">
            <label>Beschreibung</label>
            <textarea name="beschreibung" type="text" class="form-control">{{ $fach->beschreibung }}</textarea>
        </div>
            <button type="submit" class="btn btn-primary">Speichern</button>
        </form>
    </div>
</div>
    @endsection
