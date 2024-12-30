<?php

namespace App\Http\Controllers\API\V1\Administration\ExamAdministration;

use App\ExamExecutionDate;
use App\Http\Controllers\API\V1\Administration\AdministationApiController;
use App\Klasse;
use App\ModulExam;
use App\ModulExamExecution;
use App\ModulPartExam;
use App\ModulPartExamDate;
use App\Note;
use App\Schuler;
use Carbon\Carbon;
use Carbon\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExamExecutionController extends AdministationApiController
{

    public function createExamExecution(Request $request)
    {
        $modulexam = ModulExam::find($request->input("module_exam_id"));
        if($modulexam == null)
            return $this->createJsonResponse("module part exam not found.", true, 404);

        if($request->input("course_id") == null)
            return $this->createJsonResponse("course is not given", true, 404);

        $course = Klasse::find($request->input("course_id") );

        if(!$course)
            return $this->createJsonResponse("course is null", true, 404);


        $students = $course->schuler;

        // filter students and remove students that are already passed the exam
        $finalStudents = [];
        foreach ($students as $student_id)
        {
            // bestandene nicht einbuchen
            $bestanden = Note::where("model_type","=","modul")->where("schuler_id",$student_id)->where("model_id","=",$modulexam->modul->id)->where(function ($query) {
                $query->where("note", "<=", 4)->where("note", ">=", 1);
            })->orderBy("version","DESC")->exists();
            if($bestanden)
                continue;

            // nur 1-2 Versuch in "normale" Prüfungen
            if($request->input("type") == "first_exam" || $request->input("type") == "repeat_exam")
            {
                $versuche = Note::where("model_type","=","modul")->where("attest","=",0)->where("schuler_id",$student_id)->where("model_id","=",$modulexam->modul->id)->orderBy("version","DESC")->first();
                if($versuche == null || $versuche->version <= 1)
                    $finalStudents[] = $student_id;

                continue;
            }

            // 3. oder höhher in mündliche
            $versuche =  Note::where("model_type","=","modul")->where("attest","=",0)->where("schuler_id",$student_id)->where("model_id","=",$modulexam->modul->id)->orderBy("version","DESC")->first();
            if($versuche != null && $versuche->version >= 2)
                $finalStudents[] = $student_id;
        }


        if($request->input("type") == "oral_exam")
        {
            foreach ($finalStudents as $finalStudent)
            {
                $modulPartExamDate = new ModulExamExecution;
                $modulPartExamDate->modul_exam_id = $modulexam->id;
                $modulPartExamDate->klasse_id = $course->id;
                $modulPartExamDate->status = "draft";
                $modulPartExamDate->type = $request->input("type");

                $modulPartExamDate->save();
                $modulPartExamDate->students()->sync([$finalStudent]);

                $examExecutionEmpty = new ExamExecutionDate();
                $examExecutionEmpty->modul_exam_execution_id = $modulPartExamDate->id;
                $examExecutionEmpty->group = 1;
                $examExecutionEmpty->save();
            }
            return  parent::createJsonResponse("created",false, 200, [ "execution" => [], "modulExam" => $modulexam]);
        } else {

            $modulPartExamDate = new ModulExamExecution;
            $modulPartExamDate->modul_exam_id = $modulexam->id;
            $modulPartExamDate->klasse_id = $course->id;
            $modulPartExamDate->status = "draft";
            $modulPartExamDate->type = $request->input("type");

            $modulPartExamDate->save();
            $modulPartExamDate->students()->sync($finalStudents);

            $groups = $modulexam->getGroups();
            foreach ($groups as $group) {
                $examExecutionEmpty = new ExamExecutionDate();
                $examExecutionEmpty->modul_exam_execution_id = $modulPartExamDate->id;
                $examExecutionEmpty->group = $group;
                $examExecutionEmpty->save();
            }

            return $this->detailsExamExecution($modulPartExamDate->id, $request);
        }
    }

    public function detailsExamExecution($execution_id, Request $request)
    {
        $modulPartExamDate = ModulExamExecution::find($execution_id);
        if($modulPartExamDate == null)
            return $this->createJsonResponse("module execution not found.", true, 404);

        $modulPartExamDate->examDates->each->append("examParts");
        $modulPartExamDate->examDates->each->load("rooms");
        $modulPartExamDate->examDates->each->load("teacher");
        $modulPartExamDate->examDates->each->append("teacherMapped");
        $modulPartExamDate->load("students");

        $year_id = $modulPartExamDate->klasse->schuljahr_id;
        $q = [];
        foreach ($modulPartExamDate->students as $student) {
            $student->addInfo = $student->getAddInfo();
            $student->current_study_information = $student->getCurrentStudyInformation($year_id);
            if($student->current_study_information != null) {
                $student->current_study_information->loadAll();
            }
            $student->versuch = 1;
            $student->last_note = "-";
            $student->modul_note = "";
            $student->modul_punkte = "";
            $student->modul_note_status = "";
            $note = Note::where("model_type","=","modul")->where("attest","=",0)->where("schuler_id","=",$student->id)->where("model_id","=",$modulPartExamDate->modulExam->module_id)->where("exam_execution_id","!=",$modulPartExamDate->id)->orderBy("version","DESC")->first();
            if($note != null)
            {
                $student->versuch = ($note->version +1);
                $student->last_note  = $note->note < 0 ? "-" : $note->note;
            }

            $note = Note::where("exam_execution_id","=",$modulPartExamDate->id)->where("model_type","=","modul")->where("schuler_id","=",$student->id)->orderBy("version","DESC")->first();
            if($note != null)
            {
                $student->modul_note  = $note->note < 0 ? "-" : $note->note;
                $student->modul_punkte  = $note->points < 0 ? "-" : $note->points;
                if($note->attest)
                {
                    $student->modul_note = "Attest";
                    $student->modul_punkte = "-";
                }
                $student->modul_note_status = $note->status;
            }
            $q[] = $student;
        }
        usort($q, array($this,'studentSort2'));
        $modulPartExamDate->students = $q;
        $modulExam = $modulPartExamDate->modulExam;
        $modulExam->load("modul");
        $modulExam->groups = $modulExam->getGroups();
        return parent::createJsonResponse("execution details",false, 200, [ "execution" => $modulPartExamDate, "modulExam" => $modulExam, "students" => $q ]);
    }


private static function studentSort2($a,$b) {
    return $a->addInfo->personalnummer < $b->addInfo->personalnummer;
}

    public function updateExamExection($execution_id, Request $request)
    {
        $examExecution = ModulExamExecution::find($execution_id);
        if($examExecution == null)
            return $this->createJsonResponse("module part exam not found.", true, 404);

        $examExecution->notes = $request->notes;
        $examExecution->status = "draft";
        $examExecution->type = $request->input("type");
        $examExecution->save();
        if( is_array($request->input("student_ids")))
            $examExecution->students()->sync($request->input("student_ids"));

        if($examExecution->type == "oral_exam")
        {
            $student = $request->input("student");
            $noteDB = Note::where("exam_execution_id","=",$examExecution->id)->where("model_type","=","modul")->where("schuler_id","=",$student["id"])->first();
            if($noteDB == null)
            {
                $noteDB = new Note;
                $noteDB->model_id = $examExecution->modulExam->module_id;
                $noteDB->model_type = "modul";
                $noteDB->exam_execution_id = $examExecution->id;
                $noteDB->schuler_id = $student["id"];
                $noteDB->status = "examination_office";
                $note = Note::where("model_type","=","modul")->where("schuler_id","=",$student["id"])->where("model_id","=",$examExecution->modulExam->module_id)->orderBy("version","DESC")->first();
                $last_version = 0;
                if($note != null)
                    $last_version = $note->version;
                $noteDB->version = ($last_version + 1);
                $noteDB->schuljahr_id = $examExecution->klasse->schuljahr_id;
                $noteDB->datum = Carbon::now()->format("Y-m-d");
                $noteDB->save();
            }
            $noteDB->points = $student["modul_punkte"];
            $noteDB->note = $student["modul_note"];

            $noteDB->save();
        }

        return $this->detailsExamExecution($execution_id,$request);
    }

    public function removeExamExecution($execution_id, Request $request)
    {
        $modulPartExamDate = ModulExamExecution::find($execution_id);
        if($modulPartExamDate == null)
            return $this->createJsonResponse("module part exam not found.", true, 404);

        $modulPartExamDate->students()->sync([]);
        $modulPartExamDate->examDates->each->delete();

        $modulPartExamDate->delete();

        return parent::createJsonResponse("execution deleted",false, 200, []);
    }

    public function addDate($execution_id, Request $request)
    {
        $modulPartExamDate = ModulExamExecution::find($execution_id);
        if($modulPartExamDate == null)
            return $this->createJsonResponse("module part exam not found.", true, 404);

        $examDate = new ExamExecutionDate;
        $examDate->modul_exam_execution_id = $modulPartExamDate->id;
        $examDate->group = $request->input("group");
        $examDate->start = Carbon::createFromTimestamp($request->input("start"));
        if($request->input("end"))
            $examDate->end = Carbon::createFromTimestamp($request->input("end"));

        $examDate->save();

        return $this->detailsExamExecution($modulPartExamDate->id, $request);
    }

    public function updateDate($execution_id, $date_id, Request $request)
    {
        $modulPartExamDate = ModulExamExecution::find($execution_id);
        if($modulPartExamDate == null)
            return $this->createJsonResponse("module part exam not found.", true, 404);

        $modulPartExamDate = ExamExecutionDate::find($date_id);
        if($modulPartExamDate == null)
            return $this->createJsonResponse("exam date not found.", true, 404);

        $teacher_ids = $request->teacher_ids;
        $room_ids = $request->room_ids;
        if(!is_array($teacher_ids) || ! is_array($room_ids))
            return $this->createJsonResponse("teacher/rooms are not an array.", true, 400);

        $modulPartExamDate->notes = $request->notes;
        $modulPartExamDate->supervision = $request->supervision;
        $modulPartExamDate->start = $request->start ? Carbon::createFromTimestamp($request->start) : null;
        $modulPartExamDate->end = $request->end ? Carbon::createFromTimestamp($request->end) : null;
        $modulPartExamDate->reserve_before = $request->reserve_before;
        $modulPartExamDate->reserve_after = $request->reserve_after;
        $modulPartExamDate->status = $request->input("status");
        if($request->input("status") == "new")
        {
            $modulPartExamDate->status = "plan";
        }
        $modulPartExamDate->save();

        DB::table("lehrer_exam_execution_date")->where("exam_execution_date_id","=",$date_id)->delete();
        foreach ($teacher_ids as $struct) {
            if(array_key_exists("teacherIds", $struct)) {
                foreach ($struct["teacherIds"] as $teacherId) {
                    DB::table("lehrer_exam_execution_date")->insert(
                        [
                            "lehrer_id" => $teacherId,
                            "exam_execution_date_id" => $date_id,
                            "part_exam_id" => $struct["partExamId"]
                        ]);
                }
            }
        }

        $modulPartExamDate->rooms()->sync($room_ids);

        return $this->detailsExamExecution($execution_id,$request);
    }

    public function removeDate($execution_id, $date_id, Request $request)
    {
        $modulPartExamDate = ModulExamExecution::find($execution_id);
        if($modulPartExamDate == null)
            return $this->createJsonResponse("module part exam not found.", true, 404);

        $examDate = ExamExecutionDate::find($date_id);
        if($examDate == null)
            return $this->createJsonResponse("exam date not found.", true, 404);

        $examDate->delete();

        return $this->detailsExamExecution($modulPartExamDate->id, $request);
    }

    public function saveGrades($execution_id, $date_id, Request $request) {
        $modulPartExamDate = ModulExamExecution::find($execution_id);
        if($modulPartExamDate == null)
            return $this->createJsonResponse("module part exam not found.", true, 404);

        $examDate = ExamExecutionDate::find($date_id);
        if($examDate == null)
            return $this->createJsonResponse("exam date not found.", true, 404);

        //TODO

        /**
         * Array:
         *
         * "student_id" =>
         *   [
         *        "grades" => [ "part_id2" => 1.7, "part_id2" => 2.0 ],
         *        "feature" =>  "some string",
         *        "away" =>  "no", // yesWith, yesWithout or no
         *        "change" => "some string"
         *   ]
         *
         * e.g.
                "19428": {
                "away": "yesWithout",
                "change": "",
                "feature": "23",
                "grades": {
                "584": 223
                    }
                }
        */

        $mapping = $request->mapping;

        foreach ($mapping as $student_id=>$row)
        {
            $student = Schuler::find($student_id);
            if($student == null)
                continue;

            $away = $row["away"];
            $feature = $row["feature"];
            $grades = $row["grades"];

            foreach ($grades as $idSubPart => $note)
            {
                $modulPart = ModulPartExam::find($idSubPart);
                if($modulPart == null)
                    continue;

                $change_note = $note["change"];
                $note_note = $note["grade"];
                $note_title = $note["title"];

                $noteDB = Note::where("exam_execution_id","=",$modulPart->id)->where("modul_exam_execution_id","=",$modulPartExamDate->id)->where("model_type","=","modul_part")->where("schuler_id","=",$student->id)->first();
                if($noteDB == null)
                {
                    $noteDB = new Note;
                    $noteDB->model_id = $modulPart->modulExam->module_id;
                    $noteDB->model_type = "modul_part";
                    $noteDB->exam_execution_id = $modulPart->id;
                    $noteDB->modul_exam_execution_id = $modulPartExamDate->id;
                    $noteDB->schuler_id = $student->id;
                    $noteDB->status = "examination_office";
                }
                if($modulPart->rating == "points")
                {
                    // check max points?
                    if($note_note < 0 || $note_note >= $modulPart->maxPoints)
                        Log::warning("note nicht im Bereich!");
                    $noteDB->points = $note_note;
                    $noteDB->note = null;
                    $noteDB->maxPoints = $modulPart->maxPoints;
                } else {
                    $noteDB->note = $note_note;
                    $noteDB->points = ($modulPart->rating == "took_part" || $modulPart->rating == "passed") ? -2 : null;
                }
                $noteDB->note_korrektur = $change_note;
                $noteDB->datum = Carbon::parse($examDate->start)->format("Y-m-d");
                $noteDB->bemerkung = $feature;
                $noteDB->transfer = 0;
                $noteDB->title = $note_title;

                $noteDB->attest = $away == "yesWith" ? 1 : 0;
                if($away != "no" && $noteDB->attest == 0)
                {
                    $noteDB->note = 5;
                    $noteDB->points = -1;
                }

                // linked subjects ids
                $noteDB->linked_subjects_id = $modulPart->subjects->pluck("id")->join(",");

                $noteDB->save();
            }
        }

        return $this->detailsExamExecution($modulPartExamDate->id, $request);
    }

    public function getGrades($execution_id, $date_id, Request $request) {
        $modulPartExamDate = ModulExamExecution::find($execution_id);
        if($modulPartExamDate == null)
            return $this->createJsonResponse("module part exam not found.", true, 404);

        $examDate = ExamExecutionDate::find($date_id);
        if($examDate == null)
            return $this->createJsonResponse("exam date not found.", true, 404);

        $students = $modulPartExamDate->students;
        $resultArray = [];
        foreach ($students as $student)
        {
            $resultElement = [];
            $student->load("addInfo");
            $resultElement["student"] = $student;
            $resultElement["parts"] = [];
            foreach ($examDate->examParts as $examPart)
            {
                $grade = Note::where("exam_execution_id","=",$examPart->id)->where("modul_exam_execution_id","=",$modulPartExamDate->id)->where("model_type","=","modul_part")->where("schuler_id","=",$student->id)->first();
                $resultElement["parts"][] = [ "part_exam" => $examPart, "grade" => $grade  ];
            }
            $resultArray[] = $resultElement;
        }
        usort($resultArray, array($this,'studentSort'));
        return parent::createJsonResponse("execution details",false, 200, [ "students" => $resultArray ]);
    }

    private static function studentSort($a,$b) {
        return $a["student"]->addInfo->personalnummer < $b["student"]->addInfo->personalnummer;
}

    public function calculateModulNote($execution_id, Request $request)
    {
        $modulPartExamDate = ModulExamExecution::find($execution_id);
        if($modulPartExamDate == null)
            return $this->createJsonResponse("module exam execution not found.", true, 404);

        $modulExam = $modulPartExamDate->modulExam;

        $students = $modulPartExamDate->students;
        foreach ($students as $student)
        {
            $noteDB = Note::where("exam_execution_id","=",$modulPartExamDate->id)->where("model_type","=","modul")->where("schuler_id","=",$student->id)->first();
            if($noteDB == null)
            {
                $noteDB = new Note;
                $noteDB->model_id = $modulPartExamDate->modulExam->module_id;
                $noteDB->model_type = "modul";
                $noteDB->exam_execution_id = $modulPartExamDate->id;
                $noteDB->schuler_id = $student->id;
                $noteDB->status = "examination_office";
                $note = Note::where("model_type","=","modul")->where("attest","=",0)->where("schuler_id","=",$student->id)->where("model_id","=",$modulPartExamDate->modulExam->module_id)->orderBy("version","DESC")->first();
                $last_version = 0;
                if($note != null)
                    $last_version = $note->version;
                $noteDB->version = ($last_version + 1);
                $noteDB->schuljahr_id = $modulPartExamDate->klasse->schuljahr_id;
                $noteDB->datum = Carbon::now()->format("Y-m-d");
                $noteDB->save();
            }

            // check if all noten avaibable
            $allNotes = true;
            foreach ($modulExam->modulPartExams as $examPart)
            {
                $noteTest = Note::where("exam_execution_id","=",$examPart->id)->where("modul_exam_execution_id","=",$modulPartExamDate->id)->where("model_type","=","modul_part")->where("schuler_id","=",$student->id)->orderBy("version","DESC")->first();
                if($noteTest == null)
                {
                    $allNotes = false;
                }
            }
            if(!$allNotes) {
                // should we delete the old note?
                $noteDB->delete();
                continue;
            }
            // caclulate the modul note
            $points = 0;
            $note_sum = 0;
            $all_passed = true;
            $has_attest = false;
            $modul_note = true;
            $faktor_note = 0;
            $bestanden_nicht_bestanden = false;
            $hasPoints = false;
            $hasNote = false;
            foreach ($modulExam->modulPartExams as $examPart)
            {
                $note = Note::where("exam_execution_id","=",$examPart->id)->where("modul_exam_execution_id","=",$modulPartExamDate->id)->where("model_type","=","modul_part")->where("schuler_id","=",$student->id)->orderBy("version","DESC")->first();
                if($note->attest)
                    $has_attest = true;

                if($examPart->rating == "took_part" || $examPart->rating == "passed")
                {
                    if($note->note == 5 || $note->attest)
                        $all_passed = false;
                    $bestanden_nicht_bestanden = true;
                    // ignore for further calculation
                    continue;
                }

                $note->belongs_to_note = $noteDB->id;
                if($examPart->rating == "points")
                {
                    $hasPoints = true;
                    if($note->points === null) {
                        $modul_note = false;
                    }

                    if($note->points <= 0)
                        continue;


                    $points_calc = $note->points / $examPart->maxPoints * $examPart->percent;
                    $points += $points_calc;
                }

                if($examPart->rating == "thesis" || $examPart->rating == "graded")
                {
                    $hasNote = true;
                    $note_calc = $note->note * $examPart->percent / 100;
                    $note_sum += $note_calc;
                    $faktor_note += $examPart->percent;
                }
                $note->save();
            }

            if(!$modul_note && !$has_attest)
            {
                $noteDB->delete();
                continue;
            }

            if(($modulExam->need_all_parts_passed && !$all_passed) || $has_attest || ($points == 0 && $note_sum == 0))
            {
                $noteDB->note = 5;
                $noteDB->points = -1;
            }
            $noteDB->attest = $has_attest;

            if($has_attest) {

                $noteDB->note = -1;
                $noteDB->points = -1;

                $noteDB->save();
                continue;
            }

            if($bestanden_nicht_bestanden && (!$hasPoints && !$hasNote))
            {
                if($all_passed)
                {
                    $noteDB->note = 1;
                    $noteDB->points = -1;
                } else {
                    $noteDB->note = 5;
                    $noteDB->points = -1;
                }
            } else {
                $note_points = 0;
                if ($points >= 0) {
                    $note_points = $this->pointsToNote($points);
                }
                if ($note_sum != 0) {
                    $noteDB->note = $note_sum * ($faktor_note / 100) + (1 - ($faktor_note / 100)) * $note_points;
                    $noteDB->points = $this->noteToPoints($noteDB->note);
                } else {

                    $noteDB->note = $note_points;
                    $noteDB->points = $points;
                }
            }
            if($noteDB->note <= 4 && !$has_attest)
            {
                DB::table("modul_exam_execution_schuler")->where("schuler_id","=",$student->id)
                    ->where("modul_exam_execution_id", "!=",$modulPartExamDate->id)
                    ->whereIn("modul_exam_execution_id",DB::table("modul_exam_executions")->where("modul_exam_id","=",$modulExam->id)->where("status","!=","released")->pluck("id"))->delete();
            }

            $noteDB->save();
        }



        return $this->detailsExamExecution($execution_id,$request);
    }


    public static function pointsToNote($points)
    {
        if($points >= 95.5)
            return 1;
        if($points >= 90.5)
            return 1.3;
        if($points >= 85.5)
            return 1.7;
        if($points >= 80.5)
            return 2.0;
        if($points >= 75.5)
            return 2.3;
        if($points >= 70.5)
            return 2.7;
        if($points >= 65.5)
            return 3.0;
        if($points >= 60.5)
            return 3.3;
        if($points >= 55.5)
            return 3.7;
        if($points >= 49.5)
            return 4.0;
        return 5;
    }

    public static function noteToPoints($note)
    {
        if($note == 1)
            return 95.5;
        if($note== 1.3)
            return 90.5;
        if($note == 1.7)
            return 85.5;
        if($note == 2.0)
            return 80.5;
        if($note == 2.3)
            return 75.5;
        if($note == 2.7)
            return 70.5;
        if($note == 3.0)
            return 65.5;
        if($note == 3.3)
            return 60.5;
        if($note == 3.7)
            return 55.5;
        if($note == 4.0)
            return 49.5;
        return 0;
    }

    public function publicModulNote($execution_id, Request $request)
    {
        $modulPartExamDate = ModulExamExecution::find($execution_id);
        if($modulPartExamDate == null)
            return $this->createJsonResponse("module exam execution not found.", true, 404);

        $modulExam = $modulPartExamDate->modulExam;

        $public = false;
        if($modulPartExamDate->status == "released")
            $public = true;

        $students = $modulPartExamDate->students;
        foreach ($students as $student) {
            $noteDB = Note::where("exam_execution_id", "=", $modulPartExamDate->id)->where("model_type", "=", "modul")->where("schuler_id", "=", $student->id)->first();
            if($noteDB != null)
            {
                $noteDB->status = $public ? "examination_office" :"public";
                $noteDB->save();
            }
        }
        $modulPartExamDate->status = $public ? "draft" : "released";
        $modulPartExamDate->save();

        return $this->detailsExamExecution($execution_id,$request);
    }
}
