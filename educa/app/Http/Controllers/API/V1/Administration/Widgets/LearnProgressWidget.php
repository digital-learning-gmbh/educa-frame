<?php


namespace App\Http\Controllers\API\V1\Administration\Widgets;

use App\Klasse;
use App\KlassenbuchEintrag;
use App\KlassenbuchTeilnahme;
use App\Schuler;
use App\Schuljahr;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LearnProgressWidget extends Widget
{
    /**
     * @OA\Get (
     *     tags={"administration", "v1", "widgets", "learnprogess"},
     *     path="/api/v1/administration/progress/student",
     *     description="",
     *     @OA\Parameter(
     *     name="token",
     *     required=true,
     *     in="query",
     *     description="jwt token",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="student",
     *     required=true,
     *     in="query",
     *     description="id of the student",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="year",
     *     required=true,
     *     in="query",
     *     description="id of the year",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="end",
     *     required=true,
     *     in="query",
     *     description="end of the timeframe",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Loads the learn progess of a student")
     * )
     */
    public function student(Request $request)
    {
        $entries = [];
        // hol die Lerpläne
        $schuler = Schuler::findOrFail($request->input("student"));
        $schoolyear = Schuljahr::findOrFail($request->input("year"));
        $start = $request->input("end");
        $klassen = $schuler->klasseForSchuljahr($schoolyear->id);
        $lehrplan_ids = [];
        $lehrplane = [];
        foreach ($klassen as $klasse) {
            foreach ($klasse->getLehrplan as $lehrplan) {
                if (!in_array($lehrplan->id, $lehrplan_ids)) {
                    $lehrplane[] = $lehrplan;
                    $lehrplan_ids[] = $lehrplan->id;
                }
            }
        }
        $fachToUe = [];
        // nun bauen wir uns eine map von fach -> ues
        $klassenbuchTeilnahmen = KlassenbuchTeilnahme::where('schuler_id', '=', $schuler->id)->whereDate('created_at', '<=', Carbon::createFromTimestamp($start))->get();
        foreach ($klassenbuchTeilnahmen as $klassenbuchTeilnahme) {
            if ($klassenbuchTeilnahme->eintrag_id != null) {
                $klassbuchEintrag = KlassenbuchEintrag::find($klassenbuchTeilnahme->eintrag_id);
                if ($klassbuchEintrag == null)
                    continue;
                if ($klassbuchEintrag->fach_id != null) {
                    if (!array_key_exists($klassbuchEintrag->fach_id, $fachToUe)) {
                        $fachToUe[$klassbuchEintrag->fach_id] = 0;
                    }
                    $fachToUe[$klassbuchEintrag->fach_id] += $klassenbuchTeilnahme->length;
                }
            }
        }
        // die map packen wir jetzt in das rekursive und ziehen immer was ab, wenn es gebraucht wird
        foreach ($lehrplane as $lehrplan) {
            $html = [];
            $subReponse = $this->getSubLernheitHtml($lehrplan->lehreinheiten(232), $fachToUe, $lehrplan->dauer_UE);
            $html["lehrplan"] = $lehrplan;
            $html["ue"] = $subReponse["ue"] ;
            $html["percent"] = $this->getPercent($subReponse["ue"], $lehrplan->getGesamtUE());
            $html["children"][] =  $subReponse;
            $entries[] = $html;
        }
        return parent::createJsonResponseStatic('', false, 200, ["progress_student" => $entries]);
    }

    private function getSubLernheitHtml($lerneinheiten, $fachToUe, $ueFactor)
    {
        $responseObject = [];
        $responseObject["ue"] = 0;
        $responseObject["children"] = [];
        foreach ($lerneinheiten as $lerneinheit) {
            $subHtml = $this->getSubLernheitHtml($lerneinheit->children(), $fachToUe, $ueFactor);
            if (count($subHtml["children"]) != 0) {
                $ueWert = $subHtml["ue"];
            } else {
                $ueWert = $this->ueForFach($fachToUe, $lerneinheit->fach_id, $lerneinheit->anzahl, $ueFactor);
            }
            $responseObject["ue"] += $ueWert;

            $gesamtUE = $lerneinheit->getUE();

            $html = [];
            $html["lerneinheit"] = $lerneinheit;
            $html["ue"] = round($ueWert . " / " . $gesamtUE, 2);
            $html["percent"] = $this->getPercent($ueWert, $gesamtUE);
            if (count($subHtml["children"]) > 0) {
                $html["children"] = [];
                array_push($html["children"], $subHtml);
            }
            $responseObject["children"];
            array_push($responseObject["children"], $html);
        }

        return $responseObject;
    }


    /**
     * @OA\Get (
     *     tags={"administration", "v1", "widgets", "learnprogess"},
     *     path="/api/v1/administration/progress/course",
     *     description="",
     *     @OA\Parameter(
     *     name="token",
     *     required=true,
     *     in="query",
     *     description="jwt token",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="course",
     *     required=true,
     *     in="query",
     *     description="id of the student",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="end",
     *     required=true,
     *     in="query",
     *     description="end of the timeframe",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Loads the learn progess of a student")
     * )
     */
    public function course(Request $request)
    {
        $entries = [];
        // hol die Lerpläne
        $klasse = Klasse::findOrFail($request->input("course"));
        $start = $request->input("end");

        $klassenbuchEintraege = KlassenbuchEintrag::whereHas('klasse', function($q) use($klasse) {
            $q->where('klasse_id', $klasse->id);
        })->whereDate('created_at', '<=', Carbon::createFromTimestamp($start)->toDateTime())->get();
        // nun bauen wir uns eine map von fach -> ues
        $fachToUe = [];
        foreach ($klassenbuchEintraege as $klassbuchEintrag) {
            if ($klassbuchEintrag->fach_id != null) {
                if (!array_key_exists($klassbuchEintrag->fach_id, $fachToUe)) {
                    $fachToUe[$klassbuchEintrag->fach_id] = 0;
                }
                $startLesson = new Carbon($klassbuchEintrag->startDate);
                $endLesson = new Carbon($klassbuchEintrag->endDate);
                $fachToUe[$klassbuchEintrag->fach_id] += $startLesson->diffInMinutes($endLesson);
            }
        }
        // die map packen wir jetzt in das rekursive und ziehen immer was ab, wenn es gebraucht wird
        foreach ($klasse->getLehrplan as $lehrplan) {
            $html = [];
            $subReponse = $this->getSubLernheitHtml($lehrplan->lehreinheiten(232), $fachToUe, $lehrplan->dauer_UE);

            $html["lehrplan"] = $lehrplan;
            $html["ue"] = round($subReponse["ue"] . " / " . $lehrplan->getGesamtUE(),2);
            $html["percent"] = $this->getPercent($subReponse["ue"], $lehrplan->getGesamtUE());
            $html["children"][] =  $subReponse;

            $entries[] = $html;
        }

        return parent::createJsonResponseStatic('', false, 200, ["progress_course" => $entries]);
    }

    private function ueForFach($fachToUE, $fach, $maxValue, $ueFactor)
    {
        if (!array_key_exists($fach, $fachToUE))
            return 0;
        $ueAvai = $fachToUE[$fach] / $ueFactor;
        $wertAbziehen = min($ueAvai, $maxValue);
        $fachToUE[$fach] -= $wertAbziehen;
        return $wertAbziehen;
    }

    private function getPercent($ueReal, $ueSoll)
    {
        if ($ueSoll == 0)
            return 100;
        return round($ueReal / $ueSoll * 100, 2);
    }
}
