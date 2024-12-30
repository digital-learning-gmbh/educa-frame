<?php

namespace App\Http\Controllers\API\V1\Administration;

use App\Board;
use App\Http\Controllers\API\ApiController;
use App\Widget;
use App\WidgetCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdministationApiController extends ApiController
{

    public function getAdministationUser()
    {
        $cloudUser = parent::user();
        if($cloudUser != null)
            return $cloudUser->administrationUser();
        return null;
    }



}
