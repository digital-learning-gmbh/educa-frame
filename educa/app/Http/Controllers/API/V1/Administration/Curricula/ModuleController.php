<?php

namespace App\Http\Controllers\API\V1\Administration\Curricula;

use App\Http\Controllers\API\V1\Administration\AdministationApiController;
use App\Lehrplan;
use App\Module;
use App\ModulExam;
use App\ModulExamLabel;
use App\ModulPartExam;
use App\ModulPartExamLabel;
use App\Schule;
use App\Studium;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Reader\Xls\RC4;

class ModuleController extends AdministationApiController
{
    public function list(Request $request)
    {
        $school_id = $request->input("school_id");
        $school =  Schule::find($school_id);
        // TODO filter den standort
        return parent::createJsonResponse("modul",false, 200, [ "modules" => Module::with('studium')->get() ]);
    }

    public function add(Request $request)
    {
        if(Module::where('examination_number','=', $request->input("examination_number"))->exists())
            return $this->createJsonResponse("module already exists.", true, 403);

        if( !$request->study_ids )
            return $this->createJsonResponse("No IDs given", true, 404);
        $module = new Module;
        $module->name = $request->input("name", "Neues Modul ".date("H:i, d.m.Y"));
        $module->examination_number = $request->input("examination_number");
        $module->save();
        $module->studium()->sync($request->study_ids);


        return $this->details($module->id, $request);
    }

    public function details($module_id, Request $request)
    {
        $modul = Module::find($module_id);
        if($modul == null)
            return $this->createJsonResponse("module not found.", true, 404);

        if($modul->rules)
            $modul->rules = json_decode($modul->rules);
        $modul->load("subjects");
        $modul->load("studium");
        $modul->load("modulExams");
        $modul->load("modulExams.modulPartExams.subjects");
        if($request->input("lehrplan_id") && Lehrplan::find($request->input("lehrplan_id")) != null)
        {
            $lehrplan_id = $request->input("lehrplan_id");
            foreach ($modul->subjects as $subject)
            {
                $subject->semester_occurence = $subject->loadSemesterInformation($modul->id,$lehrplan_id);
            }
            $modul->allocated_exams = $modul->examsForLehrplan($lehrplan_id);
        }
        $examLabels = ModulExamLabel::all();
        return parent::createJsonResponse("modul details",false, 200, [ "module" => $modul, "examLabels" => $examLabels]);
    }

    public function update($module_id, Request $request)
    {
        $modul = Module::find($module_id);
        if($modul == null)
            return $this->createJsonResponse("module not found.", true, 404);

        $modulesObj = $request->input("object");
        $modul->name = $modulesObj["name"];
        $modul->examination_number = $modulesObj["examination_number"];
        $modul->ects = $modulesObj["ects"];

        // Katalogfelder
        $modul->participation_requirements = $modulesObj["participation_requirements"];
        $modul->recommended_knowledge = $modulesObj["recommended_knowledge"];
        $modul->qualification_goals = $modulesObj["qualification_goals"];
        $modul->particularities = $modulesObj["particularities"];
        $modul->literature = $modulesObj["literature"];
        $modul->manager_id = $modulesObj["manager_id"];
        $modul->study_presence = $modulesObj["study_presence"];
        $modul->study_pratical = $modulesObj["study_pratical"];
        $modul->study_self = $modulesObj["study_self"];
        $modul->visible_app = $modulesObj["visible_app"];

        // ENDE
        $modul->rules = $modulesObj["rules"];
        $modul->save();

        $modul->studium()->sync($modulesObj["studium"]);
        $modul->subjects()->sync($modulesObj["subjects"]);


        return $this->details($module_id, $request);
    }

    public function archive($module_id, Request $request)
    {
        $modul = Module::find($module_id);
        if($modul == null)
            return $this->createJsonResponse("module not found.", true, 404);

        $modul->archived = true;

        $modul->save();

        return parent::createJsonResponse("modul archived",false, 200, [ "modul" => $modul]);

    }

    public function addModulExam($module_id, Request $request)
    {
        $modul = Module::find($module_id);
        if($modul == null)
            return $this->createJsonResponse("module not found.", true, 404);

        $name = $request->input("name");
        $label_id = $request->input("modul_exam_label_id");

        if($label_id == null || $name == null)
            return $this->createJsonResponse("label and name -> not null.", true, 403);

        $modulExam = new ModulExam;
        $modulExam->name = $name;
        $modulExam->module_id = $modul->id;
        $modulExam->modul_exam_label_id = $label_id;
        $modulExam->need_all_parts_passed = $request->input("need_all_parts_passed");
        $modulExam->save();

        return $this->detailModulExam($module_id,$modulExam->id,$request);
    }

    public function detailModulExam($module_id, $module_exam_id, Request $request)
    {
        $modulExam = ModulExam::find($module_exam_id);
        if($modulExam == null)
            return $this->createJsonResponse("module exam not found.", true, 404);
        $modulExam->load("modulPartExams");
        $modulExam->load("modulPartExams.examPartLabel");
        $modulExam->load("modulPartExams.subjects");
        $modulExam->load("examLabel");
        $part_labels = $modulExam->examLabel->partLabels;

        return parent::createJsonResponse("modul exam details",false, 200, [ "moduleExam" => $modulExam, "part_labels" => $part_labels]);
    }

    public function updateModulExam($module_id, $module_exam_id, Request $request)
    {
        $modulExam = ModulExam::find($module_exam_id);
        if($modulExam == null)
            return $this->createJsonResponse("module exam not found.", true, 404);

        $name = $request->input("name");
        $label_id = $request->input("modul_exam_label_id");

        if($label_id == null || $name == null)
            return $this->createJsonResponse("label and name -> not null.", true, 403);

        $modulExam->name = $name;
        $modulExam->need_all_parts_passed = $request->input("need_all_parts_passed");
        if($modulExam->examLabel->count() == 0)
            $modulExam->modul_exam_label_id = $label_id;
        $modulExam->save();

        return $this->detailModulExam($module_id,$module_exam_id,$request);
    }

    public function deleteModulExam($module_id, $modul_exam_id, Request $request)
    {
        $modulExam = ModulExam::find($modul_exam_id);
        if($modulExam == null)
            return $this->createJsonResponse("module exam not found.", true, 404);

        foreach($modulExam->modulPartExams as $partExam)
        {
            $partExam->subjects()->sync([]);
            $partExam->delete();
        }

        $modulExam->delete();

        return parent::createJsonResponse("modul exam deleted",false, 200);
    }

    public function addModulPartExam($module_id, $module_exam_id, Request $request)
    {
        $modulExam = ModulExam::find($module_exam_id);
        if($modulExam == null)
            return $this->createJsonResponse("module exam not found.", true, 404);

        $modul = Module::find($module_id);
        if($modul == null)
            return $this->createJsonResponse("module not found.", true, 404);

        $modulPartExam = new ModulPartExam;
        $modulPartExam->modul_exam_id = $modulExam->id;
        $modulPartExam->percent = $request->input("percent");
        $modulPartExam->maxPoints = $request->input("maxPoints");
        $modulPartExam->rating = $request->input("rating");
        $modulPartExam->maxTry = $request->input("max_try");
        $modulPartExam->duration = $request->input("duration");
        $modulPartExam->modul_part_exam_label_id = $request->input("modul_part_exam_label_id");
        $modulPartExam->group = $request->input("group");

        $modulPartExam->save();

        if($request->has("subject_ids"))
            $modulPartExam->subjects()->sync($request->input("subject_ids"));

        return $this->detailModulExam($module_id,$module_exam_id,$request);
    }

    public function deleteModulPartExam($module_id, $module_exam_id, $module_part_id, Request $request)
    {
        $modulPartExam = ModulPartExam::find($module_part_id);
        if($modulPartExam == null)
            return $this->createJsonResponse("module part exam not found.", true, 404);

        $modulPartExam->delete();

        return $this->detailModulExam($module_id, $module_exam_id,$request);
    }

    public function updateModulPartExam($module_id, $module_exam_id, $module_part_id, Request $request)
    {
        $modulPartExam = ModulPartExam::find($module_part_id);
        if($modulPartExam == null)
            return $this->createJsonResponse("module part exam not found.", true, 404);

        $modulPartExam->percent = $request->input("percent");
        $modulPartExam->maxPoints = $request->input("maxPoints");
        $modulPartExam->rating = $request->input("rating");
        $modulPartExam->maxTry = $request->input("max_try");
        $modulPartExam->duration = $request->input("duration");
        $modulPartExam->modul_part_exam_label_id = $request->input("modul_part_exam_label_id");
        $modulPartExam->group = $request->input("group");

        $modulPartExam->save();

        if($request->has("subject_ids"))
            $modulPartExam->subjects()->sync($request->input("subject_ids"));

        return $this->detailModulExam($module_id,$module_exam_id,$request);
    }

    public function attachModulsToStudy($study_id, Request $request)
    {
        $study = Studium::find($study_id);
        if($study == null)
            return $this->createJsonResponse("Study not found.", true, 403);

        $study->module()->syncWithoutDetaching($request->input("module_ids"));
        return $this->createJsonResponse("Moduls attached to study.", false, 200, ["study" => $study->with("module")]);
    }
}
