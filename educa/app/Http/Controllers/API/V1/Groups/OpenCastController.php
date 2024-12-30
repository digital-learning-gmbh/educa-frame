<?php

namespace App\Http\Controllers\API\V1\Groups;

use App\Http\Controllers\API\ApiController;
use Illuminate\Http\Request;
use OpencastApi\Opencast;


class OpenCastController extends ApiController
{
    public function getSectionInformation(Request $request)
    {
        $opencastApi = new Opencast(config("opencast"));
        $events = [];
        $eventsResponse = $opencastApi->eventsApi->getAll();
        if ($eventsResponse['code'] == 200) {
            $events = $eventsResponse['body'];
        }

        return parent::createJsonResponse("meeting", false, 200, ["series" => $events]);
    }


    public function updateSectionMeetingDetails()
    {
        $opencastApi = new Opencast(config("opencast"));
    }
}
