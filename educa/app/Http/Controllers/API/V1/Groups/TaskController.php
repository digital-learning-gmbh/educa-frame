<?php

namespace App\Http\Controllers\API\V1\Groups;

use App\Http\Controllers\API\ApiController;
use App\PermissionConstants;
use App\Section;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TaskController extends ApiController
{
    /**
     * @OA\Post (
     *     tags={"v1","task"},
     *     path="/api/v1/groups/sections/{sectionId}/task",
     *     description="Tasks of the current user",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="sectionId",
     *       required=true,
     *       in="path",
     *       description="id of the section",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="type",
     *       required=false,
     *       in="query",
     *       description="type: open, review, close, if the parameter is not set, it will return any type",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="start",
     *       required=false,
     *       in="query",
     *       description="start timestamp",
     *         @OA\Schema(
     *           type="date"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="end",
     *       required=false,
     *       in="query",
     *       description="end timestamp",
     *         @OA\Schema(
     *           type="date"
     *         )
     *     ),
     *     @OA\Response(response="200", description="")
     * )
     */
    public function getTask($sectionId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);

        $section = Section::find($sectionId);
        if(!$section->isAllowed($cloud_user,PermissionConstants::EDUCA_TASK_OPEN))
        {
            return $this->createJsonResponse("No permission.", true, 400);
        }

        $tasks = \App\Http\Controllers\API\V1\TaskController::loadTaskForType($cloud_user,[$sectionId],false,null,null,false,false);

        return $this->createJsonResponse("ok", false,200, [ "tasks" => $tasks]);
    }
}
