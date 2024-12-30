<?php

namespace App\Http\Controllers\API\V1\Administration\ExamAdministration;

use App\Aufgabe;
use App\ExamExecutionDate;
use App\Http\Controllers\API\V1\Administration\AdministationApiController;
use App\Klasse;
use App\Kohorte;
use App\Lehrplan;
use App\Module;
use App\ModulExam;
use App\ModulExamExecution;
use App\ModulPartExam;
use App\ModulPartExamDate;
use App\Note;
use App\Schule;
use App\Schuler;
use App\Schuljahr;
use App\StudyProgressEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\AssignOp\Mod;

class OverviewController extends AdministationApiController
{

    /**
     * Hier rechnet er für das Semster bisschen was
     * @param Request $request
     */
    public function listAndCalculateExamsForSemester(Request $request)
    {
        // Starte die Zeitmessung
        $start = microtime(true);
        // Genereller Aufbau hier:
        // Wir machen das ganze für ein konkreten Standort + Semester
        $standort = Schule::find($request->input("school_id"));
        $semester = Schuljahr::find($request->input("year_id"));
        //
        $lehrplansFS = []; // hier haben wir ein array
        $lehrplanCache = []; // hier ein zweites Array
        // [
        //      "lehrplan" => LEHRPLAN_OBJECT,
        //      "fs" => [ 3, 2 ] // welches fs semester bitte?
        // ]
        $studentsCount = 0;
        $studentsCountActive = 0;
        $moduleExams = [];
        foreach ($standort->schuler as $student)
        {
            $studentsCount++;
            $studyInformation = $student->getCurrentStudyInformation($semester->id);
            if($studyInformation != null && $studyInformation->kohorte != null)
            {
                $studentsCountActive++;
                $lehrplan_id = $studyInformation->kohorte->lehrplan_id;
                if(!in_array($lehrplan_id, $lehrplanCache))
                {
                    $lehrplanCache[] = $lehrplan_id;
                    $lehrplansFS[$lehrplan_id] = [ "lehrplan" => Lehrplan::find($lehrplan_id), "fs" => [] ];
                }
                $fs = $studyInformation->fs;
                if(!in_array($fs, $lehrplansFS[$lehrplan_id]["fs"]))
                {
                    $lehrplansFS[$lehrplan_id]["fs"][] = $fs;
                }
                // Berechne offene Module für den Guy bis zum FS, wo er ist
                $module = $student->calculateOpenModuls($studyInformation, $lehrplan_id, $fs);
                foreach ($module as $modul)
                {
                    $modulExam = $modul->examsForLehrplan($lehrplan_id);
                    if($modulExam == null)
                        continue;
                    if(count($modulExam) > 0)
                    {
                        $modulExam = $modulExam[0];
                    }

                    if(!array_key_exists($modulExam->id, $moduleExams))
                    {
                        $moduleExams[$modulExam->id] = [ "modul" => $modul, "modulExam" => $modulExam, "parts" => [] ];
                    }

                    $modulExamCache = $moduleExams[$modulExam->id];
                    // Welche Teilprüfungen denn überhaupt?
                    $modulExamParts = $student->calculateOpenExamParts($modul,$lehrplan_id,$fs,$modulExam);
                    foreach ($modulExamParts as $examPart)
                    {
                        if(!array_key_exists($examPart->id, $modulExamCache["parts"]))
                        {
                            $modulExamCache["parts"][$examPart->id] = [ "modulPartExam" => $examPart, "students" => [] ];
                        }
                        $studyInformation->load("kohorte");
                        $studyInformation->load("kohorte.studium");
                        $modulExamCache["parts"][$examPart->id]["students"][] = [
                            "_object" =>  $student,
                            "fs" => $fs,
                            "studyInformation" => $studyInformation,
                        ];
                    }

                    $moduleExams[$modulExam->id] = $modulExamCache;
                }
            }
        }

        // Just statistics
        $lehrplanFSCount = [];
        foreach ($lehrplansFS as $id=>$object)
        {
            $lehrplanFSCount[$id] = count($object["fs"]);
        }

        $consideredModulExamDates = [];
        // compute the addtional information
        foreach ($moduleExams as $id => $moduleExam)
        {
            foreach ($moduleExam["parts"] as $id2 => $part)
            {
                $planunsgruppen = [];
                foreach ($part["students"] as $student)
                {
                    $planunsgruppen[] = $student["studyInformation"]->klasse;
                }
                $moduleExam["parts"][$id2]["planing_groups"] = $planunsgruppen;
                $moduleExam["parts"][$id2]["executions"] = ModulPartExamDate::where('schuljahr_id','=',$semester->id)->where('modul_part_exam_id','=',$id2)->get();
                $moduleExam["parts"][$id2]["executions"]->each->load("dates");
                foreach ($moduleExam["parts"][$id2]["executions"] as $execution)
                {
                    $consideredModulExamDates[] = $execution->id;
                }
            }
            $moduleExams[$id] = $moduleExam;
        }

        // laden wir dann nohcmal den Restlichen Bums
        $modulPartExamDatesManual = ModulPartExamDate::where('schuljahr_id','=',$semester->id)->whereNotIn('id',$consideredModulExamDates)->get();
        foreach ($modulPartExamDatesManual as $manualDate)
        {
            $modulPartExam = $manualDate->modulPartExam;
            $modulExam = $modulPartExam->modulExam;
            if(!array_key_exists($modulExam->id,$moduleExams))
            {
                $moduleExams[$modulExam->id] = [ "modul" => $modulExam->modul, "modulExam" => $modulExam, "parts" => [] ];
            }
            if(!array_key_exists($modulPartExam->id,$moduleExams[$modulExam->id]["parts"] ))
            {
                $moduleExams[$modulExam->id]["parts"][$examPart->id] = [ "isManual" => true , "modulPartExam" => $modulPartExam, "students" => [], "executions" => [] ];
            }
            $moduleExams[$modulExam->id]["parts"][$examPart->id]["executions"][] = $manualDate;
        }


        // Labels laden
        foreach ($moduleExams as $id => $moduleExam) {
            $moduleExam["modulExam"]->load("examLabel");
            foreach ($moduleExam["parts"] as $id2 => $part) {
                $part["modulPartExam"]->load("examPartLabel");
            }
        }


        // Ende der Zeitmessung
        $time_elapsed_secs = microtime(true) - $start;
        // Statistische Auswertung
        $statistics = [
            "time" => $time_elapsed_secs,
            "students_count" => $studentsCount,
            "students_active_count" => $studentsCountActive,
            "curriculum_count" => count($lehrplanCache),
            "curriculum_fs_count" => $lehrplanFSCount,
            "modul_exam_count" => count($moduleExams)
        ];
        return parent::createJsonResponseStatic('', false, 200, [ "statistics" =>  $statistics , "result" => $moduleExams ]);
    }

    public function recommendationsForExecution(Request $request)
    {
        // Wir machen das ganze für ein konkreten Standort + Semester

        $modulPartExam = ModulExamExecution::find($request->input("execution_id"));
        $suggestionOnly = $request->input("suggestion_only") == "true";
        $semester = $modulPartExam->klasse->schuljahr;

        $isBlockingGroup = false;
        if($modulPartExam->klasse->type == "blocking_group")
        {
            $isBlockingGroup = true;
        }
        if($modulPartExam->klasse->type == "cluster_group")
        {
            foreach ($modulPartExam->klasse->klassen as $klasse) {
                if($klasse->type == "blocking_group")
                {
                    $isBlockingGroup = true;
                }
            }
        }

        if($modulPartExam == null || $semester == null)
            return parent::createJsonResponseStatic('No paraemts dfs', true, 401, [  ]);

        $modul_id = $modulPartExam->modulExam->module_id;

        $schuljahr_ids = Schuljahr::where("start","<=",Carbon::now())->where("ende",">=",Carbon::now())->pluck("id");
        if($suggestionOnly) {
            $students = Schuler::whereIn("id", StudyProgressEntry::whereNotIn("status", ["withdrawn", "canceled", "finish"])->whereIn("schuljahr_id", $schuljahr_ids)->whereIn("kohorte_id", Kohorte::whereIn("lehrplan_id", DB::table("modul_fach_curiculum")->where("module_id", "=", $modul_id)->pluck("lehrplan_id"))->pluck("id"))->pluck("schuler_id"))->get();
        } else {
            $students = Schuler::whereIn("id", StudyProgressEntry::whereNotIn("status", ["withdrawn", "canceled", "finish"])->whereIn("schuljahr_id", $schuljahr_ids)->pluck("schuler_id"))->get();
        }

        $result_student = [];
        foreach ($students as $student)
        {
            if($suggestionOnly) {
                if (!$isBlockingGroup && $student->schulen->first() != null && $semester->schule_id != $student->schulen->first()->id)
                    continue;

                if ($student->schulen->first() != null && $student->schulen->first()->id == 12)
                    continue;

                $bestanden = Note::where("model_type", "=", "modul")->where("schuler_id", $student->id)->where("model_id", "=", $modul_id)->where(function ($query) {
                    $query->where("note", "<=", 4)->where("note", ">=", 1);
                })->orderBy("version", "DESC")->exists();
                if ($bestanden)
                    continue;

            }

            $student->addInfo = $student->getAddInfo();
            $student->modulPartExamStatus = "recommended";
            $student->current_study_information = StudyProgressEntry::where('schuler_id','=',$student->id)->whereIn('schuljahr_id',$schuljahr_ids)->first();
            if($student->current_study_information != null) {
                if($suggestionOnly) {
                    if ($student->current_study_information->lehrplan() != null) {
                        $entry = DB::table("modul_fach_curiculum")->where("module_id", "=", $modul_id)->where("lehrplan_id", "=", $student->current_study_information->lehrplan()->id)->first();
                        if ($entry->semester_occurrence >= $student->current_study_information->fs) {
                            continue;
                        }
                    }
                }
                $student->current_study_information->loadAll();
            }

            $student->versuch = 1;
            $student->last_note = "-";
            $note = Note::where("model_type","=","modul")->where("attest","=",0)->where("schuler_id","=",$student->id)->where("model_id","=",$modul_id)->orderBy("version","DESC")->first();
            if($note != null)
            {
                $student->versuch = ($note->version +1);
                $student->last_note  = $note->note <= 0 ? "-" : $note->note;
            }
            if($student->versuch == 1 || $student->last_note == 5 || !$suggestionOnly)
            {
                $result_student[] = $student;
            }
        }

        return parent::createJsonResponseStatic('', false, 200, [ "students" =>  $result_student ]);
    }

    public function pruefungsAufgaben(Request $request)
    {
        $school = Schule::findOrFail($request->input("school_id"));
        $aufgabenOpen = Aufgabe::where('status','new')->where('schule_id',$school->id)->where('level','=','examAdministration')->take(100)->orderBy('created_at','ASC')->get();
        return parent::createJsonResponseStatic('', false, 200, [ "tasks" => $aufgabenOpen ]);
    }

    public function detailsForModulExam(Request $request)
    {
        $standort = Schule::find($request->input("school_id"));
        $semester = Schuljahr::find($request->input("year_id"));

        $planingGroups = [];
        foreach ($semester->klassen()->orderBy("type")->orderBy("name")->get() as $klasse)
        {

            $resultModule = $this->loadModulExamForClass($klasse);

            $klasse->append("studiengang");
            $klasse->append("allFS");
            $klasse->append("allTypes");
            $clusters = [];
            foreach ($klasse->cluster as $cluster)
            {
                $resultModuleCluster = $this->loadModulExamForClass($cluster);
                $cluster->append("studiengang");
                $cluster->append("allFS");
                $cluster->append("allTypes");
                if(count($resultModuleCluster) != 0)
                    $clusters[] = ["planing_group" => $cluster, "module" => $resultModuleCluster ];
            }

            if(count($resultModule) != 0)
                $planingGroups[] = ["planing_group" => $klasse, "module" => $resultModule, "clusters" => $clusters ];
        }

        return parent::createJsonResponseStatic('', false, 200, [ "result" => $planingGroups ]);
    }

    public function loadModulExamForClass($klasse)
    {
        $resultModule = [];
        $modulExams = ModulExam::whereIn('id',
            DB::table("modul_exam_executions")->where("klasse_id",'=',$klasse->id)->pluck("modul_exam_id")
        )->with("modul")->get();

        foreach ($modulExams as $modulExam) {
            $modul = $modulExam->modul;
            $modul->exam = $modulExam;
            unset($modulExam->modul);
            $modul->exam_executions = ModulExamExecution::where('modul_exam_id','=',$modulExam->id)->where('klasse_id','=',$klasse->id)->with("students")->with("examDates")->orderBy("type")->get();

            foreach ($modul->exam_executions as $exam_execution) {
                $exam_execution->wiederholer = Note::where("model_type","=","modul")->whereIn("schuler_id",$exam_execution->students->pluck("id"))->where("model_id","=",$modul->id)->orderBy("version","DESC")->exists();
                $exam_execution->oral_exam = Note::where("model_type","=","modul")->whereIn("schuler_id",$exam_execution->students->pluck("id"))->where("model_id","=",$modul->id)->where("version",">=",2)->orderBy("version","DESC")->exists();
                $exam_execution->examDates->each->append("examParts");
                $exam_execution->examDates->each->load("rooms");
                $exam_execution->examDates->each->load("teacher");
            }

            if(count($modul->exam_executions) != 0)
                $resultModule[] = $modul;

        }
        return $resultModule;
    }

    public function calculate(Schule $standort, Schuljahr $semester)
    {
        // Starte die Zeitmessung
        $start = microtime(true);
        // Genereller Aufbau hier:
        // Wir machen das ganze für ein konkreten Standort + Semester
        //
        $lehrplansFS = []; // hier haben wir ein array
        $lehrplanCache = []; // hier ein zweites Array
        // [
        //      "lehrplan" => LEHRPLAN_OBJECT,
        //      "fs" => [ 3, 2 ] // welches fs semester bitte?
        // ]
        $studentsCount = 0;
        $studentsCountActive = 0;
        $lehrplanFSCount = 0;
        $consideredModulExamDates = [];

        $planingGroups = [];
        foreach ($semester->klassen as $klasse)
        {
            //  $klasse->type != "planning_group" && $klasse->type != "blocking_group" && $klasse->type != "special" &&
            if( $klasse->type != "planning_group" && $klasse->type != "blocking_group" && $klasse->type != "special" && $klasse->type != "cluster_group")
                continue;
            if($klasse->getLehrplan()->first() == null)
                continue;
            $lehrplan = $klasse->getLehrplan()->first();

            $klasse->lehrplan = $lehrplan;

            $module = $klasse->moduleWithOutCluster(); // HelperCalculation::calculateOpenModuls($lehrplan->id, $klasse->fs);

            $modulList = [];
            if($klasse->type == "planning_group")
            {
                $modulList = $this->calculateForPlaningGroup($module, $lehrplan,$standort, $klasse);
            }
            else if($klasse->type == "blocking_group")
            {
                if($klasse->lehrplan_einheit_id != null)
                    $modulList = $this->calculateForAnyGroupWithModulList($module, $lehrplan,$standort, $klasse);
            }
            else if($klasse->type == "special")
            {
                if($klasse->lehrplan_einheit_id != null)
                    $modulList = $this->calculateForAnyGroupWithModulList($module, $lehrplan,$standort, $klasse);
            }
            else if($klasse->type == "cluster_group")
            {
                // Können hier auch Blocking group nehmen
                $modulList = $this->calculateForAnyGroupWithModulList($module, $lehrplan,$standort, $klasse);
            }

            $planingGroups[] = ["planing_group" => $klasse, "module" => $modulList ];
        }

        // Ende der Zeitmessung
        $time_elapsed_secs = microtime(true) - $start;
        // Statistische Auswertung
        $statistics = [
            "time" => $time_elapsed_secs,
            "students_count" => $studentsCount,
            "students_active_count" => $studentsCountActive,
            "curriculum_fs_count" => $lehrplanFSCount,
            "modul_exam_count" => count($planingGroups)
        ];


        return [ "statistics" =>  $statistics , "result" => $planingGroups ];
    }

    private function calculateForPlaningGroup($module, Lehrplan $lehrplan, Schule $standort, Klasse $klasse)
    {
        $modulList = [];
        foreach ($module as $modul)
        {
            if($modul->isSchwerpunkt($lehrplan->id))
                continue;

            $modulExam = $modul->examsForLehrplan($lehrplan->id, $standort->id, $klasse->von);
            if($modulExam == null)
                continue;

            // This should not happen ...
            if(count($modulExam) > 0)
            {
                $modulExam = $modulExam[0];
            }

            $modulExam->groups = $modulExam->getGroups();
            $modul->exam = $modulExam;
            $modulExam->load("examLabel");

            $modulList[] = $modul;
            // wir brauchen die ids des parts exams
            $modul->exam_executions = ModulExamExecution::where('modul_exam_id','=',$modulExam->id)->where('klasse_id','=',$klasse->id)->get();

            if($modul->exam_executions == null || count($modul->exam_executions) == 0)
            {
                // it null, daher erstellen wir eine passende
                $modulExamExecutionEmpty = new ModulExamExecution();
                $modulExamExecutionEmpty->modul_exam_id = $modulExam->id;
                $modulExamExecutionEmpty->klasse_id = $klasse->id;
                $modulExamExecutionEmpty->save();

                $groups = $modulExam->groups;
                foreach ($groups as $group)
                {
                    // TODO if group max(fs) == current_fs
                    $examExecutionEmpty = new ExamExecutionDate();
                    $examExecutionEmpty->modul_exam_execution_id = $modulExamExecutionEmpty->id;
                    $examExecutionEmpty->group = $group;
                    $examExecutionEmpty->save();
                }

                // add teilnehmer
                $date = date("Y/m/d");
                $students = Schuler::find($klasse->getAllSchulerAttribute())->pluck("id")->toArray(); // Schuler::whereIn('id', DB::table('klasse_schuler')->where('klasse_id', $klasse->id)
            //        ->join('schulers', 'klasse_schuler.schuler_id', '=', 'schulers.id')
//                    ->where(function ($query) use ($date) {
//                        $query->where('klasse_schuler.from', '<=', $date)->orWhereNull('klasse_schuler.from');
//                    })
              //      ->where(function ($query) use ($date) {
               //         $query->where('klasse_schuler.until', '>=', $date)->orWhereNull('klasse_schuler.until');
            //        })
          //  ->pluck('schulers.id'))
        //            ->pluck("id")->toArray();
                $finalStudents = [];
                foreach ($students as $student_id)
                {
                    // bestandene nicht einbuchen
                    $bestanden = Note::where("model_type","=","modul")->where("schuler_id",$student_id)->where("model_id","=",$modulExam->modul->id)->where(function ($query) {
                        $query->where("note", "<=", 4)->where("note", ">=", 1);
                    })->orderBy("version","DESC")->exists();
                    if($bestanden)
                        continue;

                    // nur 1-2 Versuch in "normale" Prüfungen
                   $versuche =  Note::where("model_type","=","modul")->where("schuler_id",$student_id)->where("model_id","=",$modulExam->modul->id)->orderBy("version","DESC")->count();
                    if($versuche <= 2)
                            $finalStudents[] = $student_id;
                }
                $modulExamExecutionEmpty->students()->sync($finalStudents);

                $modul->exam_executions = ModulExamExecution::where('modul_exam_id','=',$modulExam->id)->where('klasse_id','=',$klasse->id)->get();
            }

            // lade die restlichen dinger

            foreach ($modul->exam_executions as $exam_execution)
            {
                $exam_execution->examDates->each->append("examParts");
                $exam_execution->load("students");
            }
        }
        return $modulList;
    }

    /**
     * Wir gehen davon aus, dass die Modul-Liste richtig ist.
     * @param $module
     * @param Lehrplan $lehrplan
     * @param Schule $standort
     * @param Klasse $klasse
     * @return array
     */
    private function calculateForAnyGroupWithModulList($module, Lehrplan $lehrplan, Schule $standort, Klasse $klasse)
    {
        $modulList = [];

        foreach ($module as $modul)
        {
            $modulExam = $modul->examsForLehrplan($lehrplan->id, $standort->id, $klasse->von);
            if($modulExam == null)
                continue;

            // This should not happen ...
            if(count($modulExam) > 0)
            {
                $modulExam = $modulExam[0];
            }

            $modulExam->groups = $modulExam->getGroups();
            $modul->exam = $modulExam;
            $modulExam->load("examLabel");

            $modulList[] = $modul;
            // wir brauchen die ids des parts exams
            $modul->exam_executions = ModulExamExecution::where('modul_exam_id','=',$modulExam->id)->where('klasse_id','=',$klasse->id)->get();

            if($modul->exam_executions == null || count($modul->exam_executions) == 0)
            {
                // it null, daher erstellen wir eine passende
                $modulExamExecutionEmpty = new ModulExamExecution();
                $modulExamExecutionEmpty->modul_exam_id = $modulExam->id;
                $modulExamExecutionEmpty->klasse_id = $klasse->id;
                $modulExamExecutionEmpty->save();

                $groups = $modulExam->groups;
                foreach ($groups as $group)
                {
                    // TODO if group max(fs) == current_fs
                    // ModulPartExam::where('modul_exam_id','=',$this->examExecution->modulExam->id)
                    //            ->where('group','=',$this->group)->with("examPartLabel")->with("subjects")->get();
                    $examExecutionEmpty = new ExamExecutionDate();
                    $examExecutionEmpty->modul_exam_execution_id = $modulExamExecutionEmpty->id;
                    $examExecutionEmpty->group = $group;
                    $examExecutionEmpty->save();
                }

                // add teilnehmer
                $students = Schuler::find($klasse->getAllSchulerAttribute())->pluck("id")->toArray(); // Schuler::whereIn('id', DB::table('klasse_schuler')->where('klasse_id', $klasse->id)
                //        ->join('schulers', 'klasse_schuler.schuler_id', '=', 'schulers.id')
//                    ->where(function ($query) use ($date) {
//                        $query->where('klasse_schuler.from', '<=', $date)->orWhereNull('klasse_schuler.from');
//                    })
                //      ->where(function ($query) use ($date) {
                //         $query->where('klasse_schuler.until', '>=', $date)->orWhereNull('klasse_schuler.until');
                //        })
                //  ->pluck('schulers.id'))
                //            ->pluck("id")->toArray();
                $finalStudents = [];
                foreach ($students as $student_id)
                {
                    // bestandene nicht einbuchen
                    $bestanden = Note::where("model_type","=","modul")->where("schuler_id",$student_id)->where("model_id","=",$modulExam->modul->id)->where(function ($query) {
                        $query->where("note", "<=", 4)->where("note", ">=", 1);
                    })->orderBy("version","DESC")->exists();
                    if($bestanden)
                        continue;

                    // nur 1-2 Versuch in "normale" Prüfungen
                    $versuche =  Note::where("model_type","=","modul")->where("schuler_id",$student_id)->where("model_id","=",$modulExam->modul->id)->orderBy("version","DESC")->count();
                    if($versuche <= 2)
                        $finalStudents[] = $student_id;
                }
                $modulExamExecutionEmpty->students()->sync($finalStudents);

                $modul->exam_executions = ModulExamExecution::where('modul_exam_id','=',$modulExam->id)->where('klasse_id','=',$klasse->id)->get();
            }

            // lade die restlichen dinger

            foreach ($modul->exam_executions as $exam_execution)
            {
                $exam_execution->examDates->each->append("examParts");
                $exam_execution->load("students");
            }

        }
        return $modulList;
    }

    public function recalculate(Request $request)
    {
        $standort = Schule::find($request->input("school_id"));
        $semester = Schuljahr::find($request->input("year_id"));

        foreach ($semester->klassen as $klasse) {
            if($request->input("deleteMode") == "all") {
                $examExecution = ModulExamExecution::where('klasse_id','=',$klasse->id)->get();
            } else {
                $examExecution = ModulExamExecution::whereHas('examDates', function ($query) { $query->where("status", '=', "new"); })
                    ->where('klasse_id','=',$klasse->id)
                    ->get();
            }
            $examExecution->each->delete();
        }

        // do the calculation
        $result = $this->calculate($standort,$semester);

        return parent::createJsonResponseStatic('', false, 200, [ "message" => "done" , "result" => $result ]);
    }

    public function oralOverview(Request $request) {
        $standort = Schule::find($request->input("school_id"));
        $semester = Schuljahr::find($request->input("year_id"));

        $planingGroups = [];

        // TODO only the kohorten that are currently active
        foreach ($standort->kohorten()->orderBy("name")->get() as $kohorte)
        {
            $students = [];
            foreach ($kohorte->members as $student)
            {
                // TODO calculate all required third exam or more that the student has to do
                continue;
                $resultModuleCluster = $this->loadModulExamForClass($cluster);
                if(count($resultModuleCluster) != 0)
                    $clusters[] = ["planing_group" => $cluster, "module" => $resultModuleCluster ];
            }

            $planingGroups[] = ["kohorte" => $kohorte, "students" => $students ];
        }

        return parent::createJsonResponseStatic('', false, 200, [ "kohorts" => $planingGroups ]);
    }
}
