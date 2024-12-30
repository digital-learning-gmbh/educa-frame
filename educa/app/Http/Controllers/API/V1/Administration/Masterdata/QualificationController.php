<?php

namespace App\Http\Controllers\API\V1\Administration\Masterdata;

use App\Http\Controllers\API\V1\Administration\AdministationApiController;
use App\Qualification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;


class QualificationController extends AdministationApiController
{

    /**
     * @OA\Post (
     *     tags={"v1","masterdata","teacher"},
     *     path="/api/v1/administration/qualifications/add",
     *     description="",
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
     *       name="date",
     *       required=true,
     *       in="query",
     *       description="the date on which the qualification was acquired",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="title",
     *       required=true,
     *       in="query",
     *       description="the title",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="type",
     *       required=true,
     *       in="query",
     *       description="the qualification's type",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="model_id",
     *       required=true,
     *       in="query",
     *       description="the id of the model which the qualification is to be attached to",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="model_type",
     *       required=true,
     *       in="query",
     *       description="the type of the model which the document is to be attached to",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="local",
     *       required=false,
     *       in="query",
     *       description="bool: whether gotten locally (iba)",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Create a new qualification for a model")
     * )
     */
    public function createQualification(Request $request)
    {
        $qualification = new Qualification();
        $qualification->model_type = $request->input('model_type');
        $qualification->model_id = $request->input("model_id");
        $qualification->type = $request->input("type");
        $qualification->title = $request->input("title");
        $timestamp = $request->input("date") == 0? null : $request->input("date");
        $qualification->date =  $timestamp? Carbon::createFromTimestamp($timestamp)->toDateTime() : null;

        if($request->has("local"))
            $qualification->local = $request->input("local");

        $qualification->save();

        return $this->getQualifications($qualification->model_type, $qualification->model_id, $request);
    }


    /**
     * @OA\Post (
     *     tags={"v1","masterdata","teacher"},
     *     path="/api/v1/administration/qualifications/{model_type}/{model_id}",
     *     description="",
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
     *       name="date",
     *       required=true,
     *       in="query",
     *       description="the date on which the qualification was acquired",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="title",
     *       required=true,
     *       in="query",
     *       description="the title",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="type",
     *       required=true,
     *       in="query",
     *       description="the qualification's type",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Get all qualifications for supplied model")
     * )
     */
    public function getQualifications($model_type, $model_id, Request $request)
    {
        $qualifications = Qualification::where("model_type", "=", $model_type)->where("model_id", "=", $model_id)->get();
        return $this->createJsonResponse("qualifications.", false, 200, ["qualifications" => $qualifications]);
    }

    /**
     * @OA\Post (
     *     tags={"v1","masterdata","teacher"},
     *     path="/api/v1/administration/qualifications/{id}/update",
     *     description="",
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
     *       name="id",
     *       required=true,
     *       in="path",
     *       description="qualification id",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="date",
     *       required=true,
     *       in="query",
     *       description="the date on which the qualification was acquired",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="title",
     *       required=true,
     *       in="query",
     *       description="the title",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="type",
     *       required=true,
     *       in="query",
     *       description="the qualification's type",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="local",
     *       required=false,
     *       in="query",
     *       description="bool: whether gotten locally (iba)",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Update a qualification")
     * )
     */
    public function updateQualification($qualification_id, Request $request)
    {
        $qualification = Qualification::findOrFail($qualification_id);

        $qualification->type = $request->input("type");
        $qualification->title = $request->input("title");
        $timestamp = $request->input("date") == 0? null : $request->input("date");
        $qualification->date =  $timestamp? Carbon::createFromTimestamp($timestamp)->toDateTime() : null;

        if($request->has("local"))
            $qualification->local = $request->input("local");

        $qualification->save();
        return $this->getQualifications($qualification->model_type, $qualification->model_id, $request);
    }

    /**
     * @OA\Post (
     *     tags={"v1","masterdata","teacher"},
     *     path="/api/v1/administration/qualifications/{id}/delete",
     *     description="",
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
     *       name="id",
     *       required=true,
     *       in="path",
     *       description="qualification id",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="delete a qualification")
     * )
     */
    public function deleteQualification($qualification_id, Request $request)
    {
        $qualification = Qualification::findOrFail($qualification_id);
        $modeltype = $qualification->model_type;
        $modelid = $qualification->model_id;
        $qualification->delete();
        return $this->getQualifications($modeltype, $modelid, $request);
    }

}
