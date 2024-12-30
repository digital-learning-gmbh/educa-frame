<?php

namespace App\Http\Controllers\API\V1\Administration\Classbook;

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\Stammdaten\SPlusApiStammdatenController;
use App\Lehrer;
use App\Providers\AppServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DozentenController extends ApiController
{

    public function canViewAllClassTimetables(Request $request)
    {
        $cloud = parent::getUserForToken($request);
        return parent::createJsonResponse("security check", false, 200, ["canViewAllClassTimetables" => $cloud->hasPermissionTo("dozent.stundenplan.alleKlassen")]);
    }

    public function me(Request $request)
    {
        $cloud = parent::getUserForToken($request);
        if($request->has("teacher_id"))
        {
            $teacher = Lehrer::find($request->input("teacher_id"));
        } else {

            $teacher = $cloud->dozentUser();
        }

        if($teacher == null)
            return parent::createJsonResponse("no teacher found", true, 400);
        $klassen = SPlusApiStammdatenController::getKlassesOfTeacher($teacher->id, null);
        $alleKlassen = [];
        foreach ($teacher->schulen as $schule)
        {
            $schuljahr = $schule->getCurrentSchoolYear();
            if($schuljahr != null)
            {
                foreach ($schuljahr->klassenAktiv as $klasse)
                {
                    $alleKlassen[] = $klasse;
                }
            }
        }

        return parent::createJsonResponse("me route dozent", false, 200, ["all_schoolclass" => $alleKlassen, "own_schoolclass" => $klassen]);
    }
}
