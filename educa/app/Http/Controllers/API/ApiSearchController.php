<?php

namespace App\Http\Controllers\API;

use App\CloudID;
use App\Fach;
use App\Group;
use App\Klasse;
use App\Kontakt;
use App\KontaktBeziehung;
use App\Lehrer;
use App\Models\Devices\Ressource;
use App\Providers\AppServiceProvider;
use App\Raum;
use App\Schule;
use App\Schuler;
use App\Schuljahr;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Spatie\Tags\Tag;

class ApiSearchController extends ApiController
{
    /**
     * @OA\Get(
     *     tags={"search"},
     *     path="/api/search",
     *     description="",
     *     @OA\Parameter(
     *     name="q",
     *     required=true,
     *     in="query",
     *     description="The search string",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Search results")
     * )
     */
    public function search(Request $request)
    {
        $q = $request->json("q");
        return response()->json($this::searchAny($q));
    }

    public static function searchAny($q)
    {
       // $schuler = Schuler::search($q)->get();
       // $raume = Raum::search($q)->get();
       // $dozenten = Lehrer::search($q)->get();
        return ["schuler" => [], "raume" => [], "dozenten" => []];
    }

    public function class(Request $request)
    {
        if($request->input("schuljahr") == "")
        {
            $raume = Klasse::all();
        } else {
            $schoolyear = Schuljahr::findOrFail($request->input("schuljahr"));
            $raume = $schoolyear->klassen;


        }
        $result = [];
        $subjects = [];
        $check = false;

        $bestFit = [];
        $secondFit = [];
        foreach ($raume as $raum) {
            $element = [];
            $element["id"] = $raum->id;
            $element["text"] = $raum->displayName;
            $bestFit[] = $element;
        }

        /*$element = [];
        $element["id"] = -1;
        $element["text"] = "Keine Klasse";
        $result[] = $element;*/

        $element =[];
        $element["text"] = "Klassen";
        $element["children"] = $bestFit;
        $result[] = $element;

        return response()->json(["results" => $result]);
    }

	/**
     * @OA\Get(
     *     tags={"search"},
     *     path="/api/search/companies",
     *     description="",
     *     @OA\Parameter(
     *     name="q",
     *     required=true,
     *     in="query",
     *     description="The search string",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="type",
     *     required=true,
     *     in="query",
     *     description="The type string",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="relation_to",
     *     required=true,
     *     in="query",
     *     description="constraint the search to a kontakt that has a relation to ",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Search results")
     * )
     */
    public function companies(Request $request)
    {
        if(!$request->has("school_id")) {
            $school_id = Schule::get()->first();
        } else {
            $school_id = Schule::findOrFail($request->input("school_id"));
        }

        $q = $request->input("q");
        if($q == "")
        {
            $unternehmen = Kontakt::where('type', '=', $request->input("type"))->orderBy('name')->get();
        } else {
            $unternehmen = Kontakt::where('name', 'LIKE', '%'.$request->input("q").'%')->where('type', '=', $request->input("type"))->orderBy('name')->get();
        }
        $result = [];

        $element = [];
        $element["id"] = "-1";
        $element["text"] = "Keine Auswahl";
        $result[] = $element;

        foreach ($unternehmen as $raum) {
            if(in_array($school_id->id, $raum->schulen()->pluck("schule_id")->toArray())) {
                if($request->has("relation_to") && $request->input("relation_to") != "")
                {
                    $relation_to = $request->input("relation_to");
                    if(!KontaktBeziehung::where(function ($query) use($raum,$relation_to) {
                        $query->where('kontakt2', '=', $raum->id);
                        $query->where('kontakt1', '=', $relation_to);
                    })->orWhere(function ($query) use($raum,$relation_to)  {
                        $query->where('kontakt1', '=', $raum->id);
                        $query->where('kontakt2', '=', $relation_to);
                    })->exists())
                    continue;
                }
                $element = [];
                $element["id"] = $raum->id;
                $element["text"] = $raum->name;
                $result[] = $element;
            }
        }

        return response()->json(["results" => $result]);
    }


    public function clouduser(Request $request)
    {
        if($request->input("q","") == "")
        {
            $raume = CloudID::all();
        } else {
            $raume = CloudID::where('name','LIKE',"%".$request->input("q","")."%")->get();
        }
        $result = [];

        $bestFit = [];
        foreach ($raume as $raum) {
            if($request->input("rcNeed","false") == "true")
            {
                if($raum->rcUser() == null)
                    continue;
            }
            $element = [];
            $element["id"] = $raum->id;
            $element["text"] = $raum->displayName;
            $bestFit[] = $element;
        }

        $element =[];
        $element["text"] = "Benutzer";
        $element["children"] = $bestFit;
        $result[] = $element;

        return response()->json(["results" => $result]);
    }

    public function group(Request $request)
    {
        if($request->input("q","") == "")
        {
            $raume = Group::all();
        } else {
            $raume = Group::where('name','LIKE',"%".$request->input("q","")."%")->get();
        }
        $result = [];

        $bestFit = [];
        foreach ($raume as $raum) {
            $element = [];
            $element["id"] = $raum->id;
            $element["text"] = $raum->name;
            $bestFit[] = $element;
        }

        $element =[];
        $element["text"] = "Gruppen";
        $element["children"] = $bestFit;
        $result[] = $element;

        return response()->json(["results" => $result]);
    }

    public function tags(Request $request)
    {
        if($request->input("q","") == "")
        {
            $raume = Tag::all();
        } else {
            $raume = Tag::findFromString($request->input("q",""))->get();
        }
        $result = [];

        $bestFit = [];
        foreach ($raume as $raum) {
            $element = [];
            $element["id"] = $raum;
            $element["text"] = $raum;
            $bestFit[] = $element;
        }

        $element =[];
        $element["text"] = "Tags";
        $element["children"] = $bestFit;
        $result[] = $element;

        return response()->json(["results" => $result]);
    }

    public function getAllCloudUsers()
    {
        $this->createJsonResponse( "ok", false, 200, CloudID::all());
    }
}
