<?php

namespace App\Imports;

use App\AdditionalInfo;
use App\Fach;
use App\Klasse;
use App\Lehrer;
use App\LessonPlan;
use App\Raum;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LessonImport implements ToModel, WithHeadingRow
{
    private $course_id;
    private $draft_id;

    public function __construct($course_id, $draft_id)
    {
        $this->course_id = $course_id;
        $this->draft_id = $draft_id;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if($row["fach"] == "" || $row["datum"] == "")
            return null;

        $nullDate = new \DateTime();
        $nullDate->setTimestamp(0);
        try {
            $datum = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row["datum"]);
            $clock = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row["von"]);
            $clock2 = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row["bis"]);
            $diff = $nullDate->diff($clock);
            $diff2 = $nullDate->diff($clock2);
            $start = clone $datum;
            $start->add($diff);
            $datum->add($diff2);
        } catch (\Exception $exception)
        {
            $start = Carbon::parse($row["datum"]);
            $start->setHour(explode(":",$row["von"])[0]);
            $start->setMinute(explode(":",$row["von"])[1]);

            $datum = Carbon::parse($row["datum"]);
            $datum->setHour(explode(":",$row["bis"])[0]);
            $datum->setMinute(explode(":",$row["bis"])[1]);
        }

        //print_r($start);
        //print_r($datum);

        // Fach
        $fach_id = null;
        if(trim($row["inhalt"]) != "") {
            $fach = Fach::where('abk', '=', trim($row["inhalt"]))->first();
            if ($fach != null) {
                $fach_id = $fach->id;
            }
        }
        if(trim($row["fach"]) != "") {
        if($fach_id == null) {
            $fach = Fach::where('name', '=', trim($row["fach"]))->first();
            if ($fach != null) {
                $fach_id = $fach->id;
            }
        }
        }
        //print_r($fach);

        // Lehrer
        $lehrer_id = null;
        if(trim(strtolower($row["dozent"])) != "") {
            $lehrer = Lehrer::where('email', '=', strtolower($row["dozent"]))->first();
            if ($lehrer == null) {
                $addtionalInfos = AdditionalInfo::where('email', '=', strtolower($row["dozent"]))->get();
                foreach ($addtionalInfos as $addtionalInfo) {
                    $lehrer = Lehrer::where('info_id', $addtionalInfo->id)->first();
                    if ($lehrer != null) {
                        break;
                    }
                }
            }

            if ($lehrer != null) {
                $lehrer_id = $lehrer->id;
            }
        }
        //print_r($lehrer);

        // Raum

        $raum_id = null;
        if(trim(strtolower($row["raum"])) != "") {
            $raum = Raum::where('name', '=', $row["raum"])->first();
            if ($raum != null) {
                $raum_id = $raum->id;
            }
        }
        //print_r($raum);

        // Kurs
        $klasse = Klasse::where('name', '=', $row["kurs"])->first();
        if($klasse == null)
            return null;
        //print_r($klasse);

        // check if exists
//        $lessonCheck = LessonPlan::where('startDate', '=', $start)->where('endDate', '=', $datum)->where('recurrenceType', '=', 'none')->where('fach_id','=', $fach_id)->first();
//        if($lessonCheck != null)
//        {
//            DB::table('klasse_lesson_plan')->where('lesson_plan_id', '=', $lessonCheck->id)->delete();
//            $lesson = $lessonCheck;
//        } else {
            $lesson = new LessonPlan();
//        }
        $lesson->startDate = $start;
        $lesson->endDate = $datum;
        $lesson->recurrenceType = 'none';
        $lesson->recurrenceUntil = $datum;
        $lesson->fach_id = $fach_id;
        $lesson->schuljahr_entwurf_id = $this->draft_id;
        $lesson->description = $row["kommentar"];
        if(array_key_exists("le",$row) && $row["le"] != null) {
            $lesson->deviant_ue = $row["le"];
        }
        // $lesson->subtitle = $row["kommentar"];
        $lesson->save();

        if ($klasse != null) {
            DB::table('klasse_lesson_plan')->insert([
                'klasse_id' => $klasse->id,
                'lesson_plan_id' => $lesson->id,
            ]);
        } else if($this->course_id != null)
        {
//            DB::table('klasse_lesson_plan')->insert([
//                'klasse_id' => $this->course_id,
//                'lesson_plan_id' => $lesson->id,
//            ]);
        }
        if($lehrer_id != null)
        {
            DB::table('lehrer_lesson_plan')->insert([
                'lehrer_id' => $lehrer_id,
                'lesson_plan_id' => $lesson->id,
            ]);
        }
        if($raum_id != null)
        {
            DB::table('raum_lesson_plan')->insert([
                'raum_id' => $raum_id,
                'lesson_plan_id' => $lesson->id,
            ]);
        }
        return $lesson;
    }
}
