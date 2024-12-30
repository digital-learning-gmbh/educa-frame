<?php

namespace App\Http\Controllers\API\V1\Administration;

use App\AdditionalInfo;
use App\Http\Controllers\API\ApiController;
use App\Kontakt;
use App\Lehrer;
use App\Schuler;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSearchController extends ApiController
{

    public function search(Request $request)
    {
        $search_term = $request->input("q");
        $search_term = str_replace("'","",$search_term);
        $school_id = $request->input("school_id");

        $members = $this->getMemberSearchResult($search_term);
        $companys = $this->getCompanaySearchResult($search_term);
        $ansprechpartner = $this->getContactSearchResult($search_term);
        $dozenten = $this->getDozentenSearchResult($search_term);
        $students = $this->getStudentenSearchResult($search_term);

        return parent::createJsonResponse("serach results", false, 200, [ "search" => [
            "employees" => $members,
            "companys" => $companys,
            "contact" => $ansprechpartner,
            "teacher" => $dozenten,
            "students" => $students
        ]]);
    }

    public function getMemberSearchResult($q)
    {
        return User::where('firstname','LIKE', '%'.$q.'%')
            ->orWhere('lastname','LIKE', '%'.$q.'%')
            ->orWhere('email','LIKE', '%'.$q.'%')
            ->where('status','=','active')
            ->take(20)
            ->get();
    }

    public function getCompanaySearchResult($q)
    {
        return Kontakt::where('name','LIKE', '%'.$q.'%')
            ->where('type','=','unternehmen')
            ->where('status','=','active')
            ->take(20)
            ->get();
    }

    public function getContactSearchResult($q)
    {
        return Kontakt::where( function ($query) use ($q) {
            $query->where('firstname','LIKE', '%'.$q.'%')->orWhere('lastname','LIKE', '%'.$q.'%');
        })->where('type','=','person')
            ->where('status','=','active')
            ->take(20)
            ->get();
    }

    public function getDozentenSearchResult($q)
    {
        return Lehrer::where('firstname','LIKE', '%'.$q.'%')
            ->orWhere('lastname','LIKE', '%'.$q.'%')
            ->where('status','=','active')
            ->take(20)
            ->get();
    }

    public function getStudentenSearchResult($q)
    {
        $elements = AdditionalInfo::where("personalnummer","LIKE",$q."%")->join("schulers","info_id","=","additional_infos.id")->get();
        if(count($elements) != 0)
        {
            return Schuler::whereIn('info_id', AdditionalInfo::where("personalnummer","LIKE",$q."%")->pluck("id"))
                ->take(10)
                ->get();
        }
        $elements = DB::table('schulers')->whereRaw(
            'MATCH (firstname, lastname) AGAINST (\''.$q.'%\' IN NATURAL LANGUAGE MODE)'
        )->whereNull("deleted_at")->take(40)
            ->get();
        if(count($elements) == 0)
        {
            return Schuler::where('firstname','LIKE', '%'.$q.'%')
                ->orWhere('firstname2','LIKE', '%'.$q.'%')
                ->orWhere('lastname','LIKE', '%'.$q.'%')
                ->take(10)
                ->get();
        }
        return $elements;
    }
}
