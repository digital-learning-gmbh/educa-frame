<?php

namespace App\Http\Controllers\API\V1\Classbook;

use App\FehlzeitTyp;
use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\UnterrichtController;
use App\KlassenbuchEintrag;
use App\Schuler;
use App\Schuljahr;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AbsenteeismController extends ApiController
{
    public function getForStudent(Request $request)
    {
        $schuler = Schuler::findOrFail($request->input("student_id"));

        $percent_entschuldigt = 0;
        $percent_uentschuldigt = 0;
        $anwesendCount = 0;

        $classbook = $schuler->klassenbuchTeilnahme()->with("eintrag")->with("typ")->with("eintrag.fach")->get();

        foreach ($classbook as $entry)
        {
            if(str_contains($entry->typ->name,"Anwesend"))
            {
                $anwesendCount++;
            }

            if(str_contains(strtolower($entry->typ->name),"entschuldigt"))
            {
                $percent_entschuldigt++;
            }

            if(str_contains(strtolower($entry->typ->name),"unentschuldigt") || str_contains(strtolower($entry->typ->name),"abwesend"))
            {
                $percent_uentschuldigt++;
            }
        }

        $percent_entschuldigt = $classbook->count() == 0 ? "-" : round($percent_entschuldigt / $classbook->count() * 100);
        $percent_uentschuldigt = $classbook->count() == 0 ? "-" : round($percent_uentschuldigt / $classbook->count() * 100);

        $unterricht = UnterrichtController::getUnterrichtWithoutEntwurf(Carbon::createFromDate(2022,01,01)->toDateTime(),Carbon::now()->toDateTime(),
            $schuler->id,"student",false,false,false,"release",false,false);

        $data = [];

        foreach ($classbook as $eintrag)
        {
            $data[]  = ["type" => "entry", "entry" => $eintrag];
        }

        foreach ($unterricht as $lesson)
        {
            if($lesson["type"] == "lesson" || $lesson["type"] == "lessonPlan") {
                if (array_key_exists("klassenbuch", $lesson) && !($lesson["klassenbuch"] instanceof KlassenbuchEintrag)) {
                    $data[] = ["type" => "teacher_missing", "lesson" => $lesson];
                }
            }
        }

        usort($data, function ($item1, $item2) {
            if($item1["type"] == "entry")
                $date = $item1["entry"]->eintrag->startDate;
            if($item1["type"] == "teacher_missing")
                $date = $item1["lesson"]["start"];

            if($item2["type"] == "entry")
                $date2 = $item2["entry"]->eintrag->startDate;
            if($item2["type"] == "teacher_missing")
                $date2 = $item2["lesson"]["start"];

            return Carbon::parse($date)->isBefore(Carbon::parse($date2));
        });

        return parent::createJsonResponse("kohorte ", false, 200, ["stats" => ["anwesendCount" => $anwesendCount, "klassenbuchCount" => $classbook->count(), "percent_entschuldigt" => $percent_entschuldigt, "percent_uentschuldigt" => $percent_uentschuldigt], "class_book_participation" => $data]);
    }
}
