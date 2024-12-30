<?php

namespace App\Http\Controllers\API\V1\Administration\Masterdata;

use App\AdditionalInfo;
use App\ExamExecutionDate;
use App\Exceptions\ModalTransactionException;
use App\Fach;
use App\FehlzeitTyp;
use App\FormularAbgeschickt;
use App\FormularAnhang;
use App\Http\Controllers\API\V1\Administration\AdministationApiController;
use App\Http\Controllers\API\V1\Administration\ExamAdministration\ExamExecutionController;
use App\Klasse;
use App\Kohorte;
use App\Kontakt;
use App\Lehrplan;
use App\LehrplanEinheit;
use App\Merkmal;
use App\Module;
use App\ModulExam;
use App\ModulExamExecution;
use App\ModulPartExam;
use App\Note;
use App\NotenCache;
use App\Schule;
use App\Schuler;
use App\Schuljahr;
use App\StudyProgressEntry;
use Carbon\Traits\Creator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use StuPla\CloudSDK\formular\models\Formular;

class MasterdataStudentController extends AdministationApiController
{
    /**
     * @OA\Get(
     *     tags={"masterdata", "v1"},
     *     path="/api/v1/administration/masterdata/schools/{school_id}/students",
     *     description="",
     *     @OA\Parameter(
     *     name="school_id",
     *     required=true,
     *     in="path",
     *     description="id of the school",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Array of all students of a school in the system with reduced additional information (masterdata)")
     * )
     */
    public function students($school_id, Request $request)
    {
        $data = [];

        $contactsNewQuery = DB::table('schulers')->select(["schulers.id as real_id", "schulers.firstname2 as firstname2","schulers.*","additional_infos.*","kontakts.name as praxispartner", "ansprechpartner.firstname as ansprechpartnerFirstname","ansprechpartner.lastname as ansprechpartnerLastname","ansprechpartner.email as praxispartnerEmailComp"])
            ->leftJoin(
            'additional_infos', 'schulers.info_id', '=','additional_infos.id'
        )->join(
            'schuler_schule', 'schulers.id', '=','schuler_schule.schuler_id'
        )->leftJoin(
            'kontakts', 'schulers.company_id', '=','kontakts.id'
        )->leftJoin(
            'kontakts as ansprechpartner', 'schulers.kontakt_id', '=','ansprechpartner.id'
        )->where('schuler_schule.schule_id','=',$school_id)->get();

        foreach ($contactsNewQuery as $obj) {
            $schuler = new Schuler;
            $obj->id = $obj->real_id;
            $schuler->id = $obj->id;

            $obj->klassen = $schuler->klasseForSchuljahr($request->input("year_id"));
            if ($obj->praxispartner == null) {
                $obj->praxispartner = "Kein Praxispartner";
            }

            $obj->ansprechpartnerName = $obj->ansprechpartnerFirstname." ". $obj->ansprechpartnerLastname;
            $obj->isGuestStudent = $schuler->guest_student != "none";
            $obj->kohorte = $schuler->getKohorteForSchuljahr($request->input("year_id"));
            $obj->current_study_information = $schuler->getCurrentStudyInformation($request->input("year_id"));
            $obj->status = $this->isActive($obj->current_study_information); // $obj->current_study_information == null || $obj->current_study_information->kohorte == null ? "inactive" : "active";
            $obj->isGuestStudent = !($obj->current_study_information == null) && $obj->current_study_information->status == "guest" || $obj->status;
            if($obj->current_study_information == null)
            {
                $obj->current_study_information = $schuler->getLastStudyInformation();
            }
            if($obj->current_study_information != null) {
                $obj->current_study_information->loadAll();
               // $obj["current_study_information"]->load("kohorte.studium");
               // $obj["current_study_information"]->load("schuljahr.schule");

            }

        }
        return parent::createJsonResponse("student", false, 200, ["students" => $contactsNewQuery]);
    }

    public static function isActive($current_study_information)
    {
        if($current_study_information == null)
            return "inactive";
        if($current_study_information->status == "freshmen" || $current_study_information->status == "newcomer" || $current_study_information->status == "re-registrants" || $current_study_information->status == "downgrading" ||
        $current_study_information->status == "extended" || $current_study_information->status == "guest" || $current_study_information->status == "victoria_guest")
        {
            return "active";
        }
        return "inactive";
    }

    /**
     * @OA\Get(
     *     tags={"masterdata", "v1"},
     *     tags={"masterdata", "v1"},
     *     path="/api/v1/administration/masterdata/students/{student_id}/studentDetailed",
     *     description="",
     *     @OA\Parameter(
     *     name="student_id",
     *     required=true,
     *     in="path",
     *     description="id of the student",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Requested student with all additional info")
     * )
     */
    public function studentDetailedWithResponse($student_id, Request $request)
    {
        $student = Schuler::findOrFail($student_id);
        $addInfo = $student->getAddInfo();
        foreach ($addInfo->toArray() as $key => $value) {
            if ($key !== "id" && $key !== "firstname2")
                $student->$key = $value;
        }

        $student->praxispartner = "Kein Praxispartner";
        if ($student->company_id != null) {
            $kontakt = Kontakt::find($student->company_id);
            if ($kontakt != null) {
                $student->praxispartner = $kontakt->name;
            }
        }
        $student->load('schulen');

        $formulare = [];
        $forms = [];
        $formular_templates = [];
        $formulareDB = Formular::whereIn('id', explode(",", $student->schulen->first()->getEinstellungen("formulare", "")))->get();
        foreach ($formulareDB as $formular) {
            $forms[$formular->id] = $formular;
            $formulare[$formular->id] = json_decode($student->getLatestFormulaDataFor($formular));
            $formular_templates[$formular->id] = json_decode($formular->getLastRevisionAttribute()->data);
        }
        $student["current_study_information"] = $student->getCurrentStudyInformation($request->year_id);
        if( $student["current_study_information"] == null)
        {
            $schuljahr_ids = Schuljahr::where("start","<=", \Carbon\Carbon::now())->where("ende",">=",Carbon::now())->pluck("id");
            $student["current_study_information"] = StudyProgressEntry::where('schuler_id','=',$student->id)->whereIn('schuljahr_id',$schuljahr_ids)->first();
        }
        $student["status"] = $this->isActive($student["current_study_information"]);
        $student["isGuestStudent"] = !($student["current_study_information"] == null) && $student["current_study_information"]->status == "guest";
        if ($student["current_study_information"] == null) {
            $student["current_study_information"] = $student->getLastStudyInformation();
        }
        if ($student["current_study_information"]) {
            $student["current_study_information"]->loadAll();
        }

        $student->load("schulen");

        return ["student" => $student, "forms" => $forms, "forms_data" => $formulare, "forms_templates" => $formular_templates];
    }

    public function studentDetailed($student_id, Request $request)
    {
        $response = $this->studentDetailedWithResponse($student_id,$request);
        return parent::createJsonResponse("student", false, 200, $response);
    }

    /**
     * @OA\Post(
     *     tags={"masterdata", "v1"},
     *     path="/api/v1/administration/masterdata/students/add",
     *     description="",
     *     @OA\Response(response="200", description="Add a student with additional info")
     * )
     */
    public function addStudent(Request $request)
    {
        $student = new Schuler;
        $addInfo = new AdditionalInfo;
        foreach ($request->object as $key => $value) {
            if ($key != "id" && $key != "info_id" && $key != "personalnummer") {
                if (Schema::hasColumn($student->getTable(), $key)) {
                    $student->$key = $value;
                } elseif (Schema::hasColumn($addInfo->getTable(), $key)) {
                    $addInfo->$key = $value;
                }
            }
        }
        $addInfo->save();
        // generate Marktikerlnummer
        $student->info_id = $addInfo->id;
        $student->generateMartikelnummer();
        $student->save();
        $student->schulen()->sync([$request->input("school_id")]);

        return $this->studentDetailed($student->id, $request);
    }


    /**
     * @OA\Post(
     *     tags={"masterdata", "v1"},
     *     path="/api/v1/administration/masterdata/students/{student_id}/update",
     *     description="",
     *     @OA\Parameter(
     *     name="student_id",
     *     required=true,
     *     in="path",
     *     description="id of the student",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Update a student with additional info")
     * )
     */
    public function updateStudent($student_id, Request $request)
    {
        $student = Schuler::findOrFail($student_id);
        $addInfo = $student->getAddInfo();
        foreach ($request->object as $key => $value) {
            if ($key != "id" && $key != "info_id" && $key != "personalnummer" && $key != "image" && $key != "kontakt_id" && $key != "company_id") {
                if ($key == "birthdate")
                    $value = $value == null ? null : Carbon::createFromTimestamp($value)->toDateTime();
                if (Schema::hasColumn($student->getTable(), $key)) {
                    $student->$key = $value;
                } elseif (Schema::hasColumn($addInfo->getTable(), $key)) {
                    $addInfo->$key = $value;
                }
            }
        }
        $addInfo->save();
        $student->save();

        return $this->studentDetailed($student_id, $request);
    }

    /**
     * @OA\Post(
     *     tags={"masterdata", "v1"},
     *     path="/api/v1/administration/masterdata/students/{student_id}/setForm/{form_id}/{creator_type}/{creator_id}",
     *     description="",
     *     @OA\Parameter(
     *     name="student_id",
     *     required=true,
     *     in="path",
     *     description="id of the student",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="form_id",
     *     required=true,
     *     in="query",
     *     description="id of the form",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="form_data",
     *     required=true,
     *     in="query",
     *     description="form data",
     *       @OA\Schema(
     *         type="object"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Set form values for a given form, student and creator")
     * )
     */
    public function setForm($student_id, Request $request)
    {
        $student = Schuler::findOrFail($student_id);

        $form_id = $request["form_id"];
        $formular = Formular::findOrFail($form_id);
        $formular_revision_id = $formular->last_revision->id;

        $student->saveFormulaDataFor($formular_revision_id, json_encode($request["form_data"]));

        return $this->studentDetailed($student_id, $request);
    }

    /**
     * @OA\Post(
     *     tags={"masterdata", "v1"},
     *     path="/api/v1/administration/masterdata/students/{student_id}/delete",
     *     description="",
     *     @OA\Parameter(
     *     name="student_id",
     *     required=true,
     *     in="path",
     *     description="id of the student",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Delete a student and its additional info")
     * )
     */
    public function deleteStudent($student_id, Request $request)
    {
        $student = Schuler::findOrFail($student_id);
        $addInfo = $student->getAddInfo();
        $student->info_id = null;
        $student->save();
        Merkmal::where('model_id', '=', $student->id)->where('model_type', '=', 'schuler')->delete();
        $counts = $student->delete();
        $addInfo->delete();

        return parent::createJsonResponse("student", false, 200, ["id" => $student_id, "counts" =>$counts]);
    }


    public function getPlannungsGruppen($student_id, Request $request)
    {
        $schuler = Schuler::findOrFail($student_id);
        $klassen = DB::table('klasse_schuler')
            ->join('klasses', 'klasses.id', '=', 'klasse_schuler.klasse_id')
            ->where('schuler_id', '=', $schuler->id)->orderBy('from')->get();

        return parent::createJsonResponse("students planungsgruppen", false, 200, ["schoolclasses" => $klassen]);
    }

    public function assignPlannungsGruppen($student_id, Request $request)
    {
        $schuler = Schuler::findOrFail($student_id);

        $klasse = Klasse::findOrFail($request->input("schoolclass_id"));
        DB::table('klasse_schuler')->where('klasse_id', '=', $klasse->id)->where('schuler_id', '=', $schuler->id)->delete();

        $from = $request->from ? Carbon::createFromTimestamp($request->from) : null;
        $until = $request->until ? Carbon::createFromTimestamp($request->until) : null;

        DB::table('klasse_schuler')->insert(
            ['klasse_id' => $klasse->id, 'schuler_id' => $schuler->id, 'from' => $from, 'until' => $until, 'note' => $request->note]
        );
        return self::getPlannungsGruppen($student_id, $request);
    }

    public function removePlannungsGruppen($student_id, Request $request)
    {
        $schuler = Schuler::findOrFail($student_id);
        $klasse = Klasse::findOrFail($request->input("schoolclass_id"));
        DB::table('klasse_schuler')->where('klasse_id', '=', $klasse->id)->where('schuler_id', '=', $schuler->id)->delete();
        return self::getPlannungsGruppen($student_id, $request);
    }

    public function editPlannungsGruppen($student_id, Request $request)
    {
        $schuler = Schuler::findOrFail($student_id);
        $klasse = Klasse::findOrFail($request->input("schoolclass_id"));
        DB::table('klasse_schuler')->where('klasse_id', '=', $klasse->id)->where('schuler_id', '=', $schuler->id)->delete();

        $from = $request->from ? Carbon::createFromTimestamp($request->from)->toDateString() : null;
        $until = $request->until ? Carbon::createFromTimestamp($request->until)->toDateString() : null;

        DB::table('klasse_schuler')->insert(
            ['klasse_id' => $klasse->id, 'schuler_id' => $schuler->id, 'from' => $from, 'until' => $until, 'note' => $request->note]
        );


        return self::getPlannungsGruppen($student_id, $request);
    }

    public function getPraxisWerdegang($student_id, Request $request)
    {
        $schuler = Schuler::findOrFail($student_id);
        $partners = [];

        $history = DB::table("partner_history")->where("schuler", "=", $schuler->id)->orderBy("created_at", "asc")->get();
        $previous = null;
        foreach ($history as $historicPartner) {
            if ($historicPartner->partner || $historicPartner->company) {
                $currentTime = new Carbon($historicPartner->created_at);
                $currentPartner = Kontakt::find($historicPartner->partner);
                $currentCompany = Kontakt::find($historicPartner->company);
                $partners[] = [
                    "id" => $historicPartner->id,
                    "company" => $currentCompany,
                    "contact_person" => $currentPartner,
                    "from" => $previous != null ? $previous->addDay()->format('d.m.Y') : "",
                    "until" => $currentTime->format('d.m.Y'),
                ];
                $previous = $currentTime->clone();
            }
        }

        if ($schuler->kontakt_id != null || $schuler->company_id != null) {
            $currentPartner = Kontakt::find($schuler->kontakt_id);
            $currentCompany = Kontakt::find($schuler->company_id);
            $newestDataSet = [
                "id" => "-1",
                "company" => $currentCompany,
                "contact_person" => $currentPartner,
                "from" => $previous != null ? $previous->addDay()->format('d.m.Y') : "",
                "until" => ""
            ];
            $partners[] = $newestDataSet;

            $partners = array_reverse($partners);
            return parent::createJsonResponse("student partner history", false, 200, ["partners" => $partners, "current" => $newestDataSet]);
        }

        return parent::createJsonResponse("student partner history", false, 200, ["partners" => $partners]);
    }

    public function updatePraxis($student_id, Request $request)
    {
        if (!$request->company_id)
            return $this->createJsonResponse("not enough information given", true,406);

        Log::info("Called updatePraxis for ".$student_id." with params".$request->input("company_id")." / ".$request->input("contact_id"));

        $schuler = Schuler::findOrFail($student_id);
        if ($schuler->kontakt_id != null || $schuler->company_id != null) {
            DB::table('partner_history')->insert([
                "schuler" => $schuler->id,
                "partner" => $schuler->kontakt_id,
                "company" => $schuler->company_id,
                "created_at" => new \DateTime()
            ]);
        }
        $schuler->kontakt_id = $request->input("contact_id");
        $schuler->company_id = $request->input("company_id");

        $schuler->save();
        return self::getPraxisWerdegang($student_id, $request);
    }

    public function practialUpdateEntry($student_id, Request $request)
    {
        if (!$request->entry_id)
            return $this->createJsonResponse("not enough information given", true,406);

        Log::info("Called practialUpdateEntry for ".$student_id." / ".$request->input("entry_id")." with params".$request->input("company_id")." / ".$request->input("contact_id"));

        $schuler = Schuler::findOrFail($student_id);
        if($request->entry_id == "-1")
        {
            $schuler->kontakt_id = $request->input("contact_id");
            $schuler->company_id = $request->input("company_id");
            $schuler->save();
        } else {
            DB::table('partner_history')->where([
                "schuler" => $student_id,
                "id" => $request->entry_id,
            ])->update([
                "partner" => $request->input("contact_id"),
                "company" => $request->input("company_id"),
                "created_at" => Carbon::parse($request->input("end_date"))->toDateTime()
            ]);
        }


        return self::getPraxisWerdegang($student_id, $request);
    }

    public function deletePraxis($student_id, Request $request)
    {
        if (!$request->entry_id)
            return $this->createJsonResponse("not enough information given", true,406);

        DB::table('partner_history')->where([
            "schuler" => $student_id,
            "id" => $request->entry_id,
        ])->delete();
        return self::getPraxisWerdegang($student_id, $request);
    }

    public function getStudienverlauf($student_id, Request $request)
    {
        $schuler = Schuler::findOrFail($student_id);

        $studInformation = $schuler->studyInformation();
        $studInformation->each->loadAll();

        $study_time = DB::select("select min(stupla_schuljahrs.start) as start_study, max(stupla_schuljahrs.ende) as end_study FROM stupla_schuljahrs, stupla_study_progress_entries WHERE stupla_study_progress_entries.schuljahr_id = stupla_schuljahrs.id AND stupla_study_progress_entries.schuler_id = ".$schuler->id);
        if(count($study_time) > 0)
        {
            $study_time = $study_time[0];
        }



        if(count($studInformation) == 0)
        {
            $actions = ["matriculation",
                "manualEntry","immaAsGuestStudent"];
        } else {
            if($studInformation->last()->status == "re-registrants" || $studInformation->last()->status == "freshmen" || $studInformation->last()->status == "extended"  || $studInformation->last()->status == "downgrading" )
            {
                $actions = [
                    "changeStudyDays",
                    "downgrading",
                    "vacation",
                    "changePlaceOfStudy",
                    "changeStudy",
                    "changeStudyDirection",
                    "extendStudy",
                    "cancelStudy",
                    "manualEntry"
                ];
            } else {
                $actions = ["matriculation",
                    "manualEntry","immaAsGuestStudent"];
            }

            if(Carbon::now()->subMonth()->isBefore(Carbon::parse($studInformation->first()->schuljahr->start)))
            {
                $actions[] = "revocation";
            }

            if(Carbon::now()->isAfter(Carbon::parse($studInformation->last()->schuljahr->start)) && $studInformation->last()->status != "finish"  )
            {
                $actions[] = "finishStudy";
            }
        }

        if($schuler->guest_student != "none")
        {
            $actions[] = "changeFromGuestStudent";
        }


        $permission = [
            "can_add_studyprogress",
            "can_edit_studyprogress",
            "can_remove_studyprogess"
        ];

        return parent::createJsonResponse("studiumsverlauf ", false, 200, [ "permissions" => $permission, "actions" => $actions, "study_time" => $study_time, "study_entries" => $studInformation]);
    }

    public function updateStudienverlaufEintrag($student_id, $entry_id, Request $request)
    {
        $student = Schuler::findOrFail( $student_id);
        $kohorte = Kohorte::findOrFail( $request->input("kohorte_id"));
        $fachrichtung = $request->input("directions_of_study");
        $attributes = [];
        if($fachrichtung != null && is_array($fachrichtung))
        {
            $attributes = [ "direction_of_study" => $fachrichtung ];
        }

        DB::table('study_progress_entries')->where([
            'schuler_id' => $student_id,
            'id' => $entry_id
        ])->update([
            'notes' => $request->input("notes"),
            'changed_by' => $request->input("changed_by"),
            'fs' => $request->input("fs"),
            'hs' => $request->input("hs"),
            'kohorte_id' => $request->input("kohorte_id"),
            'klasse_id' => $request->input("klasse_id"),
            'status' => $request->input("status"),
            'study_attributes' => json_encode($attributes)
        ]);

        $student->schulen()->sync([$kohorte->schuljahr->schule->id]);
        return $this->getStudienverlauf($student_id, $request);
    }

    public function manualEntry($student_id, Request $request)
    {
        $student = Schuler::findOrFail($student_id);
        $school_year = Schuljahr::find($request->input("year_id"));
        $fs = $request->input("fs");
        $hs = $request->input("hs");
        $kohorte = Kohorte::find($request->input("kohorte_id"));
        $status = $request->input("status");
        $klasse = Klasse::find($request->input("klasse_id"));

        DB::table('study_progress_entries')->where([
            'schuler_id' => $student_id,
            'schuljahr_id' => $school_year->id
        ])->delete();

        $fachrichtung = $request->input("directions_of_study");
        $attributes = [];
        if($fachrichtung != null && is_array($fachrichtung))
        {
            $attributes = [ "direction_of_study" => $fachrichtung ];
        }

        DB::table('study_progress_entries')->insert([
            'schuler_id' => $student_id,
            'schuljahr_id' => $school_year->id,
            'fs' => $fs,
            'hs' => $hs,
            'kohorte_id' => $kohorte->id,
            'status' => $status,
            'klasse_id' => $klasse ? $klasse->id : null,
            'notes' => $request->input("notes"),
            'changed_by' => $request->input("changed_by"),
            'study_attributes' => json_encode($attributes)
        ]);

        $student->schulen()->sync([$kohorte->schule->id]);

        return $this->studentDetailed($student_id, $request);
    }

    public function deleteStudienverlaufEintrag($student_id, $entry_id, Request $request)
    {
        DB::table('study_progress_entries')->where([
            'schuler_id' => $student_id,
            'id' => $entry_id
        ])->delete();

        return $this->getStudienverlauf($student_id, $request);
    }

    public function getCurriculum($student_id, Request $request)
    {
        if ($request->input("year_id") == null) {
            return parent::createJsonResponse("please provide your current school year", true, 400);
        }
        $schuler = Schuler::findOrFail($student_id);
        $sutdyInformation = $schuler->getCurrentStudyInformation($request->input("year_id"));
        if($sutdyInformation == null)
        {
            $sutdyInformation = $schuler->getLastStudyInformation();
        }
        if ($sutdyInformation == null) {
            return parent::createJsonResponse("the student has no study information ", false, 200, ["studyInformation" => null, "kohorte" => null, "curriculum" => null]);
        }
        $sutdyInformation->load('kohorte');
        $kohorte = $sutdyInformation->kohorte;
        if($kohorte == null)
            return parent::createJsonResponse("kohorte ", false, 200, ["studyInformation" => $sutdyInformation, "kohorte" => null, "curriculum" => null]);
        $lehrplan = $kohorte->lehrplan;

        if($request->has("with_grades") && $request->input("with_grades"))
            $lehrplan->loadLehrplanEinheitenWithGrades($schuler->id,$sutdyInformation->getDirectionOfStudyAttribute());
        else
            $lehrplan->append('lehrplanEinheiten');

        return parent::createJsonResponse("kohorte ", false, 200, ["studyInformation" => $sutdyInformation, "kohorte" => $kohorte, "curriculum" => $lehrplan]);
    }

    public function presences($student_id, Request $request)
    {
        if ($request->input("year_id") == null) {
            return parent::createJsonResponse("please provide your current school year", false, 200);
        }
        $schuler = Schuler::findOrFail($student_id);
        $fehlzeit_typs = FehlzeitTyp::where("schuljahr_id", "=", $request->input("year_id"))->get();
        return parent::createJsonResponse("kohorte ", false, 200, ["class_book_participation" => $schuler->klassenbuchTeilnahme, "absences_types" => $fehlzeit_typs]);
    }


    public function getGradesForStudent($student_id, Request $request)
    {
        $schuler = Schuler::find($student_id);
        $schule = $schuler->schulen->first();
        $schuljahr = $schule->getCurrentSchoolYear();
        $currentStudy = $schuler->getCurrentStudyInformation($schuljahr->id);
        if ($currentStudy == null) {
            $currentStudy = $schuler->getLastStudyInformation();
        }
        $lehrplan_id = $currentStudy->lehrplan()->id;
        if($request->input("lehrplan_id") != null)
        {
            $lehrplan_id = $request->input("lehrplan_id");
        }

        $lehrplane = Lehrplan::whereIn("id",DB::table("lehrplan_einheits")
            ->whereIn("module_id",DB::table("notes")->where('schuler_id', '=', $student_id)
                ->where("model_type","=","modul")->pluck("model_id"))
            ->pluck("lehrplan_id"))->get();

        if ($request->input("mode") == "curricula") {

            $semesters = DB::table("modul_fach_curiculum")
                ->where("lehrplan_id", "=", $lehrplan_id)
                ->groupBy("semester_occurrence")->pluck("semester_occurrence");

            $sections = [];
            foreach ($semesters as $semester)
            {
                $section = [];
                $section["fs"] = $semester;

                // module for this fs
                $modules = Module::whereIn("id",DB::table("modul_fach_curiculum")
                    ->where("lehrplan_id", "=", $lehrplan_id)
                    ->where("semester_occurrence","=",$semester)->pluck("module_id"))->get();

                $modules_with_notes = [];
                foreach ($modules as $modul)
                {
                    $modules_with_note = [];
                    $modules_with_note["modul"] = $modul;
                    $modules_with_note["_childern"] = Note::with("schuljahr")->where('schuler_id', '=', $student_id)->where('model_type', "=","modul")
                        ->where("model_id",$modul->id)->orderBy("version DESC, datum DESC")->get();
                }
                $section["_childern"] = $modules_with_notes;

                $sections[] = $section;
            }

            return parent::createJsonResponse("grades for student", false, 200, ["curricula" => $lehrplane, "sections" => $sections,  "selected_curriculum_id" => $lehrplan_id]);

        } else {
            $noten = Note::with("schuljahr")->where('schuler_id', '=', $student_id)->whereIn('model_type', ["modul"])
                ->whereIn("model_id",DB::table("modul_fach_curiculum")
                    ->where("lehrplan_id", "=", $lehrplan_id)
                    ->pluck("module_id")
                )
                ->orderByRaw('model_type, model_id, version DESC, datum DESC')
                ->get();



            $noten->each->append("belongsObject");
            //$noten->each->partNoten->each->append('linkedSubjects');
            $noten->each->append("partNoten");


            return parent::createJsonResponse("grades for student", false, 200, ["curricula" => $lehrplane, "grades" => $noten, "selected_curriculum_id" => $lehrplan_id]);
        }
    }

    public function gradesOverview($student_id, Request $request)
    {
        $schuljahr_ids = Schuljahr::where("start","<=", \Carbon\Carbon::now())->where("ende",">=",Carbon::now())->pluck("id");
        $lastNotenCache = NotenCache::where("schuler_id", "=", $student_id)->whereIn("schuljahr_id",$schuljahr_ids)->first();
        return parent::createJsonResponse("grades for student", false, 200, ["notenCache" => $lastNotenCache, "superview" => parent::user()->hasRole("Super-Administrator")]);
    }

    public function setGradeForStudent($student_id, Request $request)
    {

        /*
         * $request->object :
         *
         *
         * [
         *      partExamId => [ note : 3.4, feature : "text"],
         *      partExamId => [ points : 5, feature : "text"]
         * ]
         *
         */


        $user = parent::getUserForToken($request);
        $model_id = $request->input("model_id");
        $model_type = $request->input("model_type");
        if($request->has("schuljahr_id"))
            $schuljahr = Schuljahr::findOrFail($request->input("schuljahr_id"));
        else
            return parent::createJsonResponse("Fehler",true,401);

        $module = Module::findOrFail($model_id);
        $modulExam = ModulExam::findOrFail($request->input("exam_id"));

        $datum = $request->input("datum",null) != null ? Carbon::createFromTimestamp($request->input("datum")) : null;
        $object = $request->input("object");

        //  Create module note


        $noteModul = new Note;
        $noteModul->model_id = $module->id;
        $noteModul->model_type = "modul";
        $noteModul->exam_execution_id = null;
        $noteModul->schuler_id = $student_id;
        $noteModul->status = "examination_office";
        $note = Note::where("model_type","=","modul")->where("attest","=",0)->where("schuler_id","=",$student_id)->where("model_id","=",$module->id)->orderBy("version","DESC")->first();
        $last_version = 0;
        if($note != null)
            $last_version = $note->version;
        $noteModul->version = ($last_version + 1);
        $noteModul->schuljahr_id = $schuljahr->id;
        $noteModul->datum = $datum != null ? $datum->format("Y-m-d") : null;
        $noteModul->transfer = 1;
        $noteModul->save();

        // ende create Module note

        $points_sum = 0;
        $note_sum = 0;
        $all_passed = true;
        $has_attest = false;
        $modul_note = true;
        $faktor_note = 0;
        $bestanden_nicht_bestanden = false;
        $hasPoints = false;
        $hasNote = false;
        $feature_all = "";

        foreach ($object as $key => $value)
        {
            // $key is part exam id
            $partExam = ModulPartExam::findOrFail($key);
            if($partExam == null)
                continue;


            $points = array_key_exists("points",$value) ? $value["points"] : null;
            $note_value = array_key_exists("note",$value) ? $value["note"] : null;
            $feature = array_key_exists("feature",$value) ? $value["feature"] : "";
            $feature_all .= (" ".$feature);

            $noteDB = new Note;
            $noteDB->model_id = $modulExam->module_id;
            $noteDB->model_type = "modul_part";
            $noteDB->exam_execution_id = $partExam->id;
            $noteDB->modul_exam_execution_id = null;
            $noteDB->schuler_id =$student_id;
            $noteDB->status = "examination_office";

            if($partExam->rating == "points")
            {
                // check max points?
                if($points < 0 || $points >= $partExam->maxPoints)
                    Log::warning("note nicht im Bereich!");
                $noteDB->points = $points;
                $noteDB->note = null;
                $noteDB->maxPoints = $partExam->maxPoints;
            } else {
                $noteDB->note = $note_value;
                $noteDB->points = ($partExam->rating == "took_part" || $partExam->rating == "passed") ? -2 : null;
            }

            $noteDB->datum = \Carbon\Carbon::parse($partExam->start)->format("Y-m-d");
            $noteDB->bemerkung = $feature;
            $noteDB->transfer = 1;

            $noteDB->linked_subjects_id = $partExam->subjects->pluck("id")->join(",");

            $noteDB->belongs_to_note = $noteModul->id;
            $noteDB->save();

            // Notenberechnung !!!

            if($partExam->rating == "took_part" || $partExam->rating == "passed")
            {
                if($noteDB->note == 5 || $noteDB->attest)
                    $all_passed = false;
                $bestanden_nicht_bestanden = true;
                // ignore for further calculation
                continue;
            }


            if($partExam->rating == "points")
            {
                $hasPoints = true;
                if($noteDB->points === null) {
                    $modul_note = false;
                }

                if($noteDB->points <= 0)
                    continue;


                $points_calc = $noteDB->points / $partExam->maxPoints * $partExam->percent;
                $points_sum += $points_calc;
            }

            if($partExam->rating == "thesis" || $partExam->rating == "graded")
            {
                $hasNote = true;
                $note_calc = $noteDB->note * $partExam->percent / 100;
                $note_sum += $note_calc;
                $faktor_note += $partExam->percent;
            }
        }

        // calculate module note

        $noteModul->bemerkung = $feature_all;
        if(($modulExam->need_all_parts_passed && !$all_passed) || $has_attest || ($points_sum == 0 && $note_sum == 0))
        {
            $noteModul->note = 5;
            $noteModul->points = -1;
        }
        $noteModul->attest = $has_attest;

        if($bestanden_nicht_bestanden && (!$hasPoints && !$hasNote))
        {
            if($all_passed)
            {
                $noteModul->note = 1;
                $noteModul->points = -1;
            } else {
                $noteModul->note = 5;
                $noteModul->points = -1;
            }
        } else {
            $note_points = 0;
            if ($points_sum >= 0) {
                $note_points = ExamExecutionController::pointsToNote($points_sum);
            }
            if ($note_sum != 0) {
                $noteModul->note = $note_sum * ($faktor_note / 100) + (1 - ($faktor_note / 100)) * $note_points;
                $noteModul->points = ExamExecutionController::noteToPoints($noteModul->note);
            } else {

                $noteModul->note = $note_points;
                $noteModul->points = $points_sum;
            }
        }
        if($noteModul->note <= 4 && !$has_attest)
        {
            DB::table("modul_exam_execution_schuler")->where("schuler_id","=",$student_id)
                ->whereIn("modul_exam_execution_id",DB::table("modul_exam_executions")->where("modul_exam_id","=",$modulExam->id)->where("status","!=","released")->pluck("id"))->delete();
        }

        $noteModul->save();

        return parent::createJsonResponse("grade for student saved",false, 200, [ "grade" => $noteModul]);
    }

    public function deleteGradeForStudent($student_id, $grade_id, Request $request)
    {
        $user = parent::getUserForToken($request);

        $note = Note::where("id","=",$grade_id)->where("schuler_id","=",$student_id)->first();
        if($note == null)
            return parent::createJsonResponse("invalid params", true, 400);

        $note->delete();

        return parent::createJsonResponse("deleted",false, 200);
    }

    public function updateGradeForStudent($student_id, $grade_id, Request $request)
    {
        $note_request = $request->input("note");

        $note = Note::where("id","=",$grade_id)->where("schuler_id","=",$student_id)->first();
        if(!$note)
            return parent::createJsonResponse("grade he do no existing.", true, 400);

        $note->schuler_id = $student_id;
        $note->status = $note_request["status"];
        $note->note = $note_request["note"];
        $note->points = $note_request["points"];
        $note->version = $note_request["version"];
        if(array_key_exists("datum", $note_request) && $note_request["datum"] != null && $note_request["datum"] != "")
            $note->datum = \Carbon\Carbon::createFromTimestamp($note_request["datum"])->format("Y-m-d");
        else
            $note->datum = null;
        $note->bemerkung = $note_request["bemerkung"];
        $note->transfer = $note_request["transfer"];
        $note->status = $note_request["status"];
        $note->attest = $note_request["attest"];
        $note->consider_current_curriculum = $note_request["consider_current_curriculum"];
       /*
        $note->title = array_key_exists("title",$note_request)? $note_request["title"] : null;

        $away = $note_request["away"];
        $note->attest = $away == "yesWith" ? 1 : 0;
        if($away != "no" && $note->attest == 0)
        {
            $note->note = 5;
            $note->points = -1;
        }
       */
        $note->save();

      //  return $this->getGradesForStudent($student_id,$request);
        return parent::createJsonResponse("updated",false, 200);
    }

    // Actions

    public function immatrikulation($student_id, Request $request)
    {
        $schuler = Schuler::find($student_id);
        if ($schuler == null) {
            return parent::createJsonResponse("please provide a valid student id", true, 400);
        }
        $kohorte = Kohorte::find($request->input("kohorte_id"));
        if ($kohorte == null) {
            return parent::createJsonResponse("the kohorte can not be null", true, 400);
        }
        $klasse = Klasse::find($request->input("course_id"));
        $fachrichtungen = $request->input("directions_of_study");
        try {
            $schuler->immaToStudiengang($kohorte, $request->input("fs", 1), $request->input("hs", 1), $fachrichtungen, $klasse);
        } catch (ModalTransactionException $exception) {
            return parent::createJsonResponse("ModalTransactionError " . $exception->getMessage(), true, 406, ["message" => $exception->getMessage()], -2);
        }
        return $this->studentDetailed($student_id, $request);
    }

    public function matriculationMultiple(Request $request)
    {
        $student_ids = $request->input("student_ids");
        $students = [];
        foreach ($student_ids as $student_id) {
            $schuler = Schuler::find($student_id);
            if ($schuler == null) {
                return parent::createJsonResponse("please provide a valid student id", true, 400);
            }
            $kohorte = Kohorte::find($request->input("kohorte_id"));
            if ($kohorte == null) {
                return parent::createJsonResponse("the kohorte can not be null", true, 400);
            }
            $fachrichtungen = $request->input("directions_of_study");
            try {
                $schuler->immaToStudiengang($kohorte, $request->input("fs", 1), $request->input("hs", 1), $fachrichtungen);
            } catch (ModalTransactionException $exception) {
                return parent::createJsonResponse("ModalTransactionError " . $exception->getMessage(), true, 406, ["message" => $exception->getMessage()], -2);
            }
            $students[] = $this->studentDetailedWithResponse($student_id, $request)["student"];
        }
        return parent::createJsonResponse("students", false, 200,["students" => $students]);
    }

    public function widerruf($student_id, Request $request)
    {
        $schuler = Schuler::find($student_id);
        if ($schuler == null) {
            return parent::createJsonResponse("please provide a valid student id", true, 400);
        }
        $extendUntil = Carbon::createFromTimestamp($request->input("cancel_date"));
        try {
            $schuler->widerruf($extendUntil, parent::getAdministationUser());
        } catch (ModalTransactionException $exception) {
            return parent::createJsonResponse("ModalTransactionError " . $exception->getMessage(), true, 406, ["message" => $exception->getMessage()], -2);
        }
        return $this->studentDetailed($student_id, $request);
    }

    public function changeStudientage($student_id, Request $request)
    {
        $schuler = Schuler::find($student_id);
        if ($schuler == null) {
            return parent::createJsonResponse("please provide a valid student id", true, 400);
        }
        $schuljahr = Schuljahr::find($request->input("year_id"));
        if ($schuljahr == null) {
            return parent::createJsonResponse("the year can not be null", true, 400);
        }
        $tage = $request->input("days");
        // anhand der Klasse wissen wir ab wann es gelten soll + die Tage
        try {
            $schuler->changeStudientage($schuljahr, $tage);
        } catch (ModalTransactionException $exception) {
            return parent::createJsonResponse("ModalTransactionError " . $exception->getMessage(), true, 406, ["message" => $exception->getMessage()], -2);
        }
        return $this->studentDetailed($student_id, $request);
    }

    public function downgrading($student_id, Request $request)
    {
        $schuler = Schuler::find($student_id);
        if ($schuler == null) {
            return parent::createJsonResponse("please provide a valid student id", true, 400);
        }
        $schuljahr = Schuljahr::find($request->input("year_id"));
        if ($schuljahr == null) {
            return parent::createJsonResponse("the year can not be null", true, 400);
        }
        $klasse = Klasse::find($request->input("klasse_id"));
        if ($klasse == null) {
            return parent::createJsonResponse("the klasse can not be null", true, 400);
        }
        $number_of_downgrades = $request->input("number_of_downgrades");
        try {
            $schuler->downgrading($schuljahr, $number_of_downgrades, $request->input("reason"),$klasse);
        } catch (ModalTransactionException $exception) {
            return parent::createJsonResponse("ModalTransactionError " . $exception->getMessage(), true, 406, ["message" => $exception->getMessage()], -2);
        }
        return $this->studentDetailed($student_id, $request);
    }

    public function changeStudyDirection($student_id, Request $request)
    {
        $schuler = Schuler::find($student_id);
        if ($schuler == null) {
            return parent::createJsonResponse("please provide a valid student id", true, 400);
        }
        $fachrichtungen = $request->input("directions_of_study");
        if ($fachrichtungen == null) {
            return parent::createJsonResponse("the direction of study can not be null", true, 400);
        }
        try {
            $schuler->changeStudyDirection($fachrichtungen);
        } catch (ModalTransactionException $exception) {
            return parent::createJsonResponse("ModalTransactionError " . $exception->getMessage(), true, 406, ["message" => $exception->getMessage()], -2);
        }
        return $this->studentDetailed($student_id, $request);
    }

    public function vacation($student_id, Request $request)
    {
        $schuler = Schuler::find($student_id);
        if ($schuler == null) {
            return parent::createJsonResponse("please provide a valid student id", true, 400);
        }
        $schuljahr = Schuljahr::find($request->input("year_id"));
        if ($schuljahr == null) {
            return parent::createJsonResponse("the year can not be null", true, 400);
        }
        $reason = $request->input("reason");
        $note = $request->input("note");
        try {
            $schuler->vacation($schuljahr, $reason, $note);
        } catch (ModalTransactionException $exception) {
            return parent::createJsonResponse("ModalTransactionError " . $exception->getMessage(), true, 406, ["message" => $exception->getMessage()], -2);
        }
        return $this->studentDetailed($student_id, $request);
    }

    public function changeStudienort($student_id, Request $request)
    {
        $schuler = Schuler::find($student_id);
        if ($schuler == null) {
            return parent::createJsonResponse("please provide a valid student id", true, 400);
        }
        $schuljahr = Schuljahr::find($request->input("year_id"));
        if ($schuljahr == null) {
            return parent::createJsonResponse("the year can not be null", true, 400);
        }
        $standort = Schule::find($request->input("new_standort_id"));
        if ($standort == null) {
            return parent::createJsonResponse("the new standort can not be null", true, 400);
        }

        try {
            $schuler->changeStudienort($schuljahr, $standort);
        } catch (ModalTransactionException $exception) {
            return parent::createJsonResponse("ModalTransactionError " . $exception->getMessage(), true, 406, ["message" => $exception->getMessage()], -2);
        }

        return $this->studentDetailed($student_id, $request);
    }

    public function changeStudiengang($student_id, Request $request)
    {
        $schuler = Schuler::find($student_id);
        if ($schuler == null) {
            return parent::createJsonResponse("please provide a valid student id", true, 400);
        }
        $schuljahr = Schuljahr::find($request->input("year_id"));
        if ($schuljahr == null) {
            return parent::createJsonResponse("the year can not be null", true, 400);
        }
        $newKohorte = Kohorte::find($request->input("new_kohorte"));
        if ($newKohorte == null) {
            return parent::createJsonResponse("the new kohorte can not be null", true, 400);
        }
        $fachrichtungen = $request->input("directions_of_study");

        try {
            $schuler->changeStudiengang($newKohorte, $schuljahr, $fachrichtungen);
        } catch (ModalTransactionException $exception) {
            return parent::createJsonResponse("ModalTransactionError " . $exception->getMessage(), true, 406, ["message" => $exception->getMessage()], -2);
        }

        return $this->studentDetailed($student_id, $request);
    }

    public function extendStudiengang($student_id, Request $request)
    {
        $extendUntil = Carbon::createFromTimestamp($request->input("extend_until"));
        $lastDateOfExam = Carbon::createFromTimestamp($request->input("last_date_examination"));
        $schuler = Schuler::find($student_id);
        if ($schuler == null) {
            return parent::createJsonResponse("please provide a valid student id", true, 400);
        }
        try {
            $schuler->extendStudiengang($extendUntil, $lastDateOfExam);
        } catch (ModalTransactionException $exception) {
            return parent::createJsonResponse("ModalTransactionError " . $exception->getMessage(), true, 406, ["message" => $exception->getMessage()], -2);
        }
        return $this->studentDetailed($student_id, $request);
    }

    public function cancelStudiengang($student_id, Request $request)
    {
        $schuler = Schuler::find($student_id);
        if ($schuler == null) {
            return parent::createJsonResponse("please provide a valid student id", true, 400);
        }
        $extendUntil = Carbon::createFromTimestamp($request->input("cancel_date"));
        $reason = $request->input("reason");
        $notes = $request->input("notes");
        try {
            $schuler->cancelStudiengang($reason, $extendUntil, $notes);
        } catch (ModalTransactionException $exception) {
            return parent::createJsonResponse("ModalTransactionError " . $exception->getMessage(), true, 406, ["message" => $exception->getMessage()], -2);
        }
        return $this->studentDetailed($student_id, $request);
    }

    public function finishStudium($student_id, Request $request)
    {
        $schuler = Schuler::find($student_id);
        if ($schuler == null) {
            return parent::createJsonResponse("please provide a valid student id", true, 400);
        }
        $finishDate = Carbon::createFromTimestamp($request->input("finish_date"));
        try {
            $schuler->finishStudium($finishDate);
        } catch (ModalTransactionException $exception) {
            return parent::createJsonResponse("ModalTransactionError " . $exception->getMessage(), true, 406, ["message" => $exception->getMessage()], -2);
        }
        return $this->studentDetailed($student_id, $request);
    }

    public function changeFromGastStudent($student_id, Request $request)
    {
        $schuler = Schuler::find($student_id);
        if ($schuler == null) {
            return parent::createJsonResponse("please provide a valid student id", true, 400);
        }
        $finishDate = Carbon::createFromTimestamp($request->input("change_date"));
        try {
            $schuler->changeFromGastStudent($finishDate);
        } catch (ModalTransactionException $exception) {
            return parent::createJsonResponse("ModalTransactionError " . $exception->getMessage(), true, 406, ["message" => $exception->getMessage()], -2);
        }
        return $this->studentDetailed($student_id, $request);
    }

    public function immaAsGuestStudent($student_id, Request $request)
    {
        $schuler = Schuler::find($student_id);
        if ($schuler == null) {
            return parent::createJsonResponse("please provide a valid student id", true, 400);
        }
        $kohorte = Kohorte::find($request->input("kohrte_id"));
        if ($kohorte == null) {
            return parent::createJsonResponse("the kohorte can not be null", true, 400);
        }
        try {
            $schuler->immaAsGuestStudent($kohorte);
        } catch (ModalTransactionException $exception) {
            return parent::createJsonResponse("ModalTransactionError " . $exception->getMessage(), true, 406, ["message" => $exception->getMessage()], -2);
        }
        return $this->studentDetailed($student_id, $request);
    }

    public function modalInformation($student_id, Request $request)
    {
        $schuler = Schuler::find($student_id);
        if ($schuler == null) {
            return parent::createJsonResponse("please provide a valid student id", true, 400);
        }

        // this call loads the additional modal information
        $standorte = Schule::all();
        $standorte->each->load('kohorten');
        $standorte->each->load('schuljahre');
        $standorte->each->load('studiengange');
        $standorte->each->load('studiengange.lehrplan');

        foreach ($standorte as $standort) {
            foreach ($standort->kohorten as $kohorte) {
                $kohorte->planing_groups = $kohorte->getPlaningGroups($request->input("year_id"));
            }
        }

        $curricula = Lehrplan::all();
        $curricula->each->append('direction_of_study');

        $semester_valid_study = Schuljahr::find(StudyProgressEntry::where('schuler_id','=',$schuler->id)->pluck("schuljahr_id"));
        $study_time = DB::select("select min(stupla_schuljahrs.start) as start_study, max(stupla_schuljahrs.ende) as end_study FROM stupla_schuljahrs, stupla_study_progress_entries WHERE stupla_study_progress_entries.schuljahr_id = stupla_schuljahrs.id AND stupla_study_progress_entries.schuler_id = ".$schuler->id);
        if(count($study_time) > 0)
        {
            $study_time = $study_time[0];
        }

        $studyInformation = $schuler->getCurrentStudyInformation($request->input("year_id"));
        $planing_groups_possible_switch_study_days = [];
        if($studyInformation != null) {
            $studyInformation->loadAll();
            $planing_groups_possible_switch_study_days = Klasse::where('type','=','planning_group')->where('kohorte_id', '=', $studyInformation->kohorte_id)->get();
        }

        return parent::createJsonResponse("modal information ", false, 200, [
            "planing_groups_possible_switch_study_days" =>  $planing_groups_possible_switch_study_days,
            "current_study_info" => $studyInformation,
            "study_time" => $study_time,
            "semester_valid_study" => $semester_valid_study,
            "standorte" => $standorte,
            "curricula" => $curricula]);
    }

    public function downgradingInformation($student_id, Request $request)
    {
        $schuler = Schuler::find($student_id);
        if ($schuler == null) {
            return parent::createJsonResponse("please provide a valid student id", true, 400);
        }
        $schuljahr = Schuljahr::find($request->input("year_id"));
        if ($schuljahr == null) {
            return parent::createJsonResponse("the year can not be null", true, 400);
        }

        $anzahl_semester = $request->input("number_of_downgrades");

        $kohrte = $schuler->getKohorteForDowngrade($schuljahr, $anzahl_semester);

        return parent::createJsonResponse("modal information ", false, 200, [ "kohorte" => $kohrte, "planing_groups" => $kohrte->getPlaningGroups($schuljahr->id) ]);
    }

    public function changeStudyInformation($student_id, Request $request)
    {
        $schuler = Schuler::find($student_id);
        if ($schuler == null) {
            return parent::createJsonResponse("please provide a valid student id", true, 400);
        }
        $schuljahr = Schuljahr::find($request->input("year_id"));
        if ($schuljahr == null) {
            return parent::createJsonResponse("the year can not be null", true, 400);
        }
        $kohrte = Kohorte::find($request->input("cohort_id"));
        if ($kohrte == null) {
            return parent::createJsonResponse("the kohorteeeee can not be null", true, 400);
        }

        $schuljahr = Schuljahr::where("name",'=',$schuljahr->name)->where("schule_id","=",$kohrte->schule_id)->first();
        if ($schuljahr == null) {
            return parent::createJsonResponse("the year can not be null", true, 400);
        }

        return parent::createJsonResponse("modal information ", false, 200, [ "kohorte" => $kohrte, "planing_groups" => $kohrte->getPlaningGroups($schuljahr->id) ]);
    }

    public function uploadProfilImage($student_id, Request $request)
    {
        $schuler = Schuler::find($student_id);

        if($request->hasfile('image'))
        {
            Storage::disk('public')->delete('/images/students/'.$schuler->image.".png");

            $file = $request->file('image');
            $image = (new ImageManager);
            $image = $image->make($file->getRealPath());
            $image = $image->fit("250");

            $name = str_random(32);
            Storage::disk('public')->put('/images/students/'.$name.".png", $image->stream('png', 90));
            $schuler->image = $name;
            $schuler->save();
        }

        return $this->studentDetailed($student_id, $request);
    }


    public function addToClass(Request $request)
    {
        $student_ids = $request->input("student_ids");
        $class_id = $request->input("course_id");
        $klasse = Klasse::find($class_id);
        if($klasse == null)
            return parent::createJsonResponse("the course was not found", true, 400);

        $klasse->schuler()->syncWithoutDetaching($student_ids);

        return parent::createJsonResponse("the members are added", false, 200,["klasse" => $klasse]);
    }

    public function exmatriculateRegularStapel(Request $request)
    {
        $student_ids = $request->input("student_ids");
        $date = Carbon::createFromTimestamp($request->input("date"));
        foreach ($student_ids as $student_id)
        {
            $student = Schuler::find($student_id);
            if($student != null)
            {
                $student->finishStudium($date);
            }
        }
        return parent::createJsonResponse("done", false, 200,[]);
    }

    public function exmatriculateRegularGuest(Request $request)
    {
        $student_ids = $request->input("student_ids");
        foreach ($student_ids as $student_id)
        {
            $student = Schuler::find($student_id);
            if($student != null)
            {
                $student->changeFromGastStudent(Carbon::now());
            }
        }
        return parent::createJsonResponse("done", false, 200,[]);
    }

    public function report($student_id, Request $request)
    {
        $schuler = Schuler::find($student_id);
        if ($schuler == null) {
            return parent::createJsonResponse("please provide a valid student id", true, 400);
        }

        $year = Schuljahr::find($request->input("year_id"));


        $currentStudyInformatio = $schuler->getCurrentStudyInformation($year->id);
        if($currentStudyInformatio != null)
        {

        }

        if($schuler->studyInformation->count() > 0)
        {
            // exam!
        }

        return parent::createJsonResponse("the members are added", false, 200,["klasse" => $klasse]);
    }


    public function getStudentExamDates($student_id, Request $request)
    {
        $student_id = $request->input("student_id");
        $studentInstance = Schuler::findOrFail($student_id);

        $examDates = ExamExecutionDate::whereIn("modul_exam_execution_id", DB::table("modul_exam_execution_schuler")->where("schuler_id","=",$studentInstance->id)->pluck("modul_exam_execution_id")->toArray())
            ->where(function ($query) {
                $query->where("status", "=", "student")->orWhere("status","=","public");
            })->with("rooms")->with("teacher")->get();

        $examDates->each->append("examParts");

        return parent::createJsonResponse("grades for student",false, 200, [ "dates" => $examDates]);
    }
}
