<?php


namespace App\Http\Controllers\API\V1\Administration\Widgets;

use App\Board;
use App\Klasse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class ActivitiesWidget extends Widget
{
    /**
     * @OA\Get (
     *     tags={"administration", "v1", "widgets", "activities"},
     *     path="/api/v1/administration/widgets/activities",
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
     *     @OA\Response(response="200", description="Returns the latest activities")
     * )
     */
    public function aktivitaeten(Request $request)
    {
        $aktivitaeten = Activity::inLog('default', 'klassenbuch')->orderBy('created_at','DESC')->take(100)->get();
        $data = [];
        foreach ($aktivitaeten as $aktivitaet) {
            $entry = [];
            $entry["id"] = $aktivitaet->id;
            $entry["link"] = $this->getLink($aktivitaet);
            $entry["causer"] = $this->getCauserDisplay($aktivitaet->causer);
            $entry["subject"] = $this->subject($aktivitaet->subject_type);
            $entry["detail_subject"] = $this->detailsSubject($aktivitaet);
            $entry["verb"] = $this->verb($aktivitaet->description);
            $entry["time"] = $aktivitaet->created_at;
            $data[] = $entry;

        }
        return parent::createJsonResponseStatic('', false, 200,[ "activities" => $data]);

    }

    public static function getCauserDisplay($causer)
    {
        if($causer != null)
        {
            return $causer->displayName;
        }
        return "System";
    }

    private function verb($verb)
    {
        if($verb == "created") {
            return "erstellt";
        } else if($verb == "updated")
        {
            return "aktualisiert";
        }
        return  "";
    }

    private function subject($subject)
    {
        if($subject == "App\AdditionalInfo")
        {
            return "Stammdaten";
        }
        if($subject == "App\Lehrer")
        {
            return "Dozent*in-Datensatz";
        }
        if($subject == "App\Schuler")
        {
            return "Studenten*in-Datensatz";
        }
        if($subject == "App\Raum")
        {
            return "Raum";
        }
        if($subject == "App\KlassenbuchEintrag")
        {
            return "Kursbuch-Eintrag";
        }
        return "";
    }

    private function detailsSubject($activitaet)
    {
        $subject = $activitaet->subject_type;
        if($subject == "App\Schuler" || $subject == "App\Lehrer" || $subject == "App\Raum")
        {
            if($activitaet->subject == null)
            {
                return  " Gelöscht";
            }
            return $activitaet->subject->displayName;
        }
        return "";
    }

    private function getLink($aktivitaet)
    {
        $subject = $aktivitaet->subject_type;
        if($subject == "App\Lehrer")
        {
            return "/verwaltung/stammdaten/dozenten/".$aktivitaet->subject_id."/edit";
        }
        if($subject == "App\Schuler")
        {
            return "/verwaltung/schulerlisten/".$aktivitaet->subject_id;
        }
        if($subject == "App\Raum")
        {
            return "/verwaltung/stammdaten/raume/".$aktivitaet->subject_id."/edit";
        }
        return "#";
    }
}
