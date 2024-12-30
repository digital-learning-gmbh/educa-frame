<?php


namespace App\Http\Controllers\API\V1\Administration\Widgets;


use App\Aufgabe;
use App\Schule;
use Illuminate\Http\Request;

class OpenTaskWidget extends Widget
{

    /**
     * @OA\Get (
     *     tags={"administration", "v1", "widgets", "administrationtask"},
     *     path="/api/v1/administration/widgets/tasks/open",
     *     description="",
     *     @OA\Parameter(
     *     name="token",
     *     required=true,
     *     in="query",
     *     description="jwt token",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="school",
     *     required=true,
     *     in="query",
     *     description="id of the school",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Returns the tasks, that are assigend to the current loggedin user")
     * )
     */
    public function taskList(Request $request)
    {
        $school = Schule::findOrFail($request->input("school"));
        $aufgabenOpen = Aufgabe::where('status','new')->where('schule_id',$school->id)->take(100)->orderBy('created_at')->get();
        return parent::createJsonResponseStatic('', false, 200, [ "tasks" => $aufgabenOpen ]);
    }

    /**
     * @OA\Post (
     *     tags={"administration", "v1", "widgets", "administrationtask"},
     *     path="/api/v1/administration/widgets/tasks/task/{id}/status",
     *     description="",
     *     @OA\Parameter(
     *     name="token",
     *     required=true,
     *     in="query",
     *     description="jwt token",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="id",
     *     required=true,
     *     in="path",
     *     description="id of the task ",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="status",
     *     required=true,
     *     in="query",
     *     description="new status of the task",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Updates the status of a task")
     * )
     */
    public function updateTaskStatus($id, Request $request)
    {
        $aufgabe = Aufgabe::findOrFail($id);
        $aufgabe->status = $request->input("status");
        $aufgabe->cloud = parent::getAdministationUser()->id;
        $aufgabe->save();
        return parent::createJsonResponseStatic('Task status updated', false, 200, [ "task" => $aufgabe ]);
    }

}
