<?php


namespace App\Http\Controllers\API\V1\Administration\Widgets;
use App\Board;
use App\Klasse;
use App\KontaktBeziehung;
use App\Schule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactsWidget extends Widget
{
    /**
     * @OA\Get (
     *     tags={"administration", "v1", "widgets"},
     *     path="/api/v1/administration/widgets/contacts",
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
    public function kontakte(Request $request)
    {
        $school = Schule::findOrFail($request->input("school"));
        $nodes = [];
        $kontakte = $school->kontakte;
        foreach ($kontakte as $kontakt)
        {
            $node = [];
            $node["id"] = $kontakt->id;
            $node["label"] = $kontakt->name;
            $node["color"] = $kontakt->type == "person" ? "#c70039" : "#111d5e";
            $node["font"] =  [];
            $node["font"]["color"] = 'white';
            $nodes[] = $node;
        }
        $beziehungen = [];
        $realtion = KontaktBeziehung::all();
        foreach ($realtion as $single)
        {
            $node = [];
            $node["from"] = $single->kontakt1;
            $node["to"] = $single->kontakt2;
            $node["arrows"] = "to";
            $node["label"] = $single->type;
            $beziehungen[] = $node;
        }
        return parent::createJsonResponseStatic('', false, 200,["nodes" => $nodes, "edges" => $beziehungen]);
    }

}
