<?php


namespace App\Http\Controllers\API\V1\Administration\Widgets;


use App\Board;
use App\Http\Controllers\API\UnterrichtController;
use App\Http\Controllers\Stundenplan\StundenplanController;
use App\Klasse;
use App\Lehrer;
use App\LehrerFehlzeit;
use App\Schule;
use App\SchuljahrEntwurf;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SickReportsWidget extends Widget
{
    public function approveAbsence($id, Request $request)
    {
        $entwurf = SchuljahrEntwurf::findOrFail($request->input("draft"));
        $fehlzeit = LehrerFehlzeit::findOrFail($id);
        $fehlzeit->status = "genehmigt";
        $fehlzeit->save();

        $unterricht = UnterrichtController::getUnterrichtInternalWithoutFormat(new \DateTime($fehlzeit->startDate), new \DateTime($fehlzeit->endDate), "teacher", $fehlzeit->dozent_id, $entwurf);
        foreach($unterricht as $event)
        {
            if(UnterrichtController::getLessonTyp($event) == "lessonPlan") //Event ist Teil eines lessonplan -> Ausnahme-Lessons für betroffene Klassen erzeugen
            {
                foreach($event->klassen->pluck('id') as $klassenId)
                {
                    $newRequest = new Request();
                    $newRequest->setMethod("POST");
                    $newRequest->request->add(
                        [
                            "event_id" => $event->id,
                            "start" => $event->getStartDate()->format("Y-m-d H:i:s"),
                            "end" => $event->getEndDate()->format("Y-m-d H:i:s"),
                            "grund" => $fehlzeit->reason,
                            "fach_id" =>$event->fach->id,
                            "lehrer_id" => $event->dozent->id,
                            "raum_id" => $event->raum == null ? null : $event->raum->id,
                            "klasse_id" => $klassenId,
                            "type" => "ausfall"
                        ]
                    );
                    (new StundenplanController)->createLessonPlanException($newRequest);
                }
            }
            elseif(UnterrichtController::getLessonTyp($event) == "lesson") //Lesson auf "ausfall" setzen
            {
                $event->type = "ausfall";
                $event->save();
            }
        }

        return parent::createJsonResponseStatic('updated', false, 200);
    }

    /**
     * @OA\Get (
     *     tags={"administration", "v1", "widgets"},
     *     path="/api/v1/administration/widgets/absence",
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
     *     name="school",
     *     required=true,
     *     in="query",
     *     description="id of the school",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="draft",
     *     required=true,
     *     in="query",
     *     description="id of the school year draft",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Returns a list of attendees")
     * )
     */
    public function sickReports(Request $request)
    {
        //Alle Lehrer der aktuellen Schule
        $school = Schule::findOrFail($request->input("school"));
        $entwurf = SchuljahrEntwurf::findOrFail($request->input("draft"));
        $lehrers = $school->lehrer()->get()->pluck("id")->toArray();

        $krankmeldungen = Array();

        $fehlzeiten = LehrerFehlzeit::whereIn("dozent_id", $lehrers)->where("status", "=", "ausstehend")->get();
        foreach($fehlzeiten as $fehlzeit)
        {
            $lehrer = Lehrer::findOrFail($fehlzeit->dozent_id);
            $unterricht = UnterrichtController::getUnterrichtInternal(new \DateTime($fehlzeit->startDate), new \DateTime($fehlzeit->endDate), "teacher", $fehlzeit->dozent_id, $entwurf);
            $klasses = [];

            foreach($unterricht as $event)
            {
                if($event["type"] == "lessonPlan") //LessonPlan
                {
                    foreach($event["klassen_id"] as $klassen_id)
                    {
                        $klasse = Klasse::findOrFail($klassen_id);
                        if(!in_array($klasse->name, $klasses))
                        {
                            $klasses[] = $klasse->name;
                        }
                    }
                }
                elseif($event["type"] == "lesson") //Lesson
                {
                    $klassen_id = DB::table('klasse_lesson')->where('lesson_id', "=", $event["id"])->first()->klasse_id;
                    $klasse = Klasse::find($klassen_id);
                    if($klasse->exists && !in_array($klasse->name, $klasses))
                    {
                        $klasses[] = $klasse->name;
                    }
                }
            }
            $krankmeldungen[] = Array("id" => $fehlzeit->id, "lehrer_name" => $lehrer->displayname, "klasses" => implode(",", $klasses));
        }

        return parent::createJsonResponseStatic('', false, 200,["sickreports" => $krankmeldungen]);
    }
}
