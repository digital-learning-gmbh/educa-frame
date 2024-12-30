<?php


namespace App\Http\Controllers\API\V1\Administration\Widgets;

use App\Http\Controllers\API\UnterrichtController;
use App\Schule;
use App\Schuljahr;
use App\SchuljahrEntwurf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class RoomOccupancyWidget extends Widget
{
    /**
     * @OA\Get (
     *     tags={"administration", "v1", "widgets"},
     *     path="/api/v1/administration/widgets/roomOccupancy",
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
     *     name="start",
     *     required=true,
     *     in="query",
     *     description="timestamp of the start",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="end",
     *     required=true,
     *     in="query",
     *     description="timestamp of the ende",
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
     *     name="year",
     *     required=true,
     *     in="query",
     *     description="id of the school year",
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
    public function occupancy(Request $request)
    {
        $school = Schule::findOrFail($request->input("school"));
        $year = Schuljahr::findOrFail($request->input("year"));
        $entwurf = SchuljahrEntwurf::findOrFail($request->input("draft"));

        $raume = $school->raume;
        $start= \Carbon\Carbon::createFromTimestamp($request->input("start"));
        $end= Carbon::createFromTimestamp($request->input("end"));

        $gesamtMinutes = 1;
        $timeslots = $year->getTimeslots;
        foreach ($timeslots as $timeslot)
        {
            $startTimeslot =  new Carbon(new \DateTime($timeslot->begin));
            $endTimeslot =  new Carbon(new \DateTime($timeslot->end));
            $gesamtMinutes += abs($startTimeslot->diffInMinutes($endTimeslot));
        }
        if($gesamtMinutes == 1)
        {
            // default
            $gesamtMinutes = 10 * 60;
        }

        $days = [];
        $iterator = $start;
        $endee = $end;
        for($i = 1; $i <= 7; $i++)
        {
            $days[$i] = 0;
        }

        while ($iterator->lessThan($endee))
        {
            $weekDay = $iterator->dayOfWeekIso;
            $days[$weekDay]++;
            $iterator->addDay();
        }
        $data = [];
        foreach($raume as $t)
        {
            $obj["raum"] = $t->name;
            $obj[1] = 0;
            $obj[2] = 0;
            $obj[3] = 0;
            $obj[4] = 0;
            $obj[5] = 0;
            $obj[6] = 0;
            $obj[7] = 0;
            $unterrichtsplan = UnterrichtController::getUnterrichtInternalWithoutFormat($start, $end, "room", $t->id, $entwurf);
            foreach ($unterrichtsplan as $lesson)
            {
                $tag = $lesson->getStartDate()->format("N");
                $startLesson = new Carbon($lesson->getStartDate());
                $endLesson = new Carbon($lesson->getEndDate());
                $obj[$tag] += $startLesson->diffInMinutes($endLesson);
            }
            $obj[1] = round($obj[1] / ($days[1] * $gesamtMinutes),4) * 100 ." %";
            $obj[2] = round($obj[2] / ($days[1] * $gesamtMinutes), 4) * 100 ." %";
            $obj[3] = round($obj[3] / ($days[1] * $gesamtMinutes), 4) * 100 ." %";
            $obj[4] = round($obj[4] / ($days[1] * $gesamtMinutes), 4) * 100 ." %";
            $obj[5] = round($obj[5] / ($days[1] * $gesamtMinutes), 4) * 100 ." %";
            $obj[6] = round($obj[6] / ($days[1] * $gesamtMinutes), 4) * 100 ." %";
            $obj[7] = round($obj[7] / ($days[1] * $gesamtMinutes), 4) * 100 ." %";



            $obj[1] = "<div class='".$this->getColorClass($obj[1])."'>".$obj[1]."</div>";
            $obj[2] = "<div class='".$this->getColorClass($obj[2])."'>".$obj[2]."</div>";
            $obj[3] = "<div class='".$this->getColorClass($obj[3])."'>".$obj[3]."</div>";
            $obj[4] = "<div class='".$this->getColorClass($obj[4])."'>".$obj[4]."</div>";
            $obj[5] = "<div class='".$this->getColorClass($obj[5])."'>".$obj[5]."</div>";
            $obj[6] = "<div class='".$this->getColorClass($obj[6])."'>".$obj[6]."</div>";
            $obj[7] = "<div class='".$this->getColorClass($obj[7])."'>".$obj[7]."</div>";

            $data[] = $obj;
        }

        return parent::createJsonResponseStatic('', false, 200,["data" => $data]);

    }

    private function getColorClass($value)
    {
        if($value> 100)
        {
            return "colorCell bg-danger  text-white";
        }
        if($value > 80)
        {
            return "colorCell bg-warning";
        }
        return "colorCell bg-light";
    }

}
