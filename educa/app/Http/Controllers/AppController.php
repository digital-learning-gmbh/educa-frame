<?php

namespace App\Http\Controllers;

use App\Providers\AppServiceProvider;
use App\Schule;
use App\Schuljahr;
use App\SchuljahrEntwurf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AppController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:verwaltung');
    }

    public function displayUserView($view, $params = null) {
        $addParams = ['user' => Auth::guard('verwaltung')->user() ];
        if(Auth::guard('verwaltung')->user()->schulen->count() == 0)
            return "Sie sind keinem Standort zugeordnet!";
        $global_year = $this->getSchoolYear();
        $global_entwurf = $this->getEntwurf();
        $global_school = $this->getSchool();

        $addParams = array_merge ($addParams , ['global_school' => $global_school, 'global_year' => $global_year, 'global_entwurf' => $global_entwurf ] );
        if($params == null)
        {
            return parent::displayCloudView($view, $addParams );
        } else {
            return parent::displayCloudView($view, array_merge ($addParams , $params ));
        }
    }

    public function getSchool()
    {
        return AppServiceProvider::getSchool();
    }

    public function getEntwurf()
    {
        return AppServiceProvider::getEntwurf();
    }

    public function getSchoolYear()
    {
        return AppServiceProvider::getSchoolYear();
    }
}
