@extends('cloud.main')

@section('cloudContent')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Tenant bearbeiten: {{ $tenant->name }}</h5>
        <h6 class="card-subtitle mb-2 text-muted">Hier haben Sie die Möglichkeit einen Tenant zu bearbeiten. Wählen Sie die Farbe, Namen und Logos des Tenants</h6>
        <div style="margin-bottom: 3px;">
            <div class="clearfix"></div>
        </div>
        <form method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="exampleFormControlInput1">Domain</label>
                <input readonly disabled type="text" class="form-control" id="exampleFormControlInput1" value="{{ $tenant->domain }}">
            </div>
            <div class="form-group">
                <label for="exampleFormControlInput1">Name</label>
                <input name="name" class="form-control" id="exampleFormControlInput1" placeholder="Test Nutzer" value="{{ $tenant->name }}">
            </div>
            <div class="form-group">
                <label for="exampleFormControlInput1">Lizenz</label>
                <input name="licence" class="form-control" id="exampleFormControlInput1" placeholder="educa Lizenz" value="{{ $tenant->licence }}">
            </div>

            <div class="form-group">
                <label for="exampleFormControlInput1">max. Anzahl an Nutzern</label>
                <input name="maxUsers" type="number" class="form-control" id="exampleFormControlInput1" placeholder="10" value="{{ $tenant->maxUsers }}">
            </div>

            <ul id="tabs" class="nav nav-tabs">
                <li class="nav-item"><a href="" data-target="#home1" data-toggle="tab" class="nav-link active">Farbe & Design</a></li>
                <li class="nav-item"><a href="" data-target="#profile1" data-toggle="tab" class="nav-link">Verknüpfung mit Office 365</a></li>
                <li class="nav-item"><a href="" data-target="#messages1" data-toggle="tab" class="nav-link">Erkunden</a></li>
            </ul>

            <div id="tabsContent" class="tab-content">
                <div id="home1" class="tab-pane fade  active show m-2">
                    <div class="form-group">
                        <label>Farbe</label>
                        <div class="input-group  colorpicker-bl" title="Using input value">
                            <input type="text" name="color" class="form-control input-lg" value="{{ $tenant->color }}"/>
                            <span class="input-group-append">
    <span class="input-group-text colorpicker-input-addon"><i></i></span>
  </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Logo</label>
                        @if($tenant->logo != null)
                            <img src="/storage/images/tenants/{{ $tenant->logo }}" height="50" width="50" >
                        @endif
                        <div class="custom-file">
                            <input name="logo" type="file" class="custom-file-input" id="customFile">
                            <label class="custom-file-label" for="customFile">Datei auswählen</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Ladesymbol</label>
                        <div class="form-check">
                            <input name="overrideLoadingAnimation" class="form-check-input" type="checkbox" value="" id="defaultCheck1" @if($tenant->overrideLoadingAnimation) checked @endif>
                            <label class="form-check-label" for="defaultCheck1">
                                Ladesymbol überschreiben mit dem Logo des Tenants
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Name neben dem Logo anzeigen</label>
                        <div class="form-check">
                            <input name="hideLogoText" class="form-check-input" type="checkbox" value="" id="defaultCheck2" @if($tenant->hideLogoText) checked @endif>
                            <label class="form-check-label" for="defaultCheck2">
                                Name zusätzlich zu dem Logo anzeigen
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Bild auf der Startseite</label>
                        @if($tenant->coverImage != null)
                            <img src="/storage/images/tenants/{{ $tenant->coverImage }}" height="300" width="300" style="object-fit: cover" >
                        @endif
                        <div class="custom-file">
                            <input name="cover" type="file" class="custom-file-input" id="customFile">
                            <label class="custom-file-label" for="customFile">Datei auswählen</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Name neben dem Logo anzeigen</label>
                        <div class="form-check">
                            <input name="hideLogoText" class="form-check-input" type="checkbox" value="" id="defaultCheck2" @if($tenant->hideLogoText) checked @endif>
                            <label class="form-check-label" for="defaultCheck2">
                                Name zusätzlich zu dem Logo anzeigen
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlInput1">Impressum</label>
                        <textarea name="impressum" class="form-control">{{ $tenant->impressum }}</textarea>
                    </div>
                </div>
                <div id="profile1" class="tab-pane fade m-2">
                    <div class="form-group">
                        <label for="exampleFormControlInput1">MS Graph Client ID</label>
                        <input name="ms_graph_client_id" class="form-control" id="exampleFormControlInput1" placeholder="MS Graph Client ID" value="{{ $tenant->ms_graph_client_id }}">
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlInput1">MS Graph Secret ID</label>
                        <input name="ms_graph_secret_id" class="form-control" id="exampleFormControlInput1" placeholder="MS Graph Secret ID" value="{{ $tenant->ms_graph_secret_id }}">
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlInput1">MS Graph Tenant ID</label>
                        <input name="ms_graph_tenant_id" class="form-control" id="exampleFormControlInput1" placeholder="MS Graph Tenant ID" value="{{ $tenant->ms_graph_tenant_id }}">
                    </div>
                </div>
                <div id="messages1" class="tab-pane fade m-2">
                    <div class="form-group">
                        <label>Unter "Erkunden" für andere Plattformen anzeigen</label>
                        <div class="form-check">
                            <input name="isVisibleForOther" class="form-check-input" type="checkbox" value="" id="defaultCheck4" @if($tenant->isVisibleForOther) checked @endif>
                            <label class="form-check-label" for="defaultCheck4">
                                Anzeigen
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="exampleFormControlInput1">Beschreibung</label>
                        <textarea name="information_text" class="form-control">{{ $tenant->information_text }}</textarea>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Speichern</button>
        </form>
    </div>
</div>

@endsection
