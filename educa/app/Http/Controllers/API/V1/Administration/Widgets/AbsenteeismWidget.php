<?php


namespace App\Http\Controllers\API\V1\Administration\Widgets;
use App\Board;
use App\Klasse;
use App\KontaktBeziehung;
use App\Schule;
use App\Schuler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AbsenteeismWidget extends Widget
{
    /**
     * @OA\Get (
     *     tags={"administration", "v1", "widgets"},
     *     path="/api/v1/administration/widgets/absenteeism",
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
     *     @OA\Response(response="200", description="Returns data for a contact graph")
     * )
     */
    public function getAbsenteeism(Request $request)
    {
        $student = Schuler::findOrFail($request->input("student_id"));
        $schule = $student->schulen->first();
        $ue_min = 60;
        $ue_day = 8;
        if($schule != null) {
            $ue_min = $schule->getEinstellungen("ue_min", 60);
            $ue_day = $schule->getEinstellungen("ue_day", 8);
        }

        $result = DB::table('klassenbuch_teilnahmes')
            ->selectRaw("*, sum(length) as sum_duration")
            ->join("fehlzeit_typs","fehlzeit_typ_id","=","fehlzeit_typs.id")
            ->where('schuler_id',$student->id)
            ->where('fehlzeit_typs.standart','=',0)
            ->groupBy('fehlzeit_typ_id')->get();

        $data = [];
        $overall = 0;

        foreach ($result as $row)
        {
            $overall += $row->sum_duration;
            $singleRow = [];
            $singleRow["name"] = $student->display_name;
            $singleRow["fehlzeit_name"] = html_entity_decode($row->name);
            $singleRow["ue"] = round($row->sum_duration / $ue_min,2);
            $singleRow["days"] = round($row->sum_duration / $ue_min / $ue_day,2);
            $singleRow["course"] = join(", ",$student->klassen->pluck("name")->toArray());
            $data[] = $singleRow;
        }

        $singleRow = [];
        $singleRow["name"] = "";
        $singleRow["fehlzeit_name"] = "";
        $singleRow["ue"] = round($overall / $ue_min,2);
        $singleRow["days"] = round(($overall / $ue_min) / $ue_day,2);
        $singleRow["course"] = "<b>Gesamt</b>";
        $data[] = $singleRow;

        return parent::createJsonResponseStatic('', false, 200,["data" => [], "overall" => "",]);
    }

}
