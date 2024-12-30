<?php

use App\Dokument;
use App\Http\Controllers\API\V1\Cmi5\Controller\Cmi5Controller;
use App\Http\Controllers\API\V1\Cmi5\LRS\LRSController;
use App\Http\Controllers\API\V1\EducaMasterdataController;
use App\Http\Controllers\API\V1\LearnContent\InteractiveCourseBadgeController;
use App\Http\Controllers\API\V1\LearnContent\InteractiveCourseExecutionController;
use App\Http\Controllers\API\V1\SCORM\Controllers\ScormController;
use App\Http\Controllers\API\V1\SCORM\ScormTrackController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Recovery Options
Route::prefix('/v1/login/recovery')->group(function () {
    Route::post('/', 'API\V1\RecoveryController@getOptions');
    Route::post('/sendMail', 'API\V1\RecoveryController@sendMail');
    Route::post('/execute', 'API\V1\RecoveryController@executeOption');
    Route::post('/resetPassword', 'API\V1\RecoveryController@resetPassword');
});

// search

Route::any('/search', 'API\ApiSearchController@search');


Route::prefix('unterricht')->group(function () {
    Route::get('/', 'API\UnterrichtController@getUnterricht');
    Route::get('/external_booking', 'API\UnterrichtController@getExternalUnterricht');
});


Route::prefix('stammdaten')->group(function () {
    Route::get('/schule', 'API\Stammdaten\SPlusApiStammdatenController@getSchools');
    Route::get('/schule/{school_id}/klassen', 'API\Stammdaten\SPlusApiStammdatenController@getKlassenForCurrentSchoolYear');
    Route::get('/schule/{school_id}/klassen/{id2}/dozenten', 'API\Stammdaten\SPlusApiStammdatenController@getDozentenForKlasse');
    Route::get('/schule/{school_id}/dozenten', 'API\Stammdaten\SPlusApiStammdatenController@getDozenten');
    Route::get('/schule/{school_id}/schueler', 'API\Stammdaten\SPlusApiStammdatenController@getSchuler');
    Route::post('/schule/{school_id}/schueler/create', 'API\Stammdaten\SPlusApiStammdatenController@createSchuler');
    Route::get('/schule/{school_id}/schueler/{schuler_id}', 'API\Stammdaten\SPlusApiStammdatenController@getSingleSchuler');
    Route::post('/schule/{school_id}/schueler/{schuler_id}/dokument/create', 'API\Stammdaten\SPlusApiStammdatenController@createSchulerDokument');
    Route::get('/schule/{school_id}/raeume', 'API\Stammdaten\SPlusApiStammdatenController@getRaume');

    // Kontakte
    Route::get('/kontakte', 'API\Stammdaten\SPlusApiStammdatenController@getKontakte');
});

Route::prefix('search')->group(function () {
    Route::get('/classes', 'API\ApiSearchController@class');
    Route::get('/clouduser', 'API\ApiSearchController@clouduser');
    Route::get('/group', 'API\ApiSearchController@group');
    Route::get('/tags', 'API\ApiSearchController@tags');
    Route::get('/companies', 'API\ApiSearchController@companies');
});

// F+U API
Route::prefix('schueler')->group(function () {
    Route::post('/create', 'API\Stammdaten\SPlusApiStammdatenController@createSchuler');
    Route::post('/{id}/dokument/create', 'API\Stammdaten\SPlusApiStammdatenController@createSchulerDokument');
});

Route::prefix('kontakte')->group(function () {
    Route::post('/create', 'API\Stammdaten\SPlusApiStammdatenController@createKontakt');
    Route::post('/{id}/update', 'API\Stammdaten\SPlusApiStammdatenController@updateKontakt');
});

Route::prefix('image')->group(function () {
    Route::get('/cloud', 'API\V1\ImageProviderEduca@cloud');
    Route::get('/rocketchat', 'API\V1\ImageProviderEduca@rocketchat');
    Route::get('/group', 'API\V1\ImageProviderEduca@getGroupImage');
    Route::get('/section', 'API\V1\ImageProviderEduca@getSectionImage');
    Route::get('/interactive_course', 'API\V1\ImageProviderEduca@getInteractiveCourseImage');
    Route::get('/category', 'API\V1\ImageProviderEduca@getLearnContentCategoryImage');
    Route::get('/badge', 'API\V1\ImageProviderEduca@getInteractiveCourseBadgeImage');
    Route::get('/schuler', 'API\ImageProvider@getUserImage');
    Route::get('/klasse', 'API\ImageProvider@getKlasseImage');
    Route::get('/learnContent', 'API\V1\ImageProviderEduca@getLearnContentImage');
});

Route::prefix('praxisbesuche')->group(function () {
    Route::get('/', 'API\PraxisBesucheController@getPraxisbesuche');
});

Route::prefix('kalender')->group(function () {
    Route::get('/', 'API\FerienKalenderController@getFerien');
});

Route::prefix('calendar')->group(function () {
    Route::any('/', 'API\EventController@events');
    Route::any('/details/', 'API\EventController@eventDetails');
    Route::get('/update/', 'API\EventController@eventDetails');
});

Route::prefix('report')->group(function () {
    Route::get('/', 'API\ReportAPIController@query');
});

Route::prefix('/v1/embedded')->group(function () {
    Route::prefix('learnContent')->group(function () {
        Route::get('{learnContentId}', 'API\V1\LearnContent\LearnContentController@get');
    });
});

Route::prefix('/v1/scorm')->group(function () {
    Route::get('/play/{uuid}', [ScormController::class, "show"]);
});

Route::prefix('dokument')->group(function () {
    Route::post('/{id}', function (Request $request, $id) {
        $dokument = Dokument::findOrFail($id);
        $status = $request->input("status");
        if ($status == 2) {
            $content = file_get_contents($request->input("url"));
            Storage::put($dokument->disk_name, $content);
        }
        return response()->json(["error" => 0]);
    });
});

// Routes without token
Route::prefix('/v1/push')->group(function () {
    Route::post('/rocketchat', 'API\V1\Push\PushNotificationsController@rocketChatHook');
});
Route::post('/v1/login', 'API\AuthController@login');
Route::post('/v1/code/check', 'API\V1\CodeController@checkCode');
Route::prefix('/v1/tenants')->group(function () {
    Route::get('/', [EducaMasterdataController::class, 'getAllTenants']);
    Route::get('/currentConfig', [EducaMasterdataController::class, 'getTenantConfig']);
});

// public calendar route
Route::get('/v1/ical', 'API\V1\EventController@eventsOutlook');

Route::prefix('/v1/enroll')->group(function () {
    Route::post('/startEnroll', [EnrollmentController::class, 'startEnrollment']);
});

// Schule Plus 4.0
/**
 *  V1 API
 */
// downloadable via token
Route::get('v1/formtemplates/open', 'API\V1\FormularTemplateController@openFormular');
Route::get('v1/locales', 'API\V1\LocaleController@getLocales');

Route::middleware([
    'middleware' => 'api_v1'])->group(function () {

    Route::prefix('v1')->group(function () {

        Route::prefix('administration')->group(function () {

            Route::get('/search', 'API\V1\Administration\AdminSearchController@search');

            Route::prefix('correspondence')->group(function () {
                Route::get('/', 'API\V1\Administration\Correspondence\CorrespondenceController@listCorrespondence');
                Route::post('/', 'API\V1\Administration\Correspondence\CorrespondenceController@createCorrespondence');
                Route::post('/multiCreate', 'API\V1\Administration\Correspondence\CorrespondenceController@createMultiCorespondeMulti');
                Route::post('/{correspondence_id}/update', 'API\V1\Administration\Correspondence\CorrespondenceController@updateCorrespondence');
                Route::post('/{correspondence_id}/delete', 'API\V1\Administration\Correspondence\CorrespondenceController@deleteCorrespondence');
            });

            Route::prefix('masterdata')->group(function () {
                Route::get('/schools', 'API\V1\Administration\Masterdata\MasterdataController@schools');
                Route::get('/schools/{school_id}/schoolyears', 'API\V1\Administration\Masterdata\MasterdataController@schoolyears');
                Route::get('/schools/{school_id}/schoolyears/{year_id}/courses', 'API\V1\Administration\Masterdata\MasterdataController@courses');
                Route::get('/schools/{school_id}/teachers', 'API\V1\Administration\Masterdata\MasterdataController@teacher');
                Route::get('/schools/{school_id}/rooms', 'API\V1\Administration\Masterdata\MasterdataController@rooms');
                Route::get('/schools/{school_id}/studies', 'API\V1\Administration\Masterdata\MasterdataController@studium');
                Route::get('/schools/{school_id}/employees', 'API\V1\Administration\Masterdata\MasterdataEmployeeController@employees');


                //Students
                Route::get('/schools/{school_id}/students', 'API\V1\Administration\Masterdata\MasterdataStudentController@students');

                Route::prefix('students')->group(function () {


                    Route::prefix('/modale')->group(function () {
                        Route::post('matriculationMultiple', 'API\V1\Administration\Masterdata\MasterdataStudentController@matriculationMultiple');
                    });

                    //Stapelverarbeitung
                    Route::post('/stack/addToClass', 'API\V1\Administration\Masterdata\MasterdataStudentController@addToClass');
                    Route::post('/stack/exmatriculate/regular', 'API\V1\Administration\Masterdata\MasterdataStudentController@exmatriculateRegularStapel');
                    Route::post('/stack/exmatriculate/guest', 'API\V1\Administration\Masterdata\MasterdataStudentController@exmatriculateRegularGuest');

                    Route::get('/{student_id}/studentDetailed', 'API\V1\Administration\Masterdata\MasterdataStudentController@studentDetailed');
                    Route::post('/{student_id}/update', 'API\V1\Administration\Masterdata\MasterdataStudentController@updateStudent');
                    Route::post('/{student_id}/setForm', 'API\V1\Administration\Masterdata\MasterdataStudentController@setForm');
                    Route::post('/{student_id}/uploadImage', 'API\V1\Administration\Masterdata\MasterdataStudentController@uploadProfilImage');
                    Route::post('/{student_id}/delete', 'API\V1\Administration\Masterdata\MasterdataStudentController@deleteStudent');
                    Route::post('/add', 'API\V1\Administration\Masterdata\MasterdataStudentController@addStudent');

                    // More Details
                    Route::get('/{student_id}/practialHistorie', 'API\V1\Administration\Masterdata\MasterdataStudentController@getPraxisWerdegang');
                    Route::post('/{student_id}/practialUpdate', 'API\V1\Administration\Masterdata\MasterdataStudentController@updatePraxis');
                    Route::post('/{student_id}/practialDelete', 'API\V1\Administration\Masterdata\MasterdataStudentController@deletePraxis');
                    Route::post('/{student_id}/practialUpdateEntry', 'API\V1\Administration\Masterdata\MasterdataStudentController@practialUpdateEntry');

                    Route::get('/{student_id}/schoolclasses', 'API\V1\Administration\Masterdata\MasterdataStudentController@getPlannungsGruppen');
                    Route::post('/{student_id}/schoolclasses/assign', 'API\V1\Administration\Masterdata\MasterdataStudentController@assignPlannungsGruppen');
                    Route::post('/{student_id}/schoolclasses/remove', 'API\V1\Administration\Masterdata\MasterdataStudentController@removePlannungsGruppen');
                    Route::post('/{student_id}/schoolclasses/edit', 'API\V1\Administration\Masterdata\MasterdataStudentController@editPlannungsGruppen');

                    Route::get('/{student_id}/studyplan', 'API\V1\Administration\Masterdata\MasterdataStudentController@getStudienverlauf');
                    Route::post('/{student_id}/studyplan/{entry_id}/update', 'API\V1\Administration\Masterdata\MasterdataStudentController@updateStudienverlaufEintrag');
                    Route::post('/{student_id}/studyplan/{entry_id}/delete', 'API\V1\Administration\Masterdata\MasterdataStudentController@deleteStudienverlaufEintrag');

                    Route::get('/{student_id}/curriculum', 'API\V1\Administration\Masterdata\MasterdataStudentController@getCurriculum');

                    Route::get('/{student_id}/presences', 'API\V1\Administration\Masterdata\MasterdataStudentController@presences');

                    Route::get('/{student_id}/grades/overview', 'API\V1\Administration\Masterdata\MasterdataStudentController@gradesOverview');
                    Route::get('/{student_id}/grades', 'API\V1\Administration\Masterdata\MasterdataStudentController@getGradesForStudent');
                    Route::post('/{student_id}/grades', 'API\V1\Administration\Masterdata\MasterdataStudentController@setGradeForStudent');
                    Route::post('/{student_id}/grades/{grade_id}/delete', 'API\V1\Administration\Masterdata\MasterdataStudentController@deleteGradeForStudent');
                    Route::post('/{student_id}/grades/{grade_id}/update', 'API\V1\Administration\Masterdata\MasterdataStudentController@updateGradeForStudent');

                    Route::get('/{student_id}/examDates', 'API\V1\Administration\Masterdata\MasterdataStudentController@getStudentExamDates');

                    Route::post('/{student_id}/reports', 'API\V1\Administration\Masterdata\MasterdataStudentController@reports');


                    Route::prefix('/{student_id}/modale')->group(function () {
                        Route::get('modalInformation', 'API\V1\Administration\Masterdata\MasterdataStudentController@modalInformation');
                        Route::get('downgrading', 'API\V1\Administration\Masterdata\MasterdataStudentController@downgradingInformation');
                        Route::get('changeStudy', 'API\V1\Administration\Masterdata\MasterdataStudentController@changeStudyInformation');
                        Route::post('matriculation', 'API\V1\Administration\Masterdata\MasterdataStudentController@immatrikulation');
                        Route::post('revocation', 'API\V1\Administration\Masterdata\MasterdataStudentController@widerruf');
                        Route::post('changeStudyDays', 'API\V1\Administration\Masterdata\MasterdataStudentController@changeStudientage');
                        Route::post('downgrading', 'API\V1\Administration\Masterdata\MasterdataStudentController@downgrading');
                        Route::post('vacation', 'API\V1\Administration\Masterdata\MasterdataStudentController@vacation');
                        Route::post('changePlaceOfStudy', 'API\V1\Administration\Masterdata\MasterdataStudentController@changeStudienort');
                        Route::post('changeStudy', 'API\V1\Administration\Masterdata\MasterdataStudentController@changeStudiengang');
                        Route::post('changeStudyDirection', 'API\V1\Administration\Masterdata\MasterdataStudentController@changeStudyDirection');
                        Route::post('extendStudy', 'API\V1\Administration\Masterdata\MasterdataStudentController@extendStudiengang');
                        Route::post('cancelStudy', 'API\V1\Administration\Masterdata\MasterdataStudentController@cancelStudiengang');
                        Route::post('finishStudy', 'API\V1\Administration\Masterdata\MasterdataStudentController@finishStudium');
                        Route::post('changeFromGuestStudent', 'API\V1\Administration\Masterdata\MasterdataStudentController@changeFromGastStudent');
                        Route::post('immaAsGuestStudent', 'API\V1\Administration\Masterdata\MasterdataStudentController@immaAsGuestStudent');
                        Route::post('manualEntry', 'API\V1\Administration\Masterdata\MasterdataStudentController@manualEntry');
                    });
                });

                //Study
                Route::prefix('study')->group(function () {
                    Route::post('{study_id}/allocateSubjects', 'API\V1\Administration\Curricula\SubjectController@attachSubjectsToStudy');
                    Route::post('{study_id}/allocateModules', 'API\V1\Administration\Curricula\ModuleController@attachModulsToStudy');
                });


                //Contacts
                Route::get('/schools/{school_id}/contacts', 'API\V1\Administration\Masterdata\MasterdataContactController@listContacts');

                Route::prefix('contacts')->group(function () {
                    Route::post('/{contact_id}/update', 'API\V1\Administration\Masterdata\MasterdataContactController@updateContact');
                    Route::post('/add', 'API\V1\Administration\Masterdata\MasterdataContactController@createContact');
                    Route::get('/{contact_id}', 'API\V1\Administration\Masterdata\MasterdataContactController@contactDetails');
                    Route::post('/{contact_id}/delete', 'API\V1\Administration\Masterdata\MasterdataContactController@deleteContact');
                    Route::get('/{contact_id}/students', 'API\V1\Administration\Masterdata\MasterdataContactController@contactStudents');
                    Route::get('/{contact_id}/relationships', 'API\V1\Administration\Masterdata\MasterdataContactController@relationships');
                    Route::post('/{contact_id}/relationships', 'API\V1\Administration\Masterdata\MasterdataContactController@addRelationships');
                    Route::post('/{contact_id}/relationships/{relationship_id}/delete', 'API\V1\Administration\Masterdata\MasterdataContactController@deleteRelationships');
                });

                // Planingsgroups
                Route::prefix('courses')->group(function () {
                    Route::post("/add", "API\V1\Administration\Masterdata\MasterdataCourseController@add");
                    Route::get("/{course_id}/details", "API\V1\Administration\Masterdata\MasterdataCourseController@details");
                    Route::get("/{course_id}/availableStudents", "API\V1\Administration\Masterdata\MasterdataCourseController@avaiableStudentsWithOpenStuff");
                    Route::post("/{course_id}/update", "API\V1\Administration\Masterdata\MasterdataCourseController@update");
                    Route::post("/{course_id}/delete", "API\V1\Administration\Masterdata\MasterdataCourseController@delete");
                    Route::post("/{course_id}/deleteMembers", "API\V1\Administration\Masterdata\MasterdataCourseController@deleteMembers");
                });

                // Mitarbeiter
                Route::prefix('employee')->group(function () {
                    Route::post('/add', 'API\V1\Administration\Masterdata\MasterdataEmployeeController@createEmployee');
                    Route::get('/{employee_id}/details', 'API\V1\Administration\Masterdata\MasterdataEmployeeController@employeeDetailed');
                    Route::post('/{employee_id}/update', 'API\V1\Administration\Masterdata\MasterdataEmployeeController@updateEmpolyee');
                    Route::post('/{employee_id}/delete', 'API\V1\Administration\Masterdata\MasterdataEmployeeController@deleteEmployee');
                });

                //Teachers
                Route::prefix('teachers')->group(function () {
                    Route::get('/{teacher_id}/teacherDetailed', 'API\V1\Administration\Masterdata\MasterdataTeacherController@teacherDetailed');
                    Route::post('/{teacher_id}/update', 'API\V1\Administration\Masterdata\MasterdataTeacherController@updateTeacher');
                    Route::post('/{teacher_id}/add', 'API\V1\Administration\Masterdata\MasterdataTeacherController@addTeacher');
                    Route::post('/{teacher_id}/delete', 'API\V1\Administration\Masterdata\MasterdataTeacherController@deleteTeacher');
                    Route::post('/{teacher_id}/updateSettings', 'API\V1\Administration\Masterdata\MasterdataTeacherController@updateSettings');
                    Route::get('/{teacher_id}/teachingStakes', 'API\V1\Administration\Masterdata\MasterdataTeacherController@teachingStakes');
                    Route::post('/{teacher_id}/setForm', 'API\V1\Administration\Masterdata\MasterdataTeacherController@setForm');
                });

                Route::get('/supporttable', 'API\V1\Administration\Masterdata\MasterdataController@getSupportTable');

                Route::get('/users', 'API\V1\Administration\Masterdata\MasterdataController@users');
                Route::get('/users/{id}/jwt', 'API\V1\Administration\Masterdata\MasterdataController@usersJWT');
                Route::post('/users/switchBackUser', 'API\V1\Administration\Masterdata\MasterdataController@switchBackUser');

                Route::prefix('qualifications')->group(function () {
                    Route::post("/add", "API\V1\Administration\Masterdata\QualificationController@createQualification");
                    Route::get("/{model_type}/{model_id}", "API\V1\Administration\Masterdata\QualificationController@getQualifications");
                    Route::post("/{qualification_id}/update", "API\V1\Administration\Masterdata\QualificationController@updateQualification");
                    Route::post("/{qualification_id}/delete", "API\V1\Administration\Masterdata\QualificationController@deleteQualification");
                });
            });


            Route::prefix('boards')->group(function () {
                Route::get('/{boardId}', 'API\V1\Administration\BoardController@getBoardInfo');
                Route::post('/{boardId}', 'API\V1\Administration\BoardController@updateBoard');
                Route::post('/{boardId}/delete', 'API\V1\Administration\BoardController@deleteBoard');
                Route::post('/{boardId}/order', 'API\V1\Administration\BoardController@updateWidgetOrder');
                Route::post('/{boardId}/addWidget', 'API\V1\Administration\BoardController@addWidget');
                Route::post('/{boardId}/removeWidget', 'API\V1\Administration\BoardController@deleteWidget');
            });

            Route::prefix('widgets')->group(function () {
                Route::get('/', 'API\V1\Administration\BoardController@availableWidgets');
                Route::post('/updateSettings', 'API\V1\Administration\BoardController@updateWidgetSettings');

                // Widgets
                // Aktivitaeten Widget
                Route::get('/activities', 'API\V1\Administration\Widgets\ActivitiesWidget@aktivitaeten');

                // Info Widget
                Route::get('/info', 'API\V1\Administration\Widgets\InfoWidget@info');

                // Teilnehmer Widget
                Route::get('/attendees', 'API\V1\Administration\Widgets\AttendeesWidget@loadAttendee');

                // Kontakt Widget
                Route::get('/contacts', 'API\V1\Administration\Widgets\ContactsWidget@kontakte');

                // Raumauslastung Widget
                Route::get('/roomOccupancy', 'API\V1\Administration\Widgets\RoomOccupancyWidget@occupancy');

                // Soll-Ist
                Route::prefix('balance')->group(function () {
                    Route::get('/teacher', 'API\V1\Administration\Widgets\BalanceWidget@teacher');
                    Route::get('/course', 'API\V1\Administration\Widgets\BalanceWidget@course');
                });

                // Krankmeldungen

                Route::prefix('absence')->group(function () {
                    Route::get('/', "API\V1\Administration\Widgets\SickReportsWidget@sickReports");
                    Route::get('/approveAbsence/{id}', "API\V1\Administration\Widgets\SickReportsWidget@approveAbsence")->name("absence.approve");
                });

                // Lernfortschritt
                Route::prefix('progress')->group(function () {
                    // Lernfortschritt der Klasse
                    Route::get('/course', 'API\V1\Administration\Widgets\LearnProgressWidget@course');
                    // Lernfortschritt der Schüler
                    Route::get('/student', 'API\V1\Administration\Widgets\LearnProgressWidget@student');
                });

                // Aufgaben Widget
                Route::prefix('tasks')->group(function () {
                    Route::get('/my', 'API\V1\Administration\Widgets\MyTasksWidget@taskList');
                    Route::get('/open', 'API\V1\Administration\Widgets\OpenTaskWidget@taskList');
                    Route::post('/task/{id}/status', 'API\V1\Administration\Widgets\OpenTaskWidget@updateTaskStatus');
                });


                Route::prefix('planning')->group(function () {
                    Route::get('/shortcuts', 'API\V1\Administration\Widgets\BalanceCurriculaWidget@shortCuts');
                    Route::get('/balance', 'API\V1\Administration\Widgets\BalanceCurriculaWidget@sollist');
                    Route::get('/teacher', 'API\V1\Administration\Widgets\TeacherCurriculaWidget@dozent');
                    Route::get('/teacherSubject', 'API\V1\Administration\Widgets\TeacherCurriculaWidget@dozentFach');
                    Route::get('/schoolclass', 'API\V1\Administration\Widgets\SchoolclassCurriculaWidget@schoolclass');
                });
            });

            Route::prefix('timetable')->group(function () {
                Route::get('/timeframe', 'API\V1\Administration\Timetable\PlanningController@getTimeFrameForPlanning');

                Route::prefix('teaching')->group(function () {
                    Route::get('/', 'API\V1\Administration\Timetable\TeachingController@generateTimetable');
                    Route::get('/teachers', 'API\V1\Administration\Timetable\TeachingController@teachers');
                    Route::get('/subjects', 'API\V1\Administration\Timetable\TeachingController@subjects');
                    Route::get('/devices', 'API\V1\Administration\Timetable\TeachingController@devices');
                    Route::get('/rooms', 'API\V1\Administration\Timetable\TeachingController@rooms');
                });

                Route::prefix('lessonplan')->group(function () {
                    Route::post('/create', 'API\V1\Administration\Timetable\TimetableController@createLessonPlan');
                    Route::get('/{id}', 'API\V1\Administration\Timetable\TimetableController@infoLessonPlan');
                    Route::post('/{id}/move', 'API\V1\Administration\Timetable\TimetableController@moveLessonPlan');
                    Route::post('/{id}/resourceMove', 'API\V1\Administration\Timetable\TimetableController@resourceMoveLessonPlan');
                    Route::post('/{id}/update', 'API\V1\Administration\Timetable\TimetableController@updateLessonPlan');
                    Route::post('/{id}/delete', 'API\V1\Administration\Timetable\TimetableController@deleteLessonPlan');
                    Route::get('/{id}/students', 'API\V1\Administration\Timetable\TimetableController@getLessonPlanStudents');
                    Route::post('/{id}/students', 'API\V1\Administration\Timetable\TimetableController@saveLessonPlanStudents');
                    Route::post('/{id}/endSeries', 'API\V1\Administration\Timetable\TimetableController@endSeriesLessonPlan');
                });

                Route::prefix('lesson')->group(function () {
                    Route::post('/create', 'API\V1\Administration\Timetable\TimetableController@createLesson');
                    Route::get('/{id}', 'API\V1\Administration\Timetable\TimetableController@infoLesson');
                    Route::post('/{id}/update', 'API\V1\Administration\Timetable\TimetableController@updateLesson');
                    Route::post('/{id}/delete', 'API\V1\Administration\Timetable\TimetableController@deleteLesson');
                });

                Route::prefix('helper')->group(function () {
                    Route::post('/copyToNextWeek', 'API\V1\Administration\Timetable\PlanningController@copyToNextWeek');
                    Route::post('/copyToNextDay', 'API\V1\Administration\Timetable\PlanningController@copyToNextDay');

                    Route::post('/copyWeek', 'API\V1\Administration\Timetable\PlanningController@copyWeek');
                    Route::post('/deleteWeek', 'API\V1\Administration\Timetable\PlanningController@deleteWeek');
                    Route::post('/copySingleEvent', 'API\V1\Administration\Timetable\PlanningController@copySingleEvent');
                    Route::post('/copyToClass', 'API\V1\Administration\Timetable\PlanningController@copyToClass');
                    Route::post('/excelImport', 'API\V1\Administration\Timetable\TimetableController@uploadExcel');
                });
            });

            Route::prefix('sections')->group(function () {
                Route::get('/course/{course_id}', 'API\V1\Administration\Sections\SectionController@sectionForCourse');
                Route::prefix('pratical')->group(function () {
                    Route::get('dataset', 'API\V1\Administration\Sections\PraxisSectionController@dataset');
                });
            });

            Route::prefix('classbook')->group(function () {
                Route::prefix('/teaching')->group(function () {
                    Route::get('/entries', 'API\V1\Administration\Classbook\TeachingController@getEntriesByDate');
                    Route::get('/missingEntries', 'API\V1\Administration\Classbook\TeachingController@klassenbuchMissingEntries');
                    Route::get('/info', 'API\V1\Administration\Classbook\TeachingController@klassenbuchEntry');
                    Route::get('/members', 'API\V1\Administration\Classbook\TeachingController@memberList');
                    Route::post('/save', 'API\V1\Administration\Classbook\TeachingController@saveKlassenbuchEntry');
                });

                Route::get('/marks', 'API\V1\Classbook\MarksController@markForStudent');
                Route::get('/absenteeism', 'API\V1\Administration\Widgets\AbsenteeismWidget@getAbsenteeism');

                Route::prefix('/students')->group(function () {
                    Route::get('/list', 'API\V1\Administration\Classbook\TeachingController@klassenbuchEntry');
                });
            });


            Route::prefix('forms')->group(function () {
                Route::get('/{form_id}', 'API\V1\Administration\FormsController@getRevision');
                Route::post('/{form_id}/revision/{revision_id}', 'API\V1\Administration\FormsController@saveFilledRevision');
            });

            Route::prefix('grades')->group(function () {
                Route::post('/', 'API\V1\Administration\NoteController@setGrades');
                Route::post('/update', 'API\V1\Administration\NoteController@updateGrades');
                Route::post('/{grade_id}/update', 'API\V1\Administration\NoteController@updateGrade');
                Route::get('/', 'API\V1\Administration\NoteController@getGrades');
            });

        });

        // Non-Administration

        Route::prefix('wiki')->group(function () {
            // Wiki
            Route::get('/', 'API\V1\Wiki\WikiController@listWiki'); // liste von Wiki Seiten
            Route::post('create', 'API\V1\Wiki\WikiController@createWiki');
            Route::post('delete', 'API\V1\Wiki\WikiController@deleteWiki');
            Route::post('update', 'API\V1\Wiki\WikiController@updateWiki');
            Route::post('uploadImage', 'API\V1\Wiki\WikiController@uploadImage');
            Route::post('search', 'API\V1\Wiki\WikiController@search');
        });


        Route::prefix('dozent')->group(function () {
            Route::get('/me', 'API\V1\Administration\Classbook\DozentenController@me'); // me-route
            Route::get('/permissions', 'API\V1\Administration\Classbook\DozentenController@canViewAllClassTimetables'); // rechte
        });

        Route::prefix('formtemplates')->group(function () {
            Route::get('/', 'API\V1\FormularTemplateController@getForms');
            Route::post('/', 'API\V1\FormularTemplateController@createFormularTemplate'); // erstellt ein FormularTemplate
            Route::get('/{id}', 'API\V1\FormularTemplateController@getFormularTemplate'); // bearbeitet ein FormularTemplate
            Route::post('/{id}', 'API\V1\FormularTemplateController@updateFormularTemplate'); // bearbeitet ein FormularTemplate
            Route::post('/{id}/delete', 'API\V1\FormularTemplateController@deleteFormularTemplate'); // löscht ein FormularTemplate
            Route::post('/{id}/print', 'API\V1\FormularTemplateController@printFormularTemplate'); // erstellt PDF
            Route::post('/{id}/generate', 'API\V1\FormularTemplateController@generateFormularTemplate'); // erstellt PDF
        });

        Route::get('/me', 'API\V1\EducaMasterdataController@getCurrentUser');
        Route::post('/me/sessionToken', 'API\V1\EducaMasterdataController@updateSessionToken');
        Route::get('/me/sessionToken', 'API\V1\EducaMasterdataController@getSessionToken');
        Route::post('/me/sessionToken/{session_id}/close', 'API\V1\EducaMasterdataController@closeSessionToken');
        Route::post('/me/pushToken/register', 'API\V1\EducaMasterdataController@registerPushToken');
        Route::post('/me/pushToken/deregister', 'API\V1\EducaMasterdataController@deregisterPushToken');
        Route::get('/me/groupSettings', 'API\V1\EducaMasterdataController@getGroupSettings');
        Route::post('/me/groupSettings', 'API\V1\EducaMasterdataController@updateSectionGroupSetting');
        Route::post('/me/groupClusters','API\V1\EducaMasterdataController@updateGroupClusterSettings');
        Route::post('me/groupClusters/favorites','API\V1\EducaMasterdataController@flipGroupClusterFavorite');
        Route::get('/qrCode', 'API\V1\QrCodeController@generate');
        Route::get('/feed', 'API\V1\FeedController@feed'); // liefert den feed
        Route::get('/feed/events', 'API\V1\EventController@eventFeed');
        Route::get('/feed/tasks', 'API\V1\TaskController@taskFeed');
        Route::get('/feed/statistics', 'API\V1\FeedController@statistics');
        Route::get('/feed/interactiveCourse', 'API\V1\EducaCourse\EducaCourseController@feed');
        Route::get('/feed/sections', 'API\V1\FeedController@sections');
        Route::get('/feed/sections/lastseen', 'API\V1\FeedController@lastSeenSections');


        Route::post('/code', 'API\V1\CodeController@executeCode');

        Route::post('/search', 'API\V1\SearchController@search');

        Route::prefix('privacy')->group(function () {
            Route::get('/', 'API\V1\PrivacyController@getPrivacyAgreement');
            Route::post('/accept', 'API\V1\PrivacyController@accept');
        });


        Route::prefix('grouptemplates')->group(function () {
            Route::get('/', 'API\V1\Groups\GroupTemplateController@list');
            Route::post('/template/{template_id}/create', 'API\V1\Groups\GroupTemplateController@createFromTemplate');
            Route::post('/template/{template_id}/delete', 'API\V1\Groups\GroupTemplateController@deleteTemplate');
            Route::post('/createTemplateFromGroup', 'API\V1\Groups\GroupTemplateController@convertGroupToTemplate');
        });

        Route::prefix('groups')->group(function () {
            Route::post('/create', 'API\V1\Groups\GroupController@createGroup'); // Gruppe erstellen
            Route::post('/{groupId}/archive', 'API\V1\Groups\GroupController@archiveGroup'); // Gruppe archivieren
            Route::post('/{groupId}/delete', 'API\V1\Groups\GroupController@deleteGroup'); // Gruppe löschen

            Route::get('/apps/all', 'API\V1\Groups\GroupController@getAllApps'); // All apps
            // Zugangscode
            Route::get('{groupId}/code', 'API\V1\Groups\CodeController@getCode'); // gibt Zugangscodes Informationen

            Route::get('/', 'API\V1\Groups\GroupController@getGroupsWithSection'); // Listet alle Gruppen auf
            Route::get('/{groupId}', 'API\V1\Groups\GroupController@getGroupInfo'); // gibt die Infos der Gruppe wieder
            Route::post('/{groupId}/section', 'API\V1\Groups\GroupController@createSection'); // erstellt eine Section
            Route::get('/{groupId}/feed', 'API\V1\Groups\GroupController@feed'); // feed
            Route::get('/{groupId}/settings', 'API\V1\Groups\GroupController@getSettings'); // get settings
            Route::post('/{groupId}/settings', 'API\V1\Groups\GroupController@putSettings'); // change settings{}
            Route::post('/{groupId}/members/add', 'API\V1\Groups\GroupController@addMembers'); // add members
            Route::post('/{groupId}/members/update', 'API\V1\Groups\GroupController@updateMember'); // update role of member
            Route::post('/{groupId}/members/remove', 'API\V1\Groups\GroupController@removeMember'); // remove one member

            // Permissions
            Route::post('/{groupId}/roles/add', 'API\V1\Groups\GroupController@addRole'); // role adden
            Route::post('/{groupId}/roles/{roleId}/update', 'API\V1\Groups\GroupController@updateRole'); // role adden
            Route::post('/{groupId}/roles/{roleId}/delete', 'API\V1\Groups\GroupController@deleteRole'); // role adden

            Route::post('/{groupId}/sections/reorder', 'API\V1\Groups\GroupController@reorderSections'); //Reorder sections

            // External Integration
            Route::get('/{groupId}/externalIntegration', 'API\V1\Groups\ExternalIntegrationController@getGroupIntegration'); // role adden
            Route::post('/{groupId}/externalIntegration/add', 'API\V1\Groups\ExternalIntegrationController@addGroupIntegration'); // role adden
            Route::post('/{groupId}/externalIntegration/{external_integration_id}/remove', 'API\V1\Groups\ExternalIntegrationController@deleteGroupIntegration'); // role adden

            Route::prefix('sections')->group(function () {

                Route::get('{sectionId}', 'API\V1\Groups\GroupController@getSectionInfo'); // gibt die Infos der Section wieder
                Route::post('{sectionId}/updateSectionImage', 'API\V1\Groups\GroupController@updateSectionImage'); // gibt die Infos der Section wieder
                Route::get('{sectionId}/apps/available', 'API\V1\Groups\GroupController@getApps'); // available apps
                Route::post('{sectionId}/apps/add', 'API\V1\Groups\GroupController@addApp'); //add one app
                Route::post('{sectionId}/apps/rename', 'API\V1\Groups\GroupController@renameApp');  //Rename one app
                Route::post('{sectionId}/apps/remove', 'API\V1\Groups\GroupController@removeApp');  //Remove one app
                Route::post('{sectionId}/update', 'API\V1\Groups\GroupController@updateSection'); //Update section (change name)
                Route::post('{sectionId}/remove', 'API\V1\Groups\GroupController@removeSection'); //Remove section
                Route::get('{sectionId}/members', 'API\V1\Groups\GroupController@membersSection'); //Remove section
                Route::get('{sectionId}/sectionEvents', 'API\V1\Groups\GroupController@getSectionEvents'); //get events of section
                Route::get('{sectionId}/sectionTasks', 'API\V1\Groups\GroupController@getSectionTasks'); //get tasks for section

                // Beitrage
                Route::get('{sectionId}/announcements', 'API\V1\Groups\AnnouncementsController@announcements');
                Route::post('{sectionId}/announcements', 'API\V1\Groups\AnnouncementsController@createAnnouncement');

                // Beitragsvorlagen
                Route::post('{sectionId}/announcementtemplates', 'API\V1\Groups\AnnouncementTemplatesController@createAnnouncementTemplate');

                // Calender
                //Route::post('{sectionId}/events', 'API\V1\Groups\EventController@events');
                Route::get('{sectionId}/events', 'API\V1\Groups\EventController@events'); // gibt die Events für eine section zurück

                // Chat Info
                Route::get('{sectionId}/chat', 'API\V1\MessagesController@getGroupChatFromSection'); // gibt Chat Informationen


                // Chat Info
                Route::get('{sectionId}/educaCourse', 'API\V1\EducaCourse\EducaCourseSectionController@getSectionInformation'); // gibt Informationen zu dem Kurs
                Route::post('{sectionId}/educaCourse/{courseId}', 'API\V1\EducaCourse\EducaCourseSectionController@linkCourse'); // gibt Informationen zu dem Kurs

                // Task
                Route::get('{sectionId}/task', 'API\V1\Groups\TaskController@getTask'); // gibt Chat Informationen

                // Meeting
                Route::get('{sectionId}/meeting', 'API\V1\Groups\SectionMeetingController@getSectionMeetingDetails');
                Route::post('{sectionId}/meeting', 'API\V1\Groups\SectionMeetingController@updateSectionMeetingDetails');

                // OpenCast
                Route::get('{sectionId}/opencast', 'API\V1\Groups\OpenCastController@getSectionInformation');
                Route::post('{sectionId}/opencast', 'API\V1\Groups\SectionMeetingController@updateSectionMeetingDetails');
            });
        });


        //Announcements
        Route::prefix("announcements")->group(function () {
            Route::get('{announcementId}/', 'API\V1\Groups\AnnouncementsController@getById');
            Route::get('{announcementId}/history', 'API\V1\Groups\AnnouncementsController@getHistoryBeitrag');
            Route::get('{sectionId}/announcementsPreview', 'API\V1\Groups\AnnouncementsController@getSectionAnnouncementsPreview');
            Route::post('{beitragId}/like', 'API\V1\Groups\AnnouncementsController@like');
            Route::post('{beitragId}/comment', 'API\V1\Groups\AnnouncementsController@createComment');
            Route::get('{beitragId}/comment/{commentId}/history', 'API\V1\Groups\AnnouncementsController@getHistoryComment');
            Route::post('{beitragId}/comment/{commentId}/edit', 'API\V1\Groups\AnnouncementsController@editComment');
            Route::post('{beitragId}/comment/{commentId}/toggleHidden', 'API\V1\Groups\AnnouncementsController@hideComment');
            Route::post('{beitragId}/update', 'API\V1\Groups\AnnouncementsController@updateAnnouncement');
            Route::post('{beitragId}/delete', 'API\V1\Groups\AnnouncementsController@deleteAnnouncement');
        });

        Route::prefix("announcementtemplates")->group(function () {
            Route::get('templates', 'API\V1\Groups\AnnouncementTemplatesController@getAllAnnouncementTemplates');
            Route::post('{templateId}/update', 'API\V1\Groups\AnnouncementTemplatesController@updateAnnouncementTemplate');
            Route::post('{templateId}/delete', 'API\V1\Groups\AnnouncementTemplatesController@deleteAnnouncementTemplate');
        });

        Route::prefix('tasks')->group(function () {
            Route::post('/', 'API\V1\TaskController@getTask');
            Route::post('/create', 'API\V1\TaskController@createTask');
            Route::post('/createTemplate', 'API\V1\TaskController@createTaskTemplate');
            Route::post('/archived', 'API\V1\TaskController@getArchivedTasks');
            Route::post('/{taskId}', 'API\V1\TaskController@updateTask');
            Route::post('/{taskId}/finishTaskUpdate', 'API\V1\TaskController@finishTaskUpdate');
            Route::post('/{taskId}/formTemplate', 'API\V1\TaskController@saveTaskFormularTemplate');
            Route::get('/{taskId}/details', 'API\V1\TaskController@detailsTask');
            Route::post('/{taskId}/delete', 'API\V1\TaskController@deleteTask');
            Route::post('/{taskId}/content', 'API\V1\TaskController@contentTask');
            Route::post('/{taskId}/close', 'API\V1\TaskController@closeTask');

            // Einreichungen
            Route::get('/{taskId}/submissions', 'API\V1\TaskController@submissionListTask');
            Route::post('/{taskId}/submissions/createText', 'API\V1\TaskController@createTextSubmission');
            Route::post('/{taskId}/submissions/createDocument', 'API\V1\TaskController@createDocumentSubmission');
            Route::post('/{taskId}/submissions/createCheck', 'API\V1\TaskController@createCheckSubmission');
            Route::get('/{taskId}/submissions/{submissionId}', 'API\V1\TaskController@submissionDetails');
            Route::post('/{taskId}/submissions/{submissionId}', 'API\V1\TaskController@updateSubmission');
            Route::post('/{taskId}/submissions/{submissionId}/reset', 'API\V1\TaskController@resetSubmission');

            Route::post('/{taskId}/completeSubmissions', 'API\V1\TaskController@completeAllSubmissions');
            Route::post('/{taskId}/aiSubmissions', 'API\V1\TaskController@aiSubmissions');
        });

        Route::prefix('tasktemplates')->group(function () {
            Route::post('/', 'API\V1\TaskTemplateController@getTaskTemplates');
            Route::post('/create', 'API\V1\TaskTemplateController@createTaskTemplate');
            Route::get('/{taskTemplateId}/details', 'API\V1\TaskTemplateController@getTaskTemplate');
            Route::post('/{taskTemplateId}', 'API\V1\TaskTemplateController@updateTaskTemplate');
            Route::post('/{taskTemplateId}/create', 'API\V1\TaskTemplateController@createTaskFromTemplate');
            Route::post('/{taskTemplateId}/delete', 'API\V1\TaskTemplateController@deleteTaskTemplate');
            Route::post('/{taskTemplateId}/formTemplate', 'API\V1\TaskTemplateController@saveTaskFormularTemplate');
            Route::get('/{taskTemplateId}/interactiveCourse', 'API\V1\TaskTemplateController@interactiveCourseInformation');
            Route::post('/{taskTemplateId}/interactiveCourseCreate', 'API\V1\TaskTemplateController@interactiveCourseCreate');
        });

        Route::prefix('events')->group(function () {
            Route::post('/outlook/shareToken', 'API\V1\EventController@createOutlookShareToken');
            Route::post('/', 'API\V1\EventController@events');
            Route::get('/invites', 'API\V1\EventController@invites');
            Route::post('/create', 'API\V1\EventController@createEvent');
            Route::post('{eventId}', 'API\V1\EventController@updateEvent'); // aktualisiert ein event
            Route::get('{eventId}/details', 'API\V1\EventController@details'); // details eines events
            Route::post('{eventId}/delete', 'API\V1\EventController@deleteEvent'); // löscht ein event
            Route::post('{eventId}/status', 'API\V1\EventController@updateStatusEvent');

            // single appointments
            Route::post('{eventId}/single/cancel', 'API\V1\EventController@cancelSingleEvent');
            Route::post('{eventId}/single/move', 'API\V1\EventController@moveSingleEvent');
            Route::post('{eventId}/single/{singleAppointmentId}/delete', 'API\V1\EventController@deleteSingleEvent');
            Route::post('{eventId}/single/{singleAppointmentId}/update', 'API\V1\EventController@updateSingleEvent');

            Route::get('/timetable', 'API\V1\Administration\Timetable\TeachingController@generateTimetable'); // TODO: abstract from administration
        });

        Route::prefix('classbook')->group(function () {
            Route::prefix('/teaching')->group(function () {
                Route::get('/info', 'API\V1\Administration\Classbook\TeachingController@klassenbuchEntry');
                Route::get('/members', 'API\V1\Administration\Classbook\TeachingController@memberList');
                Route::post('/save', 'API\V1\Administration\Classbook\TeachingController@saveKlassenbuchEntry');
            });
            Route::get('/marks', 'API\V1\Classbook\MarksController@markForStudent');
            Route::get('/examDates', 'API\V1\Classbook\ExamDatesController@getExamDates');
            Route::get('/absenteeism', 'API\V1\Administration\Widgets\AbsenteeismWidget@getAbsenteeism');
        });

        Route::prefix('forms')->group(function () { // TODO: abstract from administration
            Route::get('/{form_id}', 'API\V1\Administration\FormsController@getRevision');
            Route::post('/{form_id}/revision/{revision_id}', 'API\V1\Administration\FormsController@saveFilledRevision');
        });

        Route::prefix('documents')->group(function () {
            Route::post('/', 'API\V1\DocumentController@createDocument'); // erstellt ein Dokument
            Route::post('/folders', 'API\V1\DocumentController@createFolder'); // erstellt einen Ordner
            Route::post('/move', 'API\V1\DocumentController@moveDocument'); // bewegt ein Dokument
            Route::post('/moveOrCopy', 'API\V1\DocumentController@moveOrCopyDocument'); // bewegt oder kopiert Dokument
            Route::post('/rename', 'API\V1\DocumentController@renameDocument'); // umbennen  ein Dokument
            Route::post('/zip', 'API\V1\DocumentController@zipDocument'); // umbennen  ein Dokument
            Route::post('/delete', 'API\V1\DocumentController@deleteDocument'); // löscht ein Dokument
            Route::get('/list', 'API\V1\DocumentController@listDocuments'); // listet Dokumente für übergebenes Model
            Route::post('/{documentId}/update', 'API\V1\DocumentController@updateDocument')->name('dokument.update'); // lädt ein Dokument herunter
            Route::middleware(['documentsPermission'])->group(function () {
                Route::get('/{documentId}/download', 'API\V1\DocumentController@downloadDocument')->name('dokument.download'); // lädt ein Dokument herunter
                Route::any('/{documentId}/callback', 'API\V1\DocumentController@callbackDocument')->name('dokument.callback'); //OnlyOffice-Callback
            });
            Route::prefix('pratical')->group(function () {
                    Route::get('dataset', 'API\V1\Administration\Sections\PraxisSectionController@dataset');
                });

                Route::prefix('{documentId}')->group(function () {
                    Route::get('', 'API\V1\DocumentController@getDocument'); // generelle Dokument-Metadaten abrufen
                    Route::get('/details', 'API\V1\DocumentController@detailsDocument')->name('dokument.details');
                    Route::get('/download', 'API\V1\DocumentController@downloadDocument')->name('dokument.download');
                    Route::prefix('subtitles')->group(function () {
                        Route::post('', 'API\V1\DocumentController@addSubtitle');
                        Route::get('', 'API\V1\DocumentController@getSubtitles');
                        Route::get('{subtitleId}', 'API\V1\DocumentController@getSubtitle');
                        Route::post('{subtitleId}', 'API\V1\DocumentController@editSubtitle');
                    });
                });

                Route::post('/ask', 'API\V1\AIDocumentController@askModel');
            });

        Route::prefix('formtemplates')->group(function () {
            Route::get('/{id}', 'API\V1\FormularTemplateController@getFormularTemplate'); // get
            Route::post('/', 'API\V1\FormularTemplateController@createFormularTemplate'); // erstellt ein FormularTemplate
            Route::post('/{id}', 'API\V1\FormularTemplateController@updateFormularTemplate'); // bearbeitet ein FormularTemplate
            Route::post('/{id}/delete', 'API\V1\FormularTemplateController@deleteFormularTemplate'); // löscht ein FormularTemplate
            Route::post('/{id}/print', 'API\V1\FormularTemplateController@printFormularTemplate'); // erstellt PDF auf Basis von einem FormularTempalte
            Route::post('/{id}/generate', 'API\V1\FormularTemplateController@generateFormularTemplate'); // erstellt auch ein PDF
        });

        Route::prefix('messages')->group(function () {
            Route::post('/report', 'API\V1\MessagesController@reportMessage');
            Route::get('/imlist', 'API\V1\MessagesController@getImListWithCloudUsernames');
            Route::post('/createimchat', 'API\V1\MessagesController@createImChat');
        });

        Route::prefix('masterdata')->group(function () {
            Route::get('/allcloudusers', 'API\V1\EducaMasterdataController@getAllCloudUsers'); // deprecated !!
            Route::post('/getCloudUsers', 'API\V1\EducaMasterdataController@getCloudUsers');
            Route::post('/searchCloudusers', 'API\V1\EducaMasterdataController@searchCloudUsers');
            Route::get('/rooms', 'API\V1\EducaMasterdataController@getRooms');
        });

        Route::prefix('meetings')->group(function () {
            Route::get('/{modelType}/{id}/info', 'API\V1\MeetingController@info');
            Route::get('/{modelType}/{id}/join', 'API\V1\MeetingController@join');
            Route::get('/{modelType}/{id}/live', 'API\V1\MeetingController@live');
            Route::post('/{modelType}/{id}/delete', 'API\V1\MeetingController@delete');
        });


        Route::prefix('interactiveCourses')->group(function () {

            Route::get('{interactive_course_id}/executions', [InteractiveCourseExecutionController::class, "list"]);
            Route::post('{interactive_course_id}/executions/create', [InteractiveCourseExecutionController::class, "create"]);

            Route::post('{interactive_course_id}/executions/{interactive_course_execution_id}/progress/create', [InteractiveCourseExecutionController::class, "createProgress"]);
            Route::post('{interactive_course_id}/executions/{interactive_course_execution_id}/progress/{course_execution_progress_id}/update', [InteractiveCourseExecutionController::class, "updateProgress"]);

            Route::get('{interactive_course_id}/interactiveCourseBadges/list', [InteractiveCourseBadgeController::class, "list"]);
            Route::post('{interactive_course_id}/interactiveCourseBadges/create', [InteractiveCourseBadgeController::class, "create"]);
            Route::post('{interactive_course_id}/interactiveCourseBadges/{interactive_course_badge_id}/update', [InteractiveCourseBadgeController::class, "update"]);
            Route::post('{interactive_course_id}/interactiveCourseBadges/delete', [InteractiveCourseBadgeController::class, "delete"]);

            Route::get('{interactive_course_id}/interactiveCourseLevels/list', [\App\Http\Controllers\API\V1\EducaCourse\InteractiveCourseLevelController::class, "list"]);
            Route::post('{interactive_course_id}/interactiveCourseLevels/create', [\App\Http\Controllers\API\V1\EducaCourse\InteractiveCourseLevelController::class, "create"]);
            Route::post('{interactive_course_id}/interactiveCourseLevels/{interactive_course_level_id}/update', [\App\Http\Controllers\API\V1\EducaCourse\InteractiveCourseLevelController::class, "update"]);
            Route::post('{interactive_course_id}/interactiveCourseLevels/{interactive_course_level_id}/delete', [\App\Http\Controllers\API\V1\EducaCourse\InteractiveCourseLevelController::class, "delete"]);

        });

        Route::prefix('learnContent')->group(function () {
            Route::post('create', 'API\V1\LearnContent\LearnContentController@create');
            Route::get('dashboard', 'API\V1\LearnContent\LearnContentController@dashboard');
            Route::get('browse', 'API\V1\LearnContent\LearnContentController@browse');
            Route::get('download', 'API\V1\LearnContent\ProtectedDownloaderController@downloadContent');
            Route::post('import', 'API\V1\LearnContent\LearnContentController@import');

            Route::prefix('tags')->group(function () {
                Route::get('/', 'API\V1\LearnContent\LearnContentTagsController@allTags');
                Route::get('{learnContentId}', 'API\V1\LearnContent\LearnContentTagsController@learnContentTags');
            });


            Route::prefix('content')->group(function () {
                Route::get('{learnContentId}', 'API\V1\LearnContent\LearnContentController@get');
                Route::post('{learnContentId}/metadata', 'API\V1\LearnContent\LearnContentController@updateMetadata');
                Route::post('{learnContentId}/delete', 'API\V1\LearnContent\LearnContentController@delete');
                Route::get('{learnContentId}/download', 'API\V1\LearnContent\LearnContentController@download');
                Route::get('{learnContentId}/xapi/download', 'API\V1\LearnContent\LearnContentController@xApiDownload');
            });

            Route::prefix('provider')->group(function () {
                Route::get('{name}/configuration', 'API\V1\LearnContent\LearnContentController@getProviderConfiguration');
                Route::get('{name}/overview', 'API\V1\LearnContent\LearnContentController@getProviderOverview');

                // Custom provider apis
                Route::post('shareFiles/upload', 'API\V1\LearnContent\Provider\ShareFileController@upload');
                Route::get('youtube/{id}', 'API\V1\LearnContent\Provider\YoutubeProvider@getYoutubeInformation');
            });

            Route::prefix('category')->group(function () {
                Route::get('/', 'API\V1\LearnContent\LearnContentCategoryController@getCategories');
                Route::get('{categoryId}', 'API\V1\LearnContent\LearnContentCategoryController@getCategory');
                Route::post('/', 'API\V1\LearnContent\LearnContentCategoryController@createCategory');
                Route::post('{categoryId}/update', 'API\V1\LearnContent\LearnContentCategoryController@editCategory');
                Route::post('{categoryId}/image', 'API\V1\LearnContent\LearnContentCategoryController@imageCategory');
                Route::post('{categoryId}/delete', 'API\V1\LearnContent\LearnContentCategoryController@deleteCategory');
            });

            Route::prefix('bookmark')->group(function () {
                Route::post('list', 'API\V1\LearnContent\LearnContentBookmarkController@createList');
                Route::get('list/{listId}', 'API\V1\LearnContent\LearnContentBookmarkController@getList');
                Route::post('list/{listId}/edit', 'API\V1\LearnContent\LearnContentBookmarkController@updateList');
                Route::post('list/{listId}/add/{learnContentId}', 'API\V1\LearnContent\LearnContentBookmarkController@bookmark');
                Route::post('list/{listId}/remove/{learnContentId}', 'API\V1\LearnContent\LearnContentBookmarkController@unbookmark');
                Route::post('list/{listId}/delete', 'API\V1\LearnContent\LearnContentBookmarkController@deleteList');
                Route::get('lists', 'API\V1\LearnContent\LearnContentBookmarkController@getLists');
                Route::get('lists/{learnContentId}', 'API\V1\LearnContent\LearnContentBookmarkController@inLists');
            });

            Route::prefix('likes')->group(function () {
                Route::get('{learnContentId}', 'API\V1\LearnContent\LearnContentLikeController@getLikes');
                Route::post('{learnContentId}/add', 'API\V1\LearnContent\LearnContentLikeController@addLike');
                Route::post('{learnContentId}/remove', 'API\V1\LearnContent\LearnContentLikeController@removeLike');
            });

            Route::prefix('comments')->group(function () {
                Route::get('content/{learnContentId}', 'API\V1\LearnContent\LearnContentCommentController@getComments');
                Route::post('content/{learnContentId}/write', 'API\V1\LearnContent\LearnContentCommentController@writeComment');
                Route::post('post/{commentId}/delete', 'API\V1\LearnContent\LearnContentCommentController@deleteComment');
            });

            Route::prefix('competences')->group(function () {
                Route::get('{learnContentId}', 'API\V1\LearnContent\LearnContentCompetencesController@getCompetences');
                Route::post('{learnContentId}/add/{competenceId}', 'API\V1\LearnContent\LearnContentCompetencesController@addCompetence');
                Route::post('{learnContentId}/points/{competenceId}', 'API\V1\LearnContent\LearnContentCompetencesController@updateCompetencePoints');
                Route::post('{learnContentId}/remove/{competenceId}', 'API\V1\LearnContent\LearnContentCompetencesController@removeCompetence');
            });

            Route::prefix('permissions')->group(function () {
                Route::get('{learnContentId}', 'API\V1\LearnContent\LearnContentPermissionController@getDetails');
                Route::post('{learnContentId}/addTenant', 'API\V1\LearnContent\LearnContentPermissionController@addTenant');
                Route::post('{learnContentId}/updateTenant', 'API\V1\LearnContent\LearnContentPermissionController@updateTenant');
                Route::post('{learnContentId}/deleteTenant', 'API\V1\LearnContent\LearnContentPermissionController@deleteTenant');
                Route::post('{learnContentId}/addUser', 'API\V1\LearnContent\LearnContentPermissionController@addUser');
                Route::post('{learnContentId}/updateUser', 'API\V1\LearnContent\LearnContentPermissionController@updateUser');
                Route::post('{learnContentId}/deleteUser', 'API\V1\LearnContent\LearnContentPermissionController@deleteUser');
            });

            Route::prefix('translate')->group(function () {
                Route::get('{learnContentId}', 'API\V1\LearnContent\LearnContentTranslateController@getTranslation');
                Route::post('{learnContentId}/translate', 'API\V1\LearnContent\LearnContentTranslateController@startTranslation');
            });
        });


        Route::prefix('filter')->group(function () {
            Route::get('/', 'API\V1\FavoriteFilterController@getFilterForKey');
            Route::post('/update', 'API\V1\FavoriteFilterController@updateFilter');
            Route::post('/{id}/delete', 'API\V1\FavoriteFilterController@deleteFilter');
        });


        Route::prefix('settings')->group(function () {
            Route::get('/', 'API\V1\SettingsController@getSettingsApps');
            Route::post('/general/save', 'API\V1\SettingsController@saveGeneralSettings');
            Route::post('/general/updatePassword', 'API\V1\SettingsController@updatePassword');
            Route::post('/general/2FAToggle', 'API\V1\SettingsController@twoFAToggle');
            Route::get('/general/2FAqrCode', 'API\V1\SettingsController@twoFAqrCode');
            Route::get('/general/security', 'API\V1\SettingsController@security');
            Route::post('/general/security', 'API\V1\SettingsController@saveSecurity');
            Route::post('/general/updateProfileImage', 'API\V1\SettingsController@updateProfilImage');
            Route::get('/{appName}', 'API\V1\SettingsController@getSettingsForApp');
            Route::post('/{appName}/save', 'API\V1\SettingsController@saveSettingsForApp');
            Route::get('/analytics/xapi/download', 'API\V1\Analytics\SettingsController@downloadxAPI');
        });

        Route::prefix('correspondence')->group(function () {
            Route::post('/multiCreate', 'API\V1\EducaCorrespondenceController@createMultiCorespondeMulti');
        });


        Route::prefix('educaCourse')->group(function () {
            Route::post('create', 'API\V1\EducaCourse\EducaCourseController@createCourse');
            Route::post('upload', 'API\V1\EducaCourse\EducaCourseController@uploadCourse');
            Route::post('import', 'API\V1\EducaCourse\EducaCourseController@import');
            Route::get('list', 'API\V1\EducaCourse\EducaCourseController@list');
            Route::get('{courseId}', 'API\V1\EducaCourse\EducaCourseController@get');
            Route::post('{courseId}', 'API\V1\EducaCourse\EducaCourseController@save');
            Route::get('{courseId}/export', 'API\V1\EducaCourse\EducaCourseController@export');
            Route::post('{courseId}/start', 'API\V1\EducaCourse\EducaCourseController@startExecution');
            Route::post('{courseId}/reorder', 'API\V1\EducaCourse\EducaCourseController@reorderChapters');
            Route::post('{courseId}/chapter/add', 'API\V1\EducaCourse\EducaCourseController@addChapter');
            Route::get('{courseId}/chapter/answers', 'API\V1\EducaCourse\EducaCourseController@answers');

            Route::post('{courseId}/chapter/{chapterId}/save', 'API\V1\EducaCourse\EducaCourseController@saveChapter');
            Route::post('{courseId}/chapter/{chapterId}/delete', 'API\V1\EducaCourse\EducaCourseController@deleteChapter');
            Route::get('{courseId}/chapter/{chapterId}/export', 'API\V1\EducaCourse\EducaCourseController@exportChapter');
            Route::post('{courseId}/chapter/{chapterId}/reorder', 'API\V1\EducaCourse\EducaCourseController@reorderTopics');

            Route::post('{courseId}/chapter/{chapterId}/topic/add', 'API\V1\EducaCourse\EducaCourseController@addTopic');
            Route::post('{courseId}/chapter/{chapterId}/topic/{topicId}/save', 'API\V1\EducaCourse\EducaCourseController@saveTopic');
            Route::post('{courseId}/chapter/{chapterId}/topic/{topicId}/delete', 'API\V1\EducaCourse\EducaCourseController@deleteTopic');

            Route::post('{courseId}/chapter/{chapterId}/topic/{topicId}/variant', 'API\V1\EducaCourse\EducaCourseController@addVariant');
            Route::post('{courseId}/chapter/{chapterId}/topic/{topicId}/variant/{variantId}', 'API\V1\EducaCourse\EducaCourseController@saveVariant');
            Route::post('{courseId}/chapter/{chapterId}/topic/{topicId}/variant/{variantId}/delete', 'API\V1\EducaCourse\EducaCourseController@deleteVariant');

            // analytics
            Route::get('{courseId}/analytics', 'API\V1\EducaCourse\EducaAnalyticsController@overview');
            Route::get('{courseId}/analytics/statements', 'API\V1\EducaCourse\EducaAnalyticsController@statements');
            Route::get('{courseId}/analytics/userCentric', 'API\V1\EducaCourse\EducaAnalyticsController@userCentric');
            Route::get('{courseId}/analytics/h5p', 'API\V1\EducaCourse\EducaAnalyticsController@h5p');
            Route::get('{courseId}/analytics/task', 'API\V1\EducaCourse\EducaAnalyticsController@task');
        });

        Route::prefix('h5p')->group(function () {
            Route::post('import', 'API\V1\EducaCourse\EducaCourseController@importContent');

            Route::prefix('content')->group(function () {
                Route::get('{contentId}/play', 'API\V1\H5P\H5PPlayerController@play');
                Route::get('{contentId}/content', 'API\V1\H5P\H5PContentController@getFile');

                // Edit Routes
                Route::get('{contentId}/edit', 'API\V1\H5P\H5PEditorController@edit');
                Route::post('{contentId}/save', 'API\V1\H5P\H5PEditorController@save');
            });

            Route::prefix('library')->group(function () {
                Route::get('/{machineName}', 'API\V1\H5P\H5PLibraryController@getFile');
            });

            Route::get('contentUserData', 'API\V1\H5P\H5PPlayerController@getContentUserData');
            Route::post('contentUserData', 'API\V1\H5P\H5PPlayerController@saveContentUserData');
            Route::post('resetUserData', 'API\V1\H5P\H5PPlayerController@resetUserData');
            Route::post('exportUserData', 'API\V1\H5P\H5PPlayerController@exportUserProgressData');
            Route::post('setFinished', 'API\V1\H5P\H5PPlayerController@setFinished');

            Route::get('ajax', 'API\V1\H5P\H5PAjaxController@ajax');
            Route::post('ajax', 'API\V1\H5P\H5PAjaxController@ajaxPost');
        });


        Route::prefix('scorm')->group(function () {
            Route::post('/upload', [ScormController::class, "upload"]);
            Route::post('/track/{uuid}', [ScormTrackController::class, "set"]);
            Route::get('/download/{id}', [ScormController::class, "zipScorm"]);
        });

        Route::prefix('cmi5')->group(function () {
            Route::post('/upload', [Cmi5Controller::class, "upload"]);
            Route::post('/fetch', [LrsController::class, 'fetch'])->name("cmi5.fetch");
            Route::get('/play/{auId}', [Cmi5Controller::class, "read"]);
        });

        Route::prefix('xAPI')->group(function () {
            Route::post('create', 'API\V1\xAPI\XAPIBaseController@create');
            Route::post('createMulti', 'API\V1\xAPI\XAPIBaseController@createMulti');
            Route::get('{id}', 'API\V1\xAPI\XAPIBaseController@get');
            Route::get('{id}/delete', 'API\V1\xAPI\XAPIBaseController@delete');
        });

        Route::prefix('support')->group(function () {
            Route::post('/', 'API\V1\SupportController@createSupportTicket');
            Route::get('/list', 'API\V1\SupportController@listSupportTicket');
            Route::get('/ticket/{id}', 'API\V1\SupportController@getSupportTicket');
            Route::post('/ticket/{id}', 'API\V1\SupportController@messageSupportTicket');
            Route::post('/ticket/{id}/close', 'API\V1\SupportController@closeSupportTicket');
            Route::post('/ticket/{id}/file', 'API\V1\SupportController@fileSupportTicket');
            Route::get('/ticket/{id}/article/{article}/attachment/{attachment}', 'API\V1\SupportController@getAttachment');

            // Block user

            // Report spam for annoucment or comments
            Route::post('/block', 'API\V1\SupportController@blockUser');
            Route::post('/report', 'API\V1\SupportController@report');
        });


        Route::prefix('cloud')->group(function () {

            Route::prefix('general')->group(function () {
                Route::get('/', 'API\V1\Cloud\GeneralController@chartInfo');
                Route::prefix('widgets')->group(function () {
                    Route::get('/new_users', 'API\V1\Cloud\GeneralController@newUsers');
                    Route::get('/active_users', 'API\V1\Cloud\GeneralController@activeUsers');
                    Route::get('/activity', 'API\V1\Cloud\GeneralController@activity');
                    Route::get('/learnContentObjects', 'API\V1\Cloud\GeneralController@learnContentObjects');
                    Route::get('/objects', 'API\V1\Cloud\GeneralController@objects');
                    Route::get('/feeds', 'API\V1\Cloud\GeneralController@feeds');
                    Route::get('/spaces', 'API\V1\Cloud\GeneralController@spaces');
                });
            });

            Route::prefix('analytics')->group(function () {
                Route::get('/reports', 'API\V1\Cloud\AnalyticsController@getReports');
                Route::post('/reports', 'API\V1\Cloud\AnalyticsController@saveReportsRoles');
            });

            Route::prefix('groups')->group(function () {
                Route::get('/', 'API\V1\Cloud\GroupsController@getGroups');
                Route::post('/{group_id}/delete', 'API\V1\Cloud\GroupsController@deleteGroup');
                Route::post('/{group_id}/unarchive', 'API\V1\Cloud\GroupsController@unArchiveGroup');
            });

            Route::prefix('tenants')->group(function () {
                Route::get('/', 'API\V1\Cloud\TenantsController@getAvailableTenants');

                Route::post('/create', 'API\V1\Cloud\TenantsController@createTenant');
                Route::get('/{tenant_id}/get', 'API\V1\Cloud\TenantsController@getTenant');
                Route::post('/{tenant_id}/update', 'API\V1\Cloud\TenantsController@updateTenant');
                Route::post('/{tenant_id}/delete', 'API\V1\Cloud\TenantsController@deleteTenant');

            });

            Route::prefix('permissions')->group(function () {
                Route::get('/', 'API\V1\Cloud\PermissionsController@getAvailablePermissions');
                Route::get('/roles', 'API\V1\Cloud\PermissionsController@getAvailableRoles');

                Route::post('/roles/create', 'API\V1\Cloud\PermissionsController@createRole');
                Route::post('/roles/{role_id}/edit', 'API\V1\Cloud\PermissionsController@editRole');
                Route::post('/roles/{role_id}/delete', 'API\V1\Cloud\PermissionsController@deleteRole');
                Route::post('/flip', 'API\V1\Cloud\PermissionsController@flipRolePermission');

            });

            Route::prefix('user')->group(function () {
                Route::get('/list', 'API\V1\Cloud\UserController@userList');
                Route::post('/{cloud_id}/roles', 'API\V1\Cloud\UserController@saveUserRoles');
                Route::post('/multipleEditRoles', 'API\V1\Cloud\UserController@changeMultipleUserRoles');
                Route::post('/import', 'API\V1\Cloud\UserController@excelImport');
                Route::get('/{cloud_id}/', 'API\V1\Cloud\UserController@userInfo');
                Route::post('/{cloud_id}/update', 'API\V1\Cloud\UserController@updateUser');
                Route::post('/create', 'API\V1\Cloud\UserController@createUser');
                Route::post('/{cloud_id}/delete', 'API\V1\Cloud\UserController@deleteUser');
            });

            Route::prefix('competenceCluster')->group(function () {
                Route::get('/', 'API\V1\Cloud\CompetenceController@getCluster');
                Route::post('/create', 'API\V1\Cloud\CompetenceController@createCluster');
                Route::post('/{clusterId}/update', 'API\V1\Cloud\CompetenceController@updateCluster');
                Route::post('/{clusterId}/delete', 'API\V1\Cloud\CompetenceController@deleteCluster');
                Route::get('/{clusterId}/competences', 'API\V1\Cloud\CompetenceController@getCompetencesForCluster');
                Route::post('/{clusterId}/competence', 'API\V1\Cloud\CompetenceController@createCompentenceForCluster');
            });

            Route::prefix('competence')->group(function () {
                Route::get('/all', 'API\V1\Cloud\CompetenceController@getAllCompetences');
                Route::get('/{competenceId}', 'API\V1\Cloud\CompetenceController@getCompetence');
                Route::post('/{competenceId}/update', 'API\V1\Cloud\CompetenceController@updateCompentence');
                Route::post('/{competenceId}/delete', 'API\V1\Cloud\CompetenceController@deleteCompentence');
            });

            Route::prefix('workflow')->group(function () {
                Route::get('/', 'API\V1\Cloud\WorkflowController@getWorkflows');
                Route::get('/getAvailableNodes', 'API\V1\Cloud\WorkflowController@availableNodes');
                Route::post('/add', 'API\V1\Cloud\WorkflowController@addWorkflow');

                Route::prefix('{workflow_id}')->group(function () {
                    Route::get('/details', 'API\V1\Cloud\WorkflowController@detailsWorkflow');
                    Route::post('/start', 'API\V1\Cloud\WorkflowController@startWorkflow');
                    Route::post('/update', 'API\V1\Cloud\WorkflowController@updateWorkflow');
                });

                Route::prefix('instances/{instance_id}')->group(function () {
                    Route::post('/stop', 'API\V1\Cloud\WorkflowController@stopWorkflowInstance');
                    Route::post('/pause', 'API\V1\Cloud\WorkflowController@pauseWorkflowInstance');
                    Route::post('/unpause', 'API\V1\Cloud\WorkflowController@unpauseWorkflowInstance');
                    Route::post('/submitFormData', 'API\V1\Cloud\WorkflowController@submitFormData');
                    Route::get('/details', 'API\V1\Cloud\WorkflowController@detailWorkflowInstance');
                });
            });


            Route::prefix('maintenance')->group(function () {
                Route::get('/', 'API\V1\Cloud\MaintenanceController@getInformation');
            });
        }); // todo add middleware


        Route::prefix('explore')->group(function () {
            Route::get('/tenants', 'API\V1\ExploreController@getTenants');
            Route::get('/tenants/{tenant_id}/groups', 'API\V1\ExploreController@getPublicTenantGroups');
        });

        Route::prefix('ai')->group(function () {
            Route::get('/completeTextbox', 'API\V1\AI\GPTController@completeTextbox');
        });


        Route::prefix('agent')->group(function () {
            Route::prefix('knowledge')->group(function () {
                Route::post('/', 'API\V1\AI\LernContentCreatorController@createKnowledgeProgress');
                Route::get('/poll/{progress_id}', 'API\V1\AI\LernContentCreatorController@pollKnowledgeAgent');
            });
            Route::prefix('research')->group(function () {
                Route::post('/', 'API\V1\AI\LernContentCreatorController@createCreatorProgress');
                Route::get('/poll/{progress_id}', 'API\V1\AI\LernContentCreatorController@pollAgent');
                Route::get('/extract/{progress_id}', 'API\V1\AI\LernContentCreatorController@pollExtraction');
                Route::post('/updateLearnContent/{progress_id}', 'API\V1\AI\LernContentCreatorController@updateLearnContent');
                Route::post('/setLearnContentTargets/{progress_id}', 'API\V1\AI\LernContentCreatorController@setLearnContentTargets');
                Route::post('/updateLearnContentTargets/{progress_id}', 'API\V1\AI\LernContentCreatorController@updateLearnContentTargets');
                Route::post('/createLearnContentH5pQuiz/{progress_id}', 'API\V1\AI\LernContentCreatorController@createLearnContentH5pQuiz');
                Route::get('/quiz/{progress_id}', 'API\V1\AI\LernContentCreatorController@quizResearch');
                Route::get('/quiz/{progress_id}/generateKeywords', 'API\V1\AI\LernContentCreatorController@generateKeywordsFromLearnContent');
                Route::get('/quiz/{progress_id}/generateQuestions', 'API\V1\AI\LernContentCreatorController@generateQuestionsFromLearnContent');
            });
        });

        Route::prefix('addressbook')->group(function () {
            Route::get('/list', 'API\V1\Adressbook\AdressbookController@getAllGeneralContacts');
            Route::get('/', 'API\V1\Adressbook\AdressbookController@getContacts');
            Route::post('/add', 'API\V1\Adressbook\AdressbookController@addContact');
            Route::post('/update', 'API\V1\Adressbook\AdressbookController@updateContact');
            Route::post('/delete', 'API\V1\Adressbook\AdressbookController@deleteContact');

            Route::prefix('mail')->group(function () {
                Route::post('/', 'API\V1\Adressbook\AdressbookController@sendMail');
            });
        });

        Route::prefix('externalIntegration')->group(function () {
            Route::get('/templates', 'API\V1\ExternalIntegrationTemplateController@getTemplates');
        });


        Route::prefix('profile')->group(function () {

            Route::prefix('educaPass')->group(function () {
                Route::get('/download/apple', 'API\V1\Profil\EducaPassController@downloadApple');
                Route::get('/download/android', 'API\V1\Profil\EducaPassController@downloadAndroid');
            });
        });

        Route::post('/code/inApp', 'API\V1\CodeController@checkCodeInApp');
    });


});

