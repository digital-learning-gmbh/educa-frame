<?php


namespace App\Http\Controllers\API\V1\Administration\Widgets;


use App\Aufgabe;
use Illuminate\Http\Request;

class MyTasksWidget extends Widget
{

    /**
     * @OA\Get (
     *     tags={"administration", "v1", "widgets", "administrationtask"},
     *     path="/api/v1/administration/widgets/tasks/my",
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
     *     @OA\Response(response="200", description="Returns the tasks, that are assigend to the current loggedin user")
     * )
     */
    public function taskList(Request $request)
    {
     $user = parent::getAdministationUser();
        if($user == null)
            return parent::createJsonResponseStatic('no administration user!', false, 200, [ "tasks" => [] ]);
        $aufgabenOpen = Aufgabe::where('status','working')->where('cloud',$user->id)->get();

        return parent::createJsonResponseStatic('', false, 200, [ "tasks" => $aufgabenOpen ]);
    }

}
