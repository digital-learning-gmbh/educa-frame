<?php

namespace App\Http\Controllers\API\V1\Groups;

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\V1\MeetingController;
use App\ModelMeeting;
use App\Models\SectionMeeting;
use App\PermissionConstants;
use App\Section;
use Illuminate\Http\Request;

class SectionMeetingController extends ApiController
{
    public function getSectionMeetingDetails($sectionId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if ($cloud_user == null) {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }

        $section = Section::findOrFail($sectionId);

        if(!$section->isAllowed($cloud_user,PermissionConstants::EDUCA_MEETING_VIEW))
        {
            return $this->createJsonResponse("No permission.", true, 400);
        }

        $meeting = SectionMeeting::where("section_id","=",$sectionId)->first();
        if($meeting == null && $section->isAllowed($cloud_user,PermissionConstants::EDUCA_MEETING_EDIT)) {
            $meeting = new SectionMeeting();
            $meeting->name = $section->name;
            $meeting->section_id = $sectionId;
            $meeting->save();
        }

        $modelMeeting = ModelMeeting::find($meeting->meeting_id);
        if($meeting->meeting_id == null) {
            $modelMeeting = new ModelMeeting();
            $modelMeeting->type = "bigbluebutton";
            $modelMeeting->model_type = "sectionMeeting";
            $modelMeeting->model_id = $meeting->id;

            $meetingController = new MeetingController();
            $meetingController->createMeeting($modelMeeting);
            $modelMeeting->save();

            $meeting->meeting_id = $modelMeeting->id;
            $meeting->save();
        }

        $meeting->modelMeeting = $modelMeeting;

        return parent::createJsonResponse("meeting", false, 200, ["sectionMeeting" => $meeting]);
    }

    public function updateSectionMeetingDetails($sectionId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if ($cloud_user == null) {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }

        $section = Section::findOrFail($sectionId);

        if($section->isAllowed($cloud_user,PermissionConstants::EDUCA_MEETING_EDIT))
        {
            $meeting = SectionMeeting::where("section_id","=",$sectionId)->first();
            if($meeting != null)
            {
                $meeting->name = $request->input("name");
                $meeting->welcomeText = $request->input("welcomeText");
                $meeting->save();
            }
        }

        return $this->getSectionMeetingDetails($sectionId,$request);
    }
}
