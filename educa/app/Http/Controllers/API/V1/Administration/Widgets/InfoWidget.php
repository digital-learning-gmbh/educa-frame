<?php


namespace App\Http\Controllers\API\V1\Administration\Widgets;

use App\KlassenbuchEintrag;
use App\Schule;
use Illuminate\Http\Request;

class InfoWidget extends Widget
{
    /**
     * @OA\Get (
     *     tags={"administration", "v1", "widgets", "information"},
     *     path="/api/v1/administration/widgets/info",
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
     *     @OA\Response(response="200", description="Returns the basic information about the school and some other statistic stuff")
     * )
     */
    public function info(Request $request)
    {
        $school = Schule::findOrFail($request->input("school"));

        $school->load('schuler');
        $school->load('schuljahre');
        $klassenCount = 0;
        $klassenBuchEintrag = 0;
        foreach ($school->schuljahre as $schuljahr) {
            $klassenCount += $schuljahr->klassen->count();
            foreach ($schuljahr->klassen as $klasse) {
                $klassenBuchEintrag += KlassenbuchEintrag::whereHas('klasse', function($q) use($klasse) {
                    $q->where('klasse_id', $klasse->id);
                })->count();
        }
        }
        $school->load('raume');
      //  $system = new System;
        return parent::createJsonResponseStatic('', false, 200,["school" => $school, "klassenCount" => $klassenCount, "klassenBuchEintrag" => $klassenBuchEintrag]);
    }
}
