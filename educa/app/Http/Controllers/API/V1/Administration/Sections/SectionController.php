<?php

namespace App\Http\Controllers\API\V1\Administration\Sections;

use App\Http\Controllers\API\ApiController;
use App\Klasse;
use Illuminate\Http\Request;

class SectionController extends ApiController
{
    /**
     * @OA\Get(
     *     tags={"sections", "v1"},
     *     path="/api/v1/administration/sections/course/{course_id}",
     *     description="",
     *     @OA\Parameter(
     *     name="course_id",
     *     required=true,
     *     in="path",
     *     description="id of the course",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Array of all sections of a course")
     * )
     */
    public function sectionForCourse($course_id, Request $request)
    {
        $course = Klasse::findOrFail($course_id);
        return parent::createJsonResponse("sections of a course",false, 200, $course->lehrabschnitte);
    }

}
