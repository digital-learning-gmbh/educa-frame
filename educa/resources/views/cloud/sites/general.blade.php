@extends('cloud.main')

@section('cloudContent')
    <div class="container">
        @if($cloud_user->hasPermissionTo(\App\PermissionConstants::IS_MULTI_TENANT_USER))

        <h2>Allgemein</h2>
        <div class="row">
            <div class="col-6" style="text-align: right;">
                <label class="font-weight-bold">Nutzer</label>
            </div>
            <div class="col-6">
                {{ \App\CloudID::count() }}
            </div>

            <div class="col-6" style="text-align: right;">
                <label class="font-weight-bold">Gruppen</label>
            </div>
            <div class="col-6">
                {{ \App\Group::count() }}
            </div>

            <div class="col-6" style="text-align: right;">
                <label class="font-weight-bold">Gruppenmitgliedschaften</label>
            </div>
            <div class="col-6">
                {{ \Illuminate\Support\Facades\DB::table('cloudid_group')->count() }}
            </div>

            <div class="col-6" style="text-align: right;">
                <label class="font-weight-bold">Feed-Einträge</label>
            </div>
            <div class="col-6">
                {{ \App\FeedActivity::count() }}
            </div>

            <div class="col-6" style="text-align: right;">
                <label class="font-weight-bold">Dokumente</label>
            </div>
            <div class="col-6">
                {{ \App\Dokument::count() }}
            </div>

            <div class="col-6" style="text-align: right;">
                <label class="font-weight-bold">Beiträge</label>
            </div>
            <div class="col-6">
                {{ \App\Beitrag::count() }}
            </div>

            <div class="col-6" style="text-align: right;">
                <label class="font-weight-bold">Termine</label>
            </div>
            <div class="col-6">
                {{ \App\Appointment::count() }}
            </div>

            <div class="col-6" style="text-align: right;">
                <label class="font-weight-bold">Aufgaben</label>
            </div>
            <div class="col-6">
                {{ \App\Task::count() }}
            </div>

            <div class="col-6" style="text-align: right;">
                <label class="font-weight-bold">Teilnehmer</label>
            </div>
            <div class="col-6">
                {{ \App\Schuler::count() }}
            </div>

            <div class="col-6" style="text-align: right;">
                <label class="font-weight-bold">Dozenten</label>
            </div>
            <div class="col-6">
                {{ \App\Lehrer::count() }}
            </div>

            <div class="col-6" style="text-align: right;">
                <label class="font-weight-bold">Verwaltungsnutzer</label>
            </div>
            <div class="col-6">
                {{ \App\User::count() }}
            </div>

            <div class="col-6" style="text-align: right;">
                <label class="font-weight-bold">Schulen</label>
            </div>
            <div class="col-6">
                {{ \App\Schule::count() }}
            </div>

        </div>
        @else
            <p class="alert alert-info">Sie haben keinen Zugriff auf die Statistiken</p>
        @endif
    </div>
@endsection
