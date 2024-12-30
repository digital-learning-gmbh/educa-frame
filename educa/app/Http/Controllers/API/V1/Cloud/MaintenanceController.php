<?php

namespace App\Http\Controllers\API\V1\Cloud;

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\V1\Cloud\Logs\LaravelLogReader;

class MaintenanceController extends ApiController
{
    public function getInformation()
    {
        $license = json_decode(file_get_contents(base_path("LICENSE.json")));
        $revision = json_decode(file_get_contents(base_path("REVISION.json")));
        $logs = (new LaravelLogReader())->get();
        return parent::createJsonResponse("system information",false, 200,[
            "license" => $license,
            "revision" => $revision,
            "logs"=> $logs
        ]);
    }
}
