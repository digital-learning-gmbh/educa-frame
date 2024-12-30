<?php

namespace App\Http\Controllers\API\V1\Analytics;

use App\Http\Controllers\API\ApiController;
use App\Models\LearnContent;
use App\xApiStatement;
use Illuminate\Http\Request;

class SettingsController extends ApiController
{
    public function downloadxAPI(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);

        $statements = xApiStatement::where("actor_id","=",$cloud_user->id)
            ->whereNotNull("context")->get()->makeHidden(['id','proxied'])->toArray();

        return response()->streamDownload(function () use ($statements) {
            echo json_encode($statements);
        }, "xapi.json");
    }
}
