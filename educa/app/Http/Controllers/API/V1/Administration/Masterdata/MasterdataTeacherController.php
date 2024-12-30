<?php
namespace App\Http\Controllers\API\V1\Administration\Masterdata;

use App\AdditionalInfo;
use App\Http\Controllers\API\V1\Administration\AdministationApiController;
use App\Lehrer;
use App\LessonPlan;
use App\Schule;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use StuPla\CloudSDK\formular\models\Formular;

class MasterdataTeacherController extends AdministationApiController
{

    public function teacherDetailed($teacherId, Request $request)
    {
        $teacher = Lehrer::findOrFail($teacherId);
        $addInfo = $teacher->getAddInfo();
        foreach($addInfo->toArray() as $key=>$value)
        {
            if( $key !== "id" && $key !== "email")
                $teacher->$key = $value;
        }

        $teacher->load('schule');
        $teacher->load('schulen');
        $teacher->load('faecher.studies');
        $teacher->append('iba_settings');

        $formulare = [];
        $forms = [];
        $formular_templates = [];
        $formulareDB = Formular::whereIn('id', explode(",", $teacher->schulen->first()->getEinstellungen("formulare_dozent", "")))->get();
        foreach ($formulareDB as $formular) {
            $forms[$formular->id] = $formular;
            $formulare[$formular->id] = json_decode($teacher->getLatestFormulaDataFor($formular));
            $formular_templates[$formular->id] = json_decode($formular->getLastRevisionAttribute()->data);
        }

        return parent::createJsonResponse("Wer das liest ist kein Hacker sondern Webdesigner ;)!",false, 200, ["teacher" => $teacher, "forms" => $forms, "forms_data" => $formulare, "forms_templates" => $formular_templates]);
    }

    public function updateTeacher($teacherId, Request $request)
    {
        $teacher = Lehrer::findOrFail($teacherId);
        $addInfo = $teacher->getAddInfo();
        foreach($request->object as $key=>$value)
        {
            if($key != "id" && $key != "info_id" && $key != "personalnummer")
            {
                if($key == "birthdate")
                    $value = $value == null? null : Carbon::createFromTimestamp($value)->toDateTime();
                if(Schema::hasColumn($teacher->getTable(), $key))
                {
                    $teacher->$key = $value;
                }
                elseif(Schema::hasColumn($addInfo->getTable(), $key))
                {
                    $addInfo->$key = $value;
                }
            }
        }

        if( array_has($request->object, "subject_ids") )
        {
            $teacher->faecher()->sync($request->object["subject_ids"]);
        }

        if( array_has($request->object, "email") )
        {
            $teacher->email = $request->object["email"];
        }

        if( array_has($request->object, "studyplaces") && is_array($request->object["studyplaces"]) )
        {
            $teacher->schulen()->sync($request->object["studyplaces"]);
        }
        if( array_has($request->object, "main_studyplace") )
        {
            $teacher->schule_id = $request->object["main_studyplace"];
        }


        $addInfo->save();
        $teacher->save();

        return $this->teacherDetailed($teacherId, $request);
    }

    public function addTeacher(Request $request)
    {
        $teacher = new Lehrer;
        $addInfo = new AdditionalInfo;

        $school_ids = $request->school_ids;

        if(!$school_ids)
            return $this->createJsonResponse("No school ids given", true, 400);
        foreach($request->object as $key=>$value)
        {
            if($key != "id" && $key != "info_id" && $key != "personalnummer")
            {
                if(Schema::hasColumn($teacher->getTable(), $key))
                {
                    $teacher->$key = $value;
                }
                elseif(Schema::hasColumn($addInfo->getTable(), $key))
                {
                    $addInfo->$key = $value;
                }
            }
        }
        $addInfo->save();
        $teacher->info_id = $addInfo->id;
        $teacher->save();
        $teacher->schulen()->sync(Schule::all());

        return $this->teacherDetailed($teacher->id,  $request);
    }

    public function updateSettings($teacherId, Request $request)
    {
        $teacher = Lehrer::findOrFail($teacherId);
        $extension = $teacher->iba_settings;

        if($request->status)
        {
            $teacher->status = $request->status;
            $teacher->save();
        }


        $obj = $request->input("settings");
        $extension->employment_type = $obj["employment_type"];

        if ($obj["employment_type"] == "permanent_employees") {
            $extension->contract_begin = null;
            $extension->contract_end = null;
        } else {
            $extension->contract_begin = !array_key_exists("contract_begin",$obj) || $obj["contract_begin"] == null ? null : Carbon::createFromTimestamp($obj["contract_begin"]);
            $extension->contract_end = !array_key_exists("contract_end",$obj) ||  $obj["contract_end"] == null ? null : Carbon::createFromTimestamp($obj["contract_end"]);
        }
        if (array_key_exists("victoria_contract",$obj) && $obj["victoria_contract"])
        {
            $extension->victoria_contract = true;
            $extension->contract_victoria_begin = $obj["contract_victoria_begin"] == null? null :Carbon::createFromTimestamp($obj["contract_victoria_begin"]);
            $extension->contract_victoria_end = $obj["contract_victoria_end"] == null? null :Carbon::createFromTimestamp($obj["contract_victoria_end"]);

        } else {
            $extension->contract_victoria_begin = null;
            $extension->contract_victoria_end = null;
            $extension->victoria_contract = false;
        }

        $extension->save();

        return $this->teacherDetailed($teacher->id,  $request);
    }


    public function teachingStakes($teacher_id, Request $request)
    {
        $lessonPlans = LessonPlan::whereHas('dozent', function($q) use($teacher_id) {
            $q->where('lehrer_id', $teacher_id);
        })->get();

        $data = [];
        foreach ($lessonPlans as $lessonPlan)
        {
            $entry = [];
            $entry["school_year"]  = $lessonPlan->draft->schuljahr;
            $entry["studiengang"] = "TODO";
            $entry["curricula"] = "TODO"; // planungsgruppe des unterrichts ->
            $entry["subject"] = $lessonPlan->fach;
            $entry["school"] = $entry["school_year"]->schule;
            $entry["should_units"] = 0;
            $entry["is_units"] = 0;
            $data[] = $entry;
        }

        return parent::createJsonResponse("nice einlagen",false, 200, ["data" => $data]);
    }

    public function setForm($student_id, Request $request)
    {
        $student = Lehrer::findOrFail($student_id);

        $form_id = $request["form_id"];
        $formular = Formular::findOrFail($form_id);
        $formular_revision_id = $formular->last_revision->id;

        $student->saveFormulaDataFor($formular_revision_id, json_encode($request["form_data"]));

        return $this->teacherDetailed($student_id, $request);
    }

    public function deleteTeacher($teacher_id, Request $request)
    {
        return parent::createJsonResponse("teacher deleted.", false, 200, Lehrer::findOrFail($teacher_id)->delete());
    }
}
