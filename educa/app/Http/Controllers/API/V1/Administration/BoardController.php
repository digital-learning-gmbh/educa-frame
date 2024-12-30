<?php

namespace App\Http\Controllers\API\V1\Administration;

use App\Board;
use App\Http\Controllers\API\ApiController;
use App\Widget;
use App\WidgetCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoardController extends AdministationApiController
{

    /**
     * @OA\Get (
     *     tags={"administration", "v1", "board"},
     *     path="/api/v1/administration/board/{id}",
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
     *     description="The id of the board",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Board information")
     * )
     */
    public function getBoardInfo($boardId, Request $request)
    {
        $board = Board::findOrFail($boardId);
        $board->load('geteilt');

        return parent::createJsonResponse("Hier ist Board", false, 200, ["board" => $board]);
    }

    /**
     * @OA\Post (
     *     tags={"administration", "v1", "board"},
     *     path="/api/v1/administration/board/{id}",
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
     *     name="name",
     *     required=true,
     *     in="query",
     *     description="new name of the board",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="shared_users",
     *     required=true,
     *     in="query",
     *     description="Users with the board is shared ",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="id",
     *     required=true,
     *     in="path",
     *     description="The id of the board",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Board information")
     * )
     */
    public function updateBoard($boardId, Request $request)
    {
        $board = Board::findOrFail($boardId);
        $board->name = $request->input("name");
        $board->save();
        $board->geteilt()->sync($request->input("shared_users", []));

        $board->load('geteilt');

        return parent::createJsonResponse("board updated", false, 200, ["board" => $board]);
    }

    /**
     * @OA\Get (
     *     tags={"administration", "v1", "widgets", "board"},
     *     path="/api/v1/administration/widgets",
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
     *     @OA\Response(response="200", description="Loads all avaiable widgets ")
     * )
     */
    public function availableWidgets(Request $request)
    {
        //school comes from frontend
        $widget_categories = WidgetCategory::all();
        $widget_categories->load('widgets');
        return parent::createJsonResponse("", false, 200, ["categories" => $widget_categories]);
    }

    /**
     * @OA\Post (
     *     tags={"administration", "v1", "widgets", "board"},
     *     path="/api/v1/administration/board/{id}/addWidget",
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
     *     name="widget",
     *     required=true,
     *     in="query",
     *     description="id of the widget",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Adds a widget to the dashboard")
     * )
     */
    public function addWidget($boardId, Request $request)
    {
        $board = Board::findOrFail($boardId);
        $widget = Widget::findOrFail($request->input("widget"));
        DB::table('board_widget')->insert([
            'board_id' => $board->id,
            'widget_id' => $widget->id
        ]);

        $board->load('geteilt');

        return parent::createJsonResponse("widget added", false, 200, ["board" => $board]);
    }

    /**
     * @OA\Post (
     *     tags={"administration", "v1", "widgets", "board"},
     *     path="/api/v1/administration/board/{id}/removeWidget",
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
     *     name="relation_id",
     *     required=true,
     *     in="query",
     *     description="relation_id of the widget and the dashboard, since their can be multiple widgets with the same id",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Removes a widget from the dashboard")
     * )
     */
    public function deleteWidget($boardId, Request $request)
    {
        if(!$request->has("relation_id"))
            return parent::createJsonResponse("need relation_id param", true, 401);
        $board = Board::findOrFail($boardId);
        DB::table('board_widget')->where(
            ['board_id' => $board->id,
                'id' => $request->input("relation_id","")
            ])->delete();

        $board->load('geteilt');

        return parent::createJsonResponse("widget removed", false, 200, ["board" => $board]);
    }

    /**
     * @OA\Post (
     *     tags={"administration", "v1", "widgets", "board"},
     *     path="/api/v1/administration/board/{id}/order",
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
     *     name="data",
     *     required=true,
     *     in="query",
     *     description="json data of the widget order",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Updates the order of the widgets and stores additional data in the board information")
     * )
     */
    public function updateWidgetOrder($boardId, Request $request)
    {
        $board = Board::findOrFail($boardId);
        $board->layout = $request->input("layout");
        $board->save();

        $board->load('geteilt');

        return parent::createJsonResponse("widget added", false, 200, ["board" => $board]);
    }

    /**
     * @OA\Post (
     *     tags={"administration", "v1", "widgets", "board"},
     *     path="/api/v1/administration/board/{id}/delete",
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
     *     @OA\Response(response="200", description="Deletes the board")
     * )
     */
    public function deleteBoard($boardId, Request $request)
    {
        $board = Board::findOrFail($boardId);
        $board->widgetsRelation()->sync([]);
        $board->delete();

        return parent::createJsonResponse("board deleted", false, 200);
    }
}
