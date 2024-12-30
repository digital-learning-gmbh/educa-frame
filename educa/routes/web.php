<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if(request()->getHost() == config('educa.self_service.domain'))
    {
        return redirect("enroll");
    }
    return redirect('app');
});

Route::get('/connect', '\App\Http\Controllers\Auth\MSGraphController@connect')->name('connect');
Route::get('/connectMobile', '\App\Http\Controllers\Auth\MSGraphController@connectMobile')->name('connectMobile');
Route::group(['middleware' => ['web', 'MsGraphAuthenticated'], 'namespace' => 'App\Http\Controllers'], function(){
    Route::get('/msgraph/oauth', '\App\Http\Controllers\Auth\MSCallbackHandler@oauth')->name('ms.oauth');
    Route::get('/msgraph/oauthMobile', '\App\Http\Controllers\Auth\MSCallbackHandler@oauthMobile')->name('ms.oauthMobile');
});


Route::get('/sso', '\App\Http\Controllers\Auth\KeyCloakConnectController@startLogin')->name('ssoStartLogin');
Route::get('/sso/callback', '\App\Http\Controllers\Auth\KeyCloakConnectController@callback')->name('ssso-callback');

Route::get('/app', function () {
    if(request()->getHost() == config('educa.self_service.domain'))
    {
        return redirect("enroll");
    }
    return view('reactapp');
});
Route::get('/app/{any}', function () {
    if(request()->getHost() == config('educa.self_service.domain'))
    {
        return redirect("enroll");
    }
    return view('reactapp');
})->where('any', '.*');

Route::get('/login', function () {
    return redirect('/app/login');
})->name("login");

Route::prefix('pruefungsverwaltung')->group(function () {
    Route::get('/', 'AppReactController@showReactApp');
    Route::get('/{any}', 'AppReactController@showReactApp');
});
Route::any('/api/bugreport', 'BugReportController@report');

Route::get('/lms_a24', function () {
    \Illuminate\Support\Facades\Auth::guard('cloud')->login(\App\CloudID::find(1));
    \Illuminate\Support\Facades\Session::put("cloud_user", \App\CloudID::find(1));
    return redirect('/lms/viewer?c=a24&chapter=0&page=1');
});


// Install routes
Route::get('/migrate/db/{token}','\App\Http\Controllers\Install\InstallController@databaseMigration');
Route::get('/migrate/clearCache/{token}','\App\Http\Controllers\Install\InstallController@clearCache');


if(config('educa.self_service.enabled'))
{
    Route::prefix('enroll')->group(function () {
        Route::get('/', function () {
            if(request()->getHost() != config('educa.self_service.domain'))
            {
                redirect("/");
            }
            return view('enroll');
        });
    });
}

Route::get('/css/app_branded.css', [\App\Http\Controllers\Controller::class,'loadCss']);
Route::prefix('verwaltung')->group(function () {

    Route::prefix('image')->group(function () {
        Route::get('/schuler', 'API\ImageProvider@getUserImage');
        Route::get('/klasse', 'API\ImageProvider@getKlasseImage');
    });

    Route::get('/module', 'Verwaltung\Stammdaten\ModuleController@index');

    Route::prefix('stammdaten')->group(function () {
        Route::get('/', function () {
            return redirect('/verwaltung/stammdaten/eventOverview');
        });

        Route::get('/eventOverview', 'Verwaltung\Stammdaten\DozentenController@index');
        Route::get('/providers', 'Verwaltung\Stammdaten\DozentenController@index');
        Route::get('/locations', 'Verwaltung\Stammdaten\DozentenController@index');

        Route::get('/dozenten', 'Verwaltung\Stammdaten\DozentenController@index');
        Route::get('/dozenten/import', 'Verwaltung\Stammdaten\DozentenController@import');
        Route::get('/dozenten/{id}/edit', 'Verwaltung\Stammdaten\DozentenController@edit');
        Route::post('/dozenten/{id}/edit', 'Verwaltung\Stammdaten\DozentenController@store')->name('dozent.store');
        Route::post('/dozenten/create', 'Verwaltung\Stammdaten\DozentenController@create')->name('dozent.create');
        Route::post('/dozenten/excelimport', 'Verwaltung\Stammdaten\DozentenController@excelImport')->name('dozent.excelimport');
        Route::get('/dozenten/{id}/delete', 'Verwaltung\Stammdaten\DozentenController@delete')->name('dozent.delete');

        // Klassen
        Route::get('/klassen', 'Verwaltung\Stammdaten\KlassenController@index');
        Route::post('/klassen/create', 'Verwaltung\Stammdaten\KlassenController@create')->name('klasse.create');
        Route::get('/klassen/{id}/edit', 'Verwaltung\Stammdaten\KlassenController@edit');
        Route::post('/klassen/{id}/editFormular', 'Verwaltung\Stammdaten\KlassenController@editFormular')->name('klasse.storeFormular');
        Route::post('/klassen/{id}/edit', 'Verwaltung\Stammdaten\KlassenController@store')->name('klasse.store');
        Route::post('/klassen/{id}/addSchuler', 'Verwaltung\Stammdaten\KlassenController@addSchuler')->name('klasse_schuler.create');
        Route::get('/klassen/{id}/deleteSchuler/{id2}', 'Verwaltung\Stammdaten\KlassenController@deleteSchuler')->name('klasse_schuler.deleteSchuler');
        Route::get('/klassen/{id}/ausscheidenSchuler/{id2}', 'Verwaltung\Stammdaten\KlassenController@ausscheidenSchuler')->name('klasse_schuler.ausscheidenSchuler');
        Route::get('/klassen/{id}/delete', 'Verwaltung\Stammdaten\KlassenController@delete')->name('klasse.delete');
        Route::post('/klassen/{id}/move', 'Verwaltung\Stammdaten\KlassenController@move')->name('klasse.move');

        // Raume
        Route::post('/raume/create', 'Verwaltung\Stammdaten\RaumController@create')->name('raum.create');
        Route::get('/raume/{id}/edit', 'Verwaltung\Stammdaten\RaumController@edit');
        Route::post('/raume/{id}/edit', 'Verwaltung\Stammdaten\RaumController@store')->name('raum.store');
        Route::get('/raume/{id}/delete', 'Verwaltung\Stammdaten\RaumController@delete')->name('raum.delete');

        // Verwaltungsmitarbeiter
        Route::get('/benutzer', 'Verwaltung\Stammdaten\BenutzerController@index');
        Route::post('/benutzer/create', 'Verwaltung\Stammdaten\BenutzerController@create')->name('benutzer.create');
        Route::get('/benutzer/{id}/edit', 'Verwaltung\Stammdaten\BenutzerController@edit');
        Route::post('/benutzer/{id}/edit', 'Verwaltung\Stammdaten\BenutzerController@update');


        Route::get('/ansprechpartner', 'Verwaltung\Stammdaten\KontakteController@ansprechpartner');

        // Kontakte wie externe Firmen etc.
        Route::get('/kontakte', 'Verwaltung\Stammdaten\KontakteController@index');
        Route::post('/kontakte/create', 'Verwaltung\Stammdaten\KontakteController@create')->name('kontakt.create');
        Route::get('/kontakte/{id}/createZugang', 'Verwaltung\Stammdaten\KontakteController@createZugang');
        Route::get('/kontakte/{id}/edit', 'Verwaltung\Stammdaten\KontakteController@edit');
        Route::post('/kontakte/{id}/edit', 'Verwaltung\Stammdaten\KontakteController@store')->name('kontakt.store');
        Route::post('/kontakte/{id}/storeFormular', 'Verwaltung\Stammdaten\KontakteController@storeFormular')->name('kontakt.storeFormular');
        Route::get('/kontakte/{id}/delete', 'Verwaltung\Stammdaten\KontakteController@delete')->name('kontakt.delete');
        Route::post('/kontakte/{id}/addBeziehung', 'Verwaltung\Stammdaten\KontakteController@addBeziehung')->name('kontaktbeziehung.create');
        Route::get('/kontakte/{id}/deleteBeziehung/{id2}', 'Verwaltung\Stammdaten\KontakteController@deleteBeziehung')->name('kontakt.deleteBeziehung');
        Route::post('/kontakte/{id}/addPraxiskapazitaet', 'Verwaltung\Stammdaten\KontakteController@addKapazitaet')->name('praxiskapazitaet.create');
        Route::get('/kontakte/{id}/deleteKapazitaet/{id2}', 'Verwaltung\Stammdaten\KontakteController@deleteKapazitaet')->name('kontakt.deleteKapazitaet');

        // Fächer
        Route::get('/fach', 'Verwaltung\SchulController@listFach');
        Route::post('/fach/addFach', 'Verwaltung\SchulController@addFach')->name('schule.addFach');
        Route::get('/fach/{id2}', 'Verwaltung\SchulController@editFach');
        Route::get('/fach/{id2}/delete', 'Verwaltung\SchulController@deleteFach');
        Route::post('/fach/{id2}', 'Verwaltung\SchulController@saveFach');
    });

//    Route::prefix('schulerlisten')->group(function () {
//        Route::get('/', 'Verwaltung\TeilnehmerController@index');
//        Route::post('/add', 'Verwaltung\TeilnehmerController@add');
//        Route::get('/import', 'Verwaltung\TeilnehmerController@import');
//        Route::post('/excelImport', 'Verwaltung\TeilnehmerController@excelImport');
//        Route::get('/{id}', 'Verwaltung\TeilnehmerController@edit');
//        Route::post('/{id}', 'Verwaltung\TeilnehmerController@store')->name('schuler.store');
//        Route::post('/{id}/formsubmit/{id2}', 'Verwaltung\TeilnehmerController@storeFormular');
//        Route::get('/{id}/delete', 'Verwaltung\TeilnehmerController@delete');
//
//        Route::get('/{id}/noten', 'Verwaltung\TeilnehmerController@noten');
//
//        Route::get('/{id}/fehlzeiten', 'Verwaltung\TeilnehmerController@fehlzeiten');
//        Route::get('/{id}/fehlzeiten/reserved/{id2}/delete', 'Verwaltung\TeilnehmerController@fehlzeitenDeleteVorgemerkt');
//        Route::get('/{id}/fehlzeiten/normal/{id2}/edit', 'Verwaltung\TeilnehmerController@fehlzeitenEdit');
//        Route::post('/{id}/fehlzeiten/normal/{id2}/edit', 'Verwaltung\TeilnehmerController@saveFehlzeitenEdit');
//
//        Route::get('/{id}/kenntnisse', 'Verwaltung\TeilnehmerController@kenntnisse');
//        Route::post('/{id}/kenntnisse/create', 'Verwaltung\TeilnehmerController@createKenntnis')->name('kenntnis.create');
//        Route::get('/{id}/kenntnisse/{id2}/delete', 'Verwaltung\TeilnehmerController@deleteKenntnis');
//
//        Route::get('/{id}/merkmale/{id2}/delete', 'Verwaltung\TeilnehmerController@deleteMerkmal');
//
//        Route::get('/{id}/dokumente', 'Verwaltung\TeilnehmerController@dokumente');
//
//        Route::get('/{id}/progress', 'Verwaltung\TeilnehmerController@progress');
//
//        Route::get('/{id}/klassen', 'Verwaltung\TeilnehmerController@klassenEdit');
//        Route::get('/{id}/partners', 'Verwaltung\TeilnehmerController@partners');
//        Route::get('/{id}/practice', 'Verwaltung\TeilnehmerController@practice');
//        Route::get('/{id}/practiceexport', 'Verwaltung\TeilnehmerController@practiceExport');
//        Route::get('/{id}/cloud', 'Verwaltung\TeilnehmerController@cloud');
//    });


    Route::prefix('studiengange')->group(function () {
        Route::get('/', 'Verwaltung\StudiengangController@index');
        Route::post('/add', 'Verwaltung\StudiengangController@create')->name('studiengang.create');
        Route::get('/{id}', 'Verwaltung\StudiengangController@details');
        Route::post('/{id}', 'Verwaltung\StudiengangController@update');
    });



    // Schuljahre
    Route::prefix('schuljahre')->group(function () {
        Route::get('/', 'Verwaltung\SchulController@schuljahre');
        Route::post('/addSchuljahr', 'Verwaltung\SchulController@addSchuljahr')->name('schule.addSchuljahr');
        Route::get('/schuljahr/{id2}', 'Verwaltung\SchulController@editSchuljahr');
        Route::post('/schuljahr/{id2}', 'Verwaltung\SchulController@saveSchuljahr');
        // Zeitslot am Schuljahr
        Route::post('/schuljahr/{id2}/addTimeslot', 'Verwaltung\SchulController@addTimeslot');
        Route::post('/schuljahr/{id2}/editTimeslot', 'Verwaltung\SchulController@editTimeslot');
        Route::get('/schuljahr/{id2}/timeslot/{id3}/delete', 'Verwaltung\SchulController@deleteTimeslot');
        // Entwürfe
        Route::get('/schuljahr/{id2}/generate', 'Verwaltung\SchulController@generateForEntwurf');

        // Erweiterte Einstellungen
        Route::get('/schuljahr/{id2}/additional/fehlzeiten', 'Verwaltung\SchulController@fehlzeiten');
    });

    // Schulverwaltung

    Route::prefix('schulen')->group(function () {
        Route::get('/settings', 'Verwaltung\SchulController@index');
        Route::get('/', 'Verwaltung\SchulController@index');
        Route::get('/{id}', 'Verwaltung\SchulController@edit');
        Route::post('/{id}', 'Verwaltung\SchulController@store')->name('schule.store');
        Route::post('/{id}/einstellungen', 'Verwaltung\SchulController@einstellungen')->name('schule.einstellungen');
        Route::post('/{id}/bezeichnungen', 'Verwaltung\SchulController@bezeichnungen')->name('schule.bezeichnungen.store');
    });

    Route::get('vorlagen', 'Verwaltung\VorlagenController@index');

    Route::prefix('technical')->group(function () {
        Route::get('/', function () {
            return redirect('/verwaltung/technical/info');
        });
        Route::get('info', 'Verwaltung\Technical\InfoController@index');
        Route::get('jobs', 'Verwaltung\Technical\InfoController@jobs');
        Route::get('logs', 'Verwaltung\Technical\LogViewerController@index');
        Route::get('searchIndex', 'Verwaltung\Technical\InfoController@searchIndex');
        Route::get('searchIndex/{id}', 'Verwaltung\Technical\InfoController@recreateIndex');
        Route::get('clingo', 'Verwaltung\Technical\ClingoTestController@index');
        Route::post('clingo/execute', 'Verwaltung\Technical\ClingoTestController@executeCommand');
        Route::post('clingo/timetable', 'Verwaltung\Technical\ClingoTestController@executeTimetable');
    });

    Route::prefix('lehrplan')->group(function () {
        Route::get('/', 'Verwaltung\LehrplanController@index');
        Route::post('/create', 'Verwaltung\LehrplanController@create')->name('lehrplan.create');
        Route::post('/importExcel', 'Verwaltung\LehrplanController@importExcel')->name('lehrplan.excelImport');
        Route::get('/{id}', 'Verwaltung\LehrplanController@details');
        Route::post('/{id}', 'Verwaltung\LehrplanController@saveDetails');
        Route::post('/{id}/module', 'Verwaltung\LehrplanController@createModul')->name('modul.create');
        Route::get('/{id}/module/{id2}', 'Verwaltung\LehrplanController@modulDetails');
        Route::get('/{id}/module/{id2}/delete', 'Verwaltung\LehrplanController@deleteModul');
        Route::post('/{id}/module/{id2}', 'Verwaltung\LehrplanController@saveModulDetails');
        Route::post('/{id}/group', 'Verwaltung\LehrplanController@createGroupLehrplan')->name('groupLehrplan.create');
        Route::post('/{id}/group/update', 'Verwaltung\LehrplanController@updateGroupLehrplan')->name('groupLehrplan.update');
        Route::get('/{id}/group/{id2}/delete', 'Verwaltung\LehrplanController@deleteGroupLehrplan');
    });

    Route::prefix('einstellungen')->group(function () {
        Route::get('/', 'Verwaltung\EinstellungenController@index');
        Route::get('/aufgaben', 'Aufgaben\AufgabenProvider@overview');
        Route::get('/aufgaben/watch', 'Aufgaben\AufgabenProvider@runFromUI');
        Route::get('/aufgaben/watch/{model}', 'Aufgaben\AufgabenProvider@runFromUISingle');
        Route::get('/aufgaben/settings/{model}', 'Aufgaben\AufgabenProvider@settings');
        Route::post('/aufgaben/settings/{model}', 'Aufgaben\AufgabenProvider@saveSettings');

        //
        Route::get('/apikeys','Verwaltung\EinstellungenController@apikeys');
        Route::get('/system','Verwaltung\EinstellungenController@system');
        Route::post('/system','Verwaltung\EinstellungenController@systemUpdate');

        Route::prefix('formulare')->group(function () {
            Route::get('/', 'Formular\ManageController@index');
            Route::post('/create', 'Formular\ManageController@create')->name('formular.create');
            Route::get('/{id}/edit', 'Formular\ManageController@edit');
            Route::post('/{id}/edit', 'Formular\ManageController@save');
            Route::get('/{id}/export', 'Formular\ManageController@export');
        });

        Route::prefix('templates')->group(function () {
            Route::get('/', 'Template\ManageController@index');
            Route::post('/create', 'Template\ManageController@create')->name('template.create');
            Route::get('/{id}/edit', 'Template\ManageController@edit');
            Route::post('/{id}/edit', 'Template\ManageController@save');
            Route::get('/{id}/export', 'Template\ManageController@export');
        });

        Route::get('/kalender','Verwaltung\KalenderController@index');
        Route::get('/kalender/{id}','Verwaltung\KalenderController@kalender');
        Route::get('/kalender/{id}/copy','Verwaltung\KalenderController@copy');
        Route::get('/kalender/{id}/activate','Verwaltung\KalenderController@activate');
        Route::get('/kalender/{id}/delete','Verwaltung\KalenderController@delete');
        Route::get('/kalender/{id}/deleteFerienzeit/{id2}','Verwaltung\KalenderController@deleteFerienzeit')->name('kalender.deleteFerienzeit');
        Route::post('/kalender/{id}/save','Verwaltung\KalenderController@saveKalender')->name('kalender.rename');

        Route::post('/kalender/create','Verwaltung\KalenderController@create')->name('kalender.create');
        Route::post('/kalender/{id}/addFerienzeit','Verwaltung\KalenderController@addFerienzeit')->name('kalender.addFerienzeit');
        Route::post('/kalender/{id}/import','Verwaltung\KalenderController@import')->name('kalender.importFerien');
    });

    if(config('stupla.preiskalkulator.active'))
    {
        Route::prefix('preiskalkulator')->group(function () {
            Route::get('/', 'Verwaltung\Tools\PreiskalkulatorController@index');
            Route::post('/', 'Verwaltung\Tools\PreiskalkulatorController@updateOrCreateAuswahl');
            Route::get('/deleteAuswahl', 'Verwaltung\Tools\PreiskalkulatorController@deleteAuswahl');
            Route::get('/copyAuswahl', 'Verwaltung\Tools\PreiskalkulatorController@copyAuswahl');
            Route::get('/settings', 'Verwaltung\Tools\PreiskalkulatorController@settings');
            Route::post('/settings', 'Verwaltung\Tools\PreiskalkulatorController@updateSettings');

            Route::get('/discount', 'Verwaltung\Tools\PreiskalkulatorController@discount');
            Route::post('/discount/add', 'Verwaltung\Tools\PreiskalkulatorController@addDiscount')->name('discount.create');
            Route::get('/discount/{id}/delete', 'Verwaltung\Tools\PreiskalkulatorController@deleteDiscount');

            Route::get('/start', 'Verwaltung\Tools\PreiskalkulatorController@start');
            Route::post('/start/add', 'Verwaltung\Tools\PreiskalkulatorController@addStart')->name('startDate.create');
            Route::get('/start/{id}/delete', 'Verwaltung\Tools\PreiskalkulatorController@deleteStart');
        });


    }

    if(config('stupla.preiskalkulator.active'))
    {
        Route::prefix('chat')->group(function () {
            Route::get('', 'Verwaltung\Tools\ChatController@index');
        });
    }

    if(config('stupla.einstufungstest.active'))
    {
        Route::prefix('einstufungstest')->group(function () {
            Route::get('', 'Verwaltung\Tools\EinstufungstestAdminController@index');
            Route::get('/create', 'Verwaltung\Tools\EinstufungstestAdminController@create');
            Route::get('/test/{id}', 'Verwaltung\Tools\EinstufungstestAdminController@edit');
            Route::post('/test/{id}', 'Verwaltung\Tools\EinstufungstestAdminController@save');
            Route::get('/test/{id}/delete', 'Verwaltung\Tools\EinstufungstestAdminController@delete');
            Route::get('/test/{id}/auswertung', 'Verwaltung\Tools\EinstufungstestAdminController@auswertung');
            Route::get('/test/{id}/auswertung/{id2}', 'Verwaltung\Tools\EinstufungstestAdminController@auswertungDetail');
        });
    }


    Route::get('/', 'Verwaltung\VerwaltungController@displayReactApp');
    Route::get('/{any}', 'Verwaltung\VerwaltungController@displayReactApp');
});

Route::prefix('board')->group(function () {
    Route::get('/', 'AppReactController@showReactApp');
    Route::get('/{any}', 'AppReactController@showReactApp');

    Route::prefix('ajax')->group(function () {
        // Lernfortschritt der Klasse
        Route::post('/lernfortschrittKlasse', 'Dashboard\old_widgets\KlasseLernfortschrittWidget@lernfortschritt');
        // Lernfortschritt der Schüler
        Route::post('/lernfortschrittSchuler', 'Dashboard\old_widgets\SchulerLernfortschrittWidget@lernfortschritt');
    });
});

Route::prefix('stundenplan')->group(function () {
    Route::get('/', 'Stundenplan\StundenplanController@index');
    Route::post('/plan/{id}/import', 'Stundenplan\StundenplanController@import');

    // Print API
    Route::get('/printDisplay', 'Stundenplan\PrintController@printDisplay');
    Route::get('/print', 'Stundenplan\PrintController@print');
});

Route::prefix('klassenbuch')->group(function () {
    Route::get('/', 'Klassenbuch\KlassenbuchController@index');
    Route::get('/{id}', 'Klassenbuch\KlassenbuchController@klassenbuch');

    Route::get('/{id}/schueler', 'Klassenbuch\KlassenbuchController@teilnehmer');
    Route::get('/{id}/schueler/{id2}', 'Klassenbuch\KlassenbuchController@teilnehmerDetails');

    Route::post('/{id}/schueler/{id2}/createNote', 'Klassenbuch\KlassenbuchController@createNote')->name('note.create');
    Route::post('/note/edit/{id}', 'Klassenbuch\KlassenbuchController@editNote')->name('note.edit');
    Route::get('/note/delete/{id}', 'Klassenbuch\KlassenbuchController@deleteNote')->name('note.delete');

    Route::post('/{id}/schueler/{id2}/createFehlzeit', 'Klassenbuch\KlassenbuchController@createFehlzeit')->name('fehlzeit.create');

    Route::get('/{id}/progress', 'Klassenbuch\KlassenbuchController@progress');
    Route::get('/{id}/dokumente', 'Klassenbuch\KlassenbuchController@dokumente');
    Route::get('/{id}/lehrplan', 'Klassenbuch\KlassenbuchController@lehrplan');

    Route::get('/{id}/aemter', 'Klassenbuch\KlassenbuchController@aemter');

    Route::prefix('/{id}/noten')->group(function () {

        Route::get('/', 'Klassenbuch\NotenController@index');
        Route::post('/exam/add', 'Klassenbuch\NotenController@addExam');
        Route::get('/exam/edit/{examId}', 'Klassenbuch\NotenController@editExam');
        Route::post('/exam/update/{examId}', 'Klassenbuch\NotenController@updateExam');
        Route::post('/exam/editMeta', 'Klassenbuch\NotenController@editMeta');
    });

    Route::get('/{id}/serienbriefe', 'Klassenbuch\KlassenbuchController@serienbriefe');


    Route::prefix('/{id}/ajax')->group(function () {
        Route::post('/save', 'Klassenbuch\KlassenbuchController@saveLesson');
        Route::post('/info', 'Klassenbuch\KlassenbuchController@infoLesson');
        Route::get('/teilnehmer', 'Klassenbuch\KlassenbuchController@teilnehmerStunde');
        Route::get('/dokumente', 'Klassenbuch\KlassenbuchController@dokumenteStunde');
    });
});

Route::prefix('praxis')->group(function () {
    // Excel
    Route::get('/sections/{id}/{id2}/export', 'Praxis\PraxisController@export');
    Route::get('/sections/{id}/export', 'Praxis\PraxisController@exportComplete');

    //
    Route::get('/sections', 'Praxis\PraxisController@index');
    Route::get('/sections/{id}', 'Praxis\PraxisController@selectKlasse');
    Route::post('/sections/{id}', 'Praxis\PraxisController@createAbschnitt');
    Route::get('/sections/{id}/{id2}', 'Praxis\PraxisController@selectKlasse');
    Route::post('/sections/{id}/{id2}', 'Praxis\PraxisController@updateAbschnitt');
    Route::post('/sections/{id}/{id2}/saveTheorie', 'Praxis\PraxisController@saveTheorie');

    Route::get('/overview/praxis', 'Praxis\PraxisController@praxisOverview');
    Route::get('/overview/theorie', 'Praxis\PraxisController@theorieOverview');


    // Praxis API
    Route::post('/{id}/{id2}/ajax/dataset', 'Praxis\PraxisController@dataset');
    Route::post('/{id}/{id2}/ajax/praxiseinsatz/info', 'Praxis\PraxisController@infoPraxisEinsatz');
    Route::post('/{id}/{id2}/ajax/praxiseinsatz/update', 'Praxis\PraxisController@updatePraxisEinsatz');
    Route::post('/{id}/{id2}/ajax/praxiseinsatz/create', 'Praxis\PraxisController@createPraxisEinsatz');
    Route::post('/{id}/{id2}/ajax/praxiseinsatz/remove', 'Praxis\PraxisController@removePraxisEinsatz');
    Route::post('/{id}/{id2}/ajax/praxiseinsatz/move', 'Praxis\PraxisController@movePraxisEinsatz');
    Route::get('/{id}/{id2}/ajax/praxiseinsatz/besuche', 'Praxis\PraxisController@besuchePraxisEinsatz');

    Route::post('/{id}/{id2}/ajax/praxisbesuch/info', 'Praxis\PraxisController@infoPraxisBesuch');
    Route::post('/{id}/{id2}/ajax/praxisbesuch/create', 'Praxis\PraxisController@createPraxisBesuch');
    Route::post('/{id}/{id2}/ajax/praxisbesuch/update', 'Praxis\PraxisController@updatePraxisBesuch');
    Route::post('/{id}/{id2}/ajax/praxisbesuch/delete', 'Praxis\PraxisController@deletePraxisBesuch');
});

Route::prefix('nachrichten')->group(function () {
    Route::get('/', 'Nachrichten\NachrichtenController@index');
});

Route::prefix('search')->group(function () {
    Route::any('/', 'Search\SearchController@index');
});

// Dokument API
Route::prefix('dokument')->group(function () {
    Route::post('/upload', 'Dokument\DokumentController@createDocument');
    Route::post('/folder', 'Dokument\DokumentController@createFolder');
    Route::post('/move', 'Dokument\DokumentController@moveDocument');
    Route::get('/{id}/view', 'Dokument\DokumentController@viewDocument');
    Route::any('/{id}/download', 'Dokument\DokumentController@downloadDocument');
    Route::any('/{id}/download/{filename}', 'Dokument\DokumentController@downloadDocument');
    Route::get('/{id}/delete', 'Dokument\DokumentController@deleteDocument');

    Route::get('/office', 'Dokument\DokumentController@office');
    Route::get('/office/mailMerge', 'Dokument\DokumentController@mailMerge');
});

Route::prefix('smartDokument')->group(function () {
    Route::get('/', 'Dokument\SmartDokumentController@index');
});

// HeadSwitcher
Route::get('switchSchool', 'HeadSwitchController@switchSchule');
Route::get('switchYear', 'HeadSwitchController@switchJahr');
Route::get('switchEntwurf', 'HeadSwitchController@switchEntwurf');


// Other user types
Route::prefix('dozent')->group(function () {
    Route::get('/', 'Dozent\DozentenKlassenbuchController@index');
    Route::get('/klassenbuch', 'Dozent\DozentenKlassenbuchController@klassenbuch');
    Route::get('/klassen', 'Dozent\DozentenKlassenbuchController@klassen');
    Route::get('/klassenamt', 'Dozent\DozentenKlassenbuchController@klassenamt');
    Route::get('/klassenamt/{id}/view', 'Dozent\DozentenKlassenbuchController@klassenamt');
    Route::get('/noten', 'Dozent\DozentenKlassenbuchController@noten');
    Route::get('/noten/{id}', 'Dozent\DozentenKlassenbuchController@notenKlassen');
    Route::post('/noten/{id}/exam/add', 'Dozent\DozentenKlassenbuchController@addExam');
    Route::get('/noten/{id}/exam/edit/{examId}', 'Dozent\DozentenKlassenbuchController@editExam');
    Route::post('/noten/{id}/exam/update/{examId}', 'Dozent\DozentenKlassenbuchController@updateExam');
    Route::get('/klassen/{id}', 'Dozent\DozentenKlassenbuchController@klassen');
    Route::get('/klassen/{id}/schueler/{id2}', 'Dozent\DozentenKlassenbuchController@klassen');
    Route::post('/klassen/{id}/fehlzeit/create', 'Dozent\DozentenKlassenbuchController@createFehlzeit')->name('dozent.fehlzeit.create');;

    Route::get('/abwesenheit', 'Dozent\DozentenKlassenbuchController@abwesenheit');
    Route::post('/abwesenheit/create', 'Dozent\DozentenKlassenbuchController@createAbwesenheit');
    Route::get('/abwesenheit/delete/{id}', 'Dozent\DozentenKlassenbuchController@deleteAbwesenheit');

    Route::get('/praxis', 'Dozent\DozentenKlassenbuchController@praxis');
    Route::get('/praxis/{id}', 'Dozent\DozentenKlassenbuchController@praxis');
    Route::post('/praxis/{id}/savePraxisFormular', 'Dozent\DozentenKlassenbuchController@savePraxisFormular');
});

Route::prefix('unternehmen')->group(function () {
    Route::get('/', 'Unternehmen\UnternehmenPraktikaController@index');
    Route::get('/nachrichten', 'Unternehmen\UnternehmenPraktikaController@nachrichten');

    Route::get('/teilnehmer', 'Unternehmen\UnternehmenPraktikaController@teilnehmer');
    Route::get('/teilnehmer/{date}', 'Unternehmen\UnternehmenPraktikaController@teilnehmerdetails');
    Route::post('/teilnehmer', 'Unternehmen\UnternehmenPraktikaController@saveTeilnehmer');
    Route::get('/teilnehmerEvents', 'Unternehmen\UnternehmenPraktikaController@events');

    Route::get('/team', 'Unternehmen\UnternehmenPraktikaController@team');
    Route::get('/plan', 'Unternehmen\UnternehmenPraktikaController@plan');
    Route::get('/formular', 'Unternehmen\UnternehmenPraktikaController@formular');
    Route::get('/formular/{id}', 'Unternehmen\UnternehmenPraktikaController@formularView');
    Route::post('/formular/{id}', 'Unternehmen\UnternehmenPraktikaController@formularSave');
    Route::get('/formular/{id}/delete', 'Unternehmen\UnternehmenPraktikaController@formularDelete');
    Route::get('/dokumente', 'Unternehmen\UnternehmenPraktikaController@dokumente');

});

Route::prefix('external')->group(function () {
    // Space for external sites
    Route::prefix('/preiskalkulator')->group(function () {
        Route::get('', 'Verwaltung\Tools\PreiskalkulatorExternController@index');
        Route::get('/image', 'Verwaltung\Tools\PreiskalkulatorExternController@image');
        Route::get('/reserve', 'Verwaltung\Tools\PreiskalkulatorExternController@contact');
        Route::post('/reserve', 'Verwaltung\Tools\PreiskalkulatorExternController@sendContact');
        Route::get('/reserve2', 'Verwaltung\Tools\PreiskalkulatorExternController@final');
    });
    // Einstufungstest
    Route::prefix('/einstufungstest')->group(function () {
        Route::get('/', 'Verwaltung\Tools\EinstufungstestController@index');
        Route::get('/execute/{id}', 'Verwaltung\Tools\EinstufungstestController@execute');
        Route::post('/execute/{id}', 'Verwaltung\Tools\EinstufungstestController@executeCorrect');
        Route::get('/done', 'Verwaltung\Tools\EinstufungstestController@done');
    });
});

Route::prefix('sso')->group(function () {
    Route::get('/dozent', 'API\SSOProvider@dozenten');
});

// Ressourcen-Planer
Route::prefix('devices')->group(function () {
    Route::get('/', 'Devices\DeviceController@index');
    Route::get('', 'Devices\DeviceController@index');
    Route::get('/ressource/view/{id}', 'Devices\DeviceController@viewRessource');
    Route::post('/ressource/view/{id}/save', 'Devices\DeviceController@updateRessource');
    Route::get('/ressource/add', 'Devices\DeviceController@addRessource');
    Route::get('/ressource/delete/{id}', 'Devices\DeviceController@deleteRessource');
    Route::post('/ressource/add', 'Devices\DeviceController@addRessource2');
    Route::get('/ressource/all', 'Devices\DeviceController@allRessource');

    Route::get('/ausleihe1', 'Devices\DeviceController@ausleihestep1');
    Route::post('/ausleihe2', 'Devices\DeviceController@ausleihestep2');
    Route::post('/ausleihe3', 'Devices\DeviceController@ausleihestep3');

    Route::get('/reservation/delete/{id}', 'Devices\DeviceController@deleteReservation');
    // Ressourcen blocken
    Route::post('/ressource/block', 'Devices\DeviceController@blockRessource');
});


Route::prefix('help')->group(function () {
    Route::get('/', 'Help\HelpController@index');
    Route::post('/', 'Help\HelpController@search');
    Route::post('/add', 'Help\HelpController@add');
    Route::get('/view/{id}', 'Help\HelpController@view');
    Route::get('/edit/{id}', 'Help\HelpController@edit');
    Route::post('/edit/{id}', 'Help\HelpController@update');
});

Route::prefix('settings')->group(function () {
    Route::get('/', 'Settings\SettingsController@index');
    Route::get('/{settingsName}', 'Settings\SettingsController@settings');
    Route::post('/settings', 'Settings\SettingsController@saveGeneralSettings');
    Route::post('/{settingsName}', 'Settings\SettingsController@saveSettings');
});

Route::prefix('lms')->group(function () {
    Route::get('/', 'LMS\LMSController@index');
    Route::get('/viewer', 'LMS\LMSController@viewer');
    Route::get('/viewer/next', 'LMS\LMSController@next');
    Route::get('/viewer/back', 'LMS\LMSController@back');
    Route::get('/editor', 'LMS\LMSController@editor');
    Route::get('/link', 'LMS\LMSController@link');
    Route::get('/generate', 'LMS\LMSController@generateLink');
    Route::get('/secure', 'LMS\LMSController@openprotectedCourse');

});

Route::prefix('formular')->group(function () {
    Route::get('/{id}/view', 'Formular\ManageController@view');
    Route::get('/{id}/delete', 'Formular\ManageController@formularDelete');
});

Route::prefix('cloud')->group(function () {
    Route::get('/', 'Cloud\CloudController@index');
    Route::get('/general', 'Cloud\CloudController@general');
    Route::get('/logoutSecond', 'Cloud\CloudController@logoutSecond');
    Route::get('/user', 'Cloud\CloudController@userReact');
    Route::get('/employee', 'Cloud\CloudController@employee');
    Route::get('/employee/ajax', 'Cloud\CloudController@employeeAjax');
    Route::post('/user/addUser', 'Cloud\CloudController@addUser');
    Route::get('/user/{id}/edit', 'Cloud\CloudController@userEdit');
    Route::post('/user/{id}/edit', 'Cloud\CloudController@saveUserEdit');
    Route::get('/user/{id}/switch', 'Cloud\CloudController@userSwitch');
    Route::get('/user/{id}/links', 'Cloud\CloudController@userLinks');
    Route::get('/user/{id}/delete', 'Cloud\CloudController@userDelete');
    Route::get('/rights', 'Cloud\CloudController@rights');
    Route::post('/rights/addRole', 'Cloud\CloudController@addRole');
    Route::post('/rights', 'Cloud\CloudController@saveRights');
    Route::get('/groups', 'Cloud\CloudController@groups');
    Route::get('/groups/{id}/unarchive', 'Cloud\CloudController@unarchiveGroup');
    Route::get('/analytics', 'Cloud\CloudController@analytics');
    Route::post('/analytics', 'Cloud\CloudController@saveAnalytics');
    Route::get('/tenants', 'Cloud\CloudController@tenants');
    Route::post('/tenants/addTenant', 'Cloud\CloudController@addTenants');
    Route::get('/tenants/{tenant_id}/edit', 'Cloud\CloudController@editTenant');
    Route::post('/tenants/{tenant_id}/edit', 'Cloud\CloudController@saveTenant');
});

Route::prefix('analytics')->group(function () {
    Route::get('/', function () {
        return redirect('/analytics/report');
    });
    Route::prefix('report')->group(function () {
        Route::get('/', 'Analytics\ReportController@index');
        Route::get('/{report_id}', 'Analytics\ReportController@showReport');
        Route::post('/{report_id}', 'Analytics\ReportController@showReportsWithParam');
        Route::prefix('ajax')->group(function () {
            Route::get('/{report}', 'Analytics\ReportController@reportQuery');
            Route::get('/{report}/excel', 'Analytics\ReportController@reportExcel');
        });
    });
});

Route::prefix('tasks')->group(function () {
    Route::get('/', 'Aufgaben\AufgabenController@index');
    Route::get('/create', 'Aufgaben\AufgabenController@create');
    Route::post('/create', 'Aufgaben\AufgabenController@doCreate');
    Route::get('/wait', 'Aufgaben\AufgabenController@wait');
    Route::get('/close', 'Aufgaben\AufgabenController@close');
    Route::get('/detail/{id}', 'Aufgaben\AufgabenController@detail');
    Route::post('/detail/{id}', 'Aufgaben\AufgabenController@detailUpdate');
    Route::get('/detail/{id}/files', 'Aufgaben\AufgabenController@files');

    // Hand in and out stuff
    Route::get('/detail/{id}/handIn', 'Aufgaben\AufgabenController@handIn');
    Route::post('/detail/{id}/handIn', 'Aufgaben\AufgabenController@handInSave');
    Route::get('/detail/{id}/handInAll', 'Aufgaben\AufgabenController@handInAll');
    Route::get('/detail/{id}/handInAll/{id2}/rating', 'Aufgaben\AufgabenController@rating');
    Route::post('/detail/{id}/handInAll/{id2}/rating', 'Aufgaben\AufgabenController@ratingSave');
});


Route::prefix('meeting')->group(function () {
    Route::get('/join', 'Meeting\MeetingController@join');
    Route::post('/join', 'Meeting\MeetingController@join2');
});

// Legacy switcher
Route::prefix('appswitcher')->group(function () {
    Route::get('/switch/{appName}', 'AppSwitcher@switchApp');
});
// Sprache wechseln
Route::get('/locale', 'LocalController@changeLocale');

// Legacy login
Route::prefix('/api/v1')->middleware("throttle:60,1")->group(function () {
    Route::post('/code/createAccount', 'API\V1\CodeController@createAccountWithCode');
    Route::post('/loginReact', 'Auth\ReactLoginController@login');
    Route::post('/registerReact', 'Auth\ReactLoginController@registerReact');
    Route::post('/logoutReact', 'Auth\ReactLoginController@logout');
    Route::post('/refreshReact', 'Auth\ReactLoginController@refresh');
    Route::post('/derive', 'Auth\ReactLoginController@derive');

    Route::get('/administration/me', 'API\V1\Administration\AdminstrationMeController@me');
});
