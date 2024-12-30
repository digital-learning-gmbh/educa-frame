<?php

namespace App\Http\Controllers\API\V1\Classbook;

use App\ExamExecutionDate;
use App\Http\Controllers\API\ApiController;
use App\ModulExamExecution;
use App\Schuler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamDatesController extends ApiController
{
    public function getExamDates(Request $request)
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
