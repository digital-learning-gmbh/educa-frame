<?php

namespace App\Http\Controllers\API\V1\Administration\Timetable;

use App\Http\Controllers\API\ApiController;
use App\Imports\LessonImport;
use App\LehrplanEinheit;
use App\LehrplanGroups;
use App\Lesson;
use App\LessonPlan;
use App\Providers\AppServiceProvider;
use App\Schuler;
use App\Schuljahr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TimetableController extends ApiController
{
    /**
     * @OA\Post (
     *     tags={"administration", "v1", "timetable"},
     *     path="/api/v1/administration/timetable/lessonplan/create",
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
     *     name="start",
     *     required=true,
     *     in="query",
     *     description="timestamp of the start",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="end",
     *     required=true,
     *     in="query",
     *     description="timestamp of the ende",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="recurrenceUntil",
     *     required=true,
     *     in="query",
     *     description="the end timestamp of the lessonplan",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="draft",
     *     required=true,
     *     in="query",
     *     description="id of the draft",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="subject",
     *     required=true,
     *     in="query",
     *     description="id of the subject",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="type",
     *     required=true,
     *     in="query",
     *     description="type of the entity: room, schoolclass, teacher",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="id",
     *     required=true,
     *     in="query",
     *     description="id of the entity",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Creates a new lessonplan")
     * )
     */
    public function createLessonPlan(Request $request)
    {
        $schuljahr = Schuljahr::find($request->input("year_id"));
        if (!$schuljahr)
            return parent::createJsonResponse("Schoolyear not found.", true, 400);
        $lessonPlan = new LessonPlan();
        $lessonPlan->startDate = Carbon::createFromTimestamp($request->input("start"))->toDateTime();
        $lessonPlan->endDate = Carbon::createFromTimestamp($request->input("end"))->toDateTime();
        $lessonPlan->fach_id = $request->input("subject_id") && $request->input("subject_id") > 0 ? $request->input("subject_id") : null;
        $lessonPlan->schuljahr_entwurf_id = $request->input("draft_id");
        $lessonPlan->recurrenceType = $schuljahr->getEinstellungen("default_period_type", "weekly");
        $lessonPlan->planningState = "plan";
        $lessonPlan->recurrenceUntil = Carbon::createFromTimestamp($request->input("recurrenceUntil"))->toDateTime();
        $lessonPlan->save();

        $type = $request->input("type", "schoolclass");
        if ($type == "schoolclass") {
            DB::table('klasse_lesson_plan')->insert([
                'klasse_id' => $request->input("id"),
                'lesson_plan_id' => $lessonPlan->id,
            ]);
        } else if ($type == "teacher") {
            DB::table('lehrer_lesson_plan')->insert([
                'lehrer_id' => $request->input("id"),
                'lesson_plan_id' => $lessonPlan->id,
            ]);
        } else if ($type == "room") {
            DB::table('raum_lesson_plan')->insert([
                'raum_id' => $request->input("id"),
                'lesson_plan_id' => $lessonPlan->id,
            ]);
        }

        return parent::createJsonResponse("lessonplan created!", false, 200, ["lessonplan" => $lessonPlan]);
    }

    /**
     * @OA\Post (
     *     tags={"administration", "v1", "timetable"},
     *     path="/api/v1/administration/timetable/lessonplan/{id}/move",
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
     *     name="start",
     *     required=true,
     *     in="query",
     *     description="timestamp of the start",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="end",
     *     required=true,
     *     in="query",
     *     description="timestamp of the ende",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="id",
     *     required=true,
     *     in="path",
     *     description="id of the lessonplans",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Moves a lessonplan")
     * )
     */
    public function moveLessonPlan($id, Request $request)
    {
        $lessonPlan = LessonPlan::findOrFail($id);
        $lessonPlan->startDate = Carbon::createFromTimestamp($request->input("start"))->toDateTime();
        $lessonPlan->endDate = Carbon::createFromTimestamp($request->input("end"))->toDateTime();
        $lessonPlan->save();

        return parent::createJsonResponse("lessonplan moved", false, 200, ["lessonplan" => $lessonPlan]);
    }

    public function resourceMoveLessonPlan($id, Request $request)
    {

        $lessonPlan = LessonPlan::findOrFail($id);
        $lessonPlan->startDate = Carbon::createFromTimestamp($request->input("start"))->toDateTime();
        $lessonPlan->endDate = Carbon::createFromTimestamp($request->input("end"))->toDateTime();

        $type = $request->input("type", "schoolclass");
        if ($type == "schoolclass") {
            $lessonPlan->klassen()->sync([$request->input("new_resource_id")], [$request->input("old_resource_id")]);
        } else if ($type == "teacher") {
            $lessonPlan->dozent()->sync([$request->input("new_resource_id")], [$request->input("old_resource_id")]);
        } else if ($type == "room") {
            $lessonPlan->raum()->sync([$request->input("new_resource_id")], [$request->input("old_resource_id")]);
        }

        $lessonPlan->save();

        return parent::createJsonResponse("lessonplan moved and resources updates ", false, 200, ["lessonplan" => $lessonPlan]);
    }

    /**
     * @OA\Get (
     *     tags={"administration", "v1", "timetable"},
     *     path="/api/v1/administration/timetable/lessonplan/{id}",
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
     *     description="id of the lessonplans",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Moves a lessonplan")
     * )
     */
    public function infoLessonPlan($id, Request $request)
    {
        $lessonplan = self::getLessonPlan($id);
        $lessonplan->load("students");
        return parent::createJsonResponse("lessonplan information", false, 200, ["lessonplan" => $lessonplan]);
    }

    private function getLessonPlan($id)
    {
        $lessonPlan = LessonPlan::with('klassen')->findOrFail($id);
        $customAttributes = new \ArrayObject();
        foreach ($lessonPlan->merkmale as $merkmal) {
            $customAttributes[$merkmal->key] = $merkmal->value;
        }
        // TODO Refactor
        //rename
        $lessonPlan->courses = $lessonPlan->klassen;
        $lessonPlan->teacher_id = $lessonPlan->dozent->pluck("id");
        $lessonPlan->subject_id = $lessonPlan->fach_id;
        $lessonPlan->room_id = $lessonPlan->raum->pluck("id");
        $lessonPlan->custom_attributes = $customAttributes;
        //Unset german names
        unset($lessonPlan->dozent);
        unset($lessonPlan->fach);
        unset($lessonPlan->klassen);
        unset($lessonPlan->merkmale);
        unset($lessonPlan->dozent);
        unset($lessonPlan->fach_id);
        unset($lessonPlan->raum_id);
        unset($lessonPlan->lehrer_id);

        return $lessonPlan;
    }

    private function getLesson($id)
    {
        $lesson = Lesson::with('klassen')->findOrFail($id);
        $customAttributes = new \ArrayObject();
        foreach ($lesson->merkmale as $merkmal) {
            $customAttributes[$merkmal->key] = $merkmal->value;
        }
        // TODO Refactor
        //rename
        $lesson->courses = $lesson->klassen;
        $lesson->teacher_id = $lesson->dozent->pluck("id");
        $lesson->subject_id = $lesson->fach_id;
        $lesson->room_id = $lesson->raum->pluck("id");
        $lesson->reason_type = $lesson->type;
        $lesson->custom_attributes = $customAttributes;
        //Unset german names
        unset($lesson->dozent);
        unset($lesson->fach);
        unset($lesson->klassen);
        unset($lesson->merkmale);
        unset($lesson->dozent);
        unset($lesson->fach_id);
        unset($lesson->raum_id);
        unset($lesson->lehrer_id);
        unset($lesson->type);

        return $lesson;
    }

    /**
     * @OA\Post (
     *     tags={"administration", "v1", "timetable"},
     *     path="/api/v1/administration/timetable/lessonplan/{id}/update",
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
     *     in="query",
     *     description="id of the lessonplan",
     *       @OA\Schema(
     *         type="integer"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="start",
     *     required=true,
     *     in="query",
     *     description="timestamp of the start",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="end",
     *     required=true,
     *     in="query",
     *     description="timestamp of the ende",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="recurrenceUntil",
     *     required=true,
     *     in="query",
     *     description="the end timestamp of the lessonplan",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="subject",
     *     required=true,
     *     in="query",
     *     description="id of the subject",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="room",
     *     required=true,
     *     in="query",
     *     description="id of the room",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="teacher",
     *     required=true,
     *     in="query",
     *     description="id of the teacher",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="schoolclasses",
     *     required=true,
     *     in="query",
     *     description="ids of the schoolclasses",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="description",
     *     required=true,
     *     in="query",
     *     description="description of the lessonplan",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="subtitle",
     *     required=true,
     *     in="query",
     *     description="subtitle of the lessonplan",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="recurrenceType",
     *     required=true,
     *     in="query",
     *     description="recurrenceType of the lessonplan",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="recurrenceTurnus",
     *     required=true,
     *     in="query",
     *     description="recurrenceTurnus of the recurrenceType",
     *       @OA\Schema(
     *         type="integer"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="properties_",
     *     required=true,
     *     in="query",
     *     description="properties of the lessonplan",
     *       @OA\Schema(
     *         type="integer"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Updates a lessonplan")
     * )
     */
    public function updateLessonPlan($id, Request $request)
    {
        $plan = $request->lessonPlan;

        if (!$plan)
            return parent::createJsonResponse("object invalid", true, 400);

        $lessonPlan = LessonPlan::findOrFail($id);
        $lessonPlan->startDate = Carbon::createFromTimestamp($plan["startDate"])->toDateTime();
        $lessonPlan->endDate = Carbon::createFromTimestamp($plan["endDate"])->toDateTime();
        $lessonPlan->recurrenceType = $plan["recurrenceType"];
        $lessonPlan->recurrenceTurnus = $plan["recurrenceTurnus"];
        $lessonPlan->recurrenceUntil = Carbon::createFromTimestamp($plan["recurrenceUntil"])->toDateTime();


        if (array_key_exists("subject_id", $plan) && $plan["subject_id"] != null) {
            $lessonPlan->fach_id = $plan["subject_id"];
        } else {
            $lessonPlan->fach_id = null;
        }
        $lessonPlan->description = $plan["description"];
        $lessonPlan->subtitle = $plan["subtitle"];
        $lessonPlan->planningState = $plan["planningState"];
        if (array_key_exists("deviant_ue", $plan)) {
            $lessonPlan->deviant_ue = $plan["deviant_ue"];
        }
        $lessonPlan->save();
        // Speichere die Merkmale
        $custom_attributes = $plan["custom_attributes"];
        foreach ($custom_attributes as $key => $value) {
            $lessonPlan->setMerkmal($key, $value);
        }
        // Speichere die Klasse
        if (array_key_exists("courses", $plan)) {
            $lessonPlan->klassen()->sync($plan["courses"]);
        } else {
            $lessonPlan->klassen()->sync([]);
        }
        if (array_key_exists("teacher_id", $plan)) {
            $lessonPlan->dozent()->sync($plan["teacher_id"]);
        } else {
            $lessonPlan->dozent()->sync([]);
        }
        if (array_key_exists("room_id", $plan)) {
            $lessonPlan->raum()->sync($plan["room_id"]);
        } else {
            $lessonPlan->raum()->sync([]);
        }
        return parent::createJsonResponse("lessonplan information has been updated", false, 200, ["lessonplan" => $lessonPlan]);
    }

    /**
     * @OA\Post (
     *     tags={"administration", "v1", "timetable"},
     *     path="/api/v1/administration/timetable/lessonplan/{id}",
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
     *     description="id of the lessonplans",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Removes a lessonplan")
     * )
     */
    public function deleteLessonPlan($id, Request $request)
    {
        $lessonPlan = LessonPlan::findOrFail($id);
        $lessons = Lesson::where('parentId', $lessonPlan->id)->get();
        foreach ($lessons as $lesson)
        {
            DB::table('klasse_lesson')->where([
                'lesson_id' => $lesson->id,
            ])->delete();
            DB::table('lehrer_lesson')->where([
                'lesson_id' => $lesson->id,
            ])->delete();
            DB::table('raum_lesson')->where([
                'lesson_id' => $lesson->id,
            ])->delete();
            $lesson->delete();
        }

        DB::table('klasse_lesson_plan')->where([
            'lesson_plan_id' => $lessonPlan->id,
        ])->delete();
        DB::table('lehrer_lesson_plan')->where([
            'lesson_plan_id' => $lessonPlan->id,
        ])->delete();
        DB::table('raum_lesson_plan')->where([
            'lesson_plan_id' => $lessonPlan->id,
        ])->delete();
        $merkmale = $lessonPlan->merkmale;
        foreach ($merkmale as $merkmal) {
            $merkmal->delete();
        }
        $lessonPlan->delete();

        return parent::createJsonResponse("lessonplan was removed", false, 200);
    }

    // EXECPTIONS

    /**
     * @OA\Post (
     *     tags={"administration", "v1", "timetable"},
     *     path="/api/v1/administration/timetable/lesson/create",
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
     *     name="lessonplan_id",
     *     required=true,
     *     in="query",
     *     description="id of the lessonplan",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="start",
     *     required=true,
     *     in="query",
     *     description="timestamp of the new start",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="end",
     *     required=true,
     *     in="query",
     *     description="timestamp of the new ende",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="old_start",
     *     required=true,
     *     in="query",
     *     description="the old_start timestamp, for we create a expection",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="reason",
     *     required=true,
     *     in="query",
     *     description="text reason for the exeception",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="reason_type",
     *     required=true,
     *     in="query",
     *     description="type of the reeason: ausfall / vertretung",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="subject",
     *     required=true,
     *     in="query",
     *     description="id of the subject",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="room",
     *     required=true,
     *     in="query",
     *     description="id of the room",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="teacher",
     *     required=true,
     *     in="query",
     *     description="id of the teacher",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="schoolclasses",
     *     required=true,
     *     in="query",
     *     description="ids of the schoolclasses",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Creates a new exception of the lesson plan")
     * )
     */
    public function createLesson(Request $request)
    {
        //create new lesson
        $lessonPlan = LessonPlan::findOrFail($request->input("lessonplan_id"));

        $lessonObj = $request->lesson;
        if (!$lessonObj)
            return parent::createJsonResponse("object invalid", true, 400);

        $lesson = new Lesson();
        $date = Carbon::createFromTimestamp($lessonObj["startDate"])->toDateTime();

        $lesson->startDate = $date;
        $lesson->endDate = Carbon::createFromTimestamp($lessonObj["endDate"])->toDateTime();
        $lesson->parentId = $lessonPlan->id;
        $lesson->occurrenceDate = $lessonObj["old_start"] ? Carbon::createFromTimestamp($lessonObj["old_start"])->toDateTime() : null;
        $lesson->reason = $lessonObj["reason"];
        $lesson->subtitle = $lessonObj["subtitle"];
        $lesson->description = $lessonObj["description"];
        $lesson->fach_id = $lessonObj["subject_id"];
        $lesson->description = $lessonObj["description"];
        $lesson->planningState = $lessonObj["planningState"];
        $lesson->schuljahr_entwurf_id = $lessonPlan->schuljahr_entwurf_id;

        if ($lessonObj["reason_type"] == 'ausfall') {
            $lesson->type = 'ausfall';
        } else {
            $lesson->type = 'vertretung';
        }
        $lesson->save();
        // Speichere die Merkmale
        $custom_attributes = $lessonObj["custom_attributes"] ? $lessonObj["custom_attributes"] : [];
        foreach ($custom_attributes as $key => $value) {
            $lesson->setMerkmal($key, $value);
        }

        $lesson->save();
        $lesson->klassen()->sync($lessonObj["courses"]);
        $lesson->dozent()->sync($lessonObj["teacher_id"]);
        $lesson->raum()->sync($lessonObj["room_id"]);

        return parent::createJsonResponse("lesson was created", false, 200, ["lesson" => $lesson]);
    }

    /**
     * @OA\Get (
     *     tags={"administration", "v1", "timetable"},
     *     path="/api/v1/administration/timetable/lesson/{id}",
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
     *     description="id of the lesson exeception",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Removes a lessonplan")
     * )
     */
    public function infoLesson($id, Request $request)
    {
        $lesson = Lesson::with('klassen')->findOrFail($id);

        $lesson->teacher_id = $lesson->dozent->pluck("id");
        $lesson->room_id = $lesson->raum->pluck("id");

        if ($lesson->fach_id == null) {
            $lesson->fach_id = -1;
        }

        $customAttributes = new \ArrayObject();
        foreach ($lesson->merkmale as $merkmal) {
            $customAttributes[$merkmal->key] = $merkmal->value;
        }
        $lesson->custom_attributes = $customAttributes;
        $lessonPlan = $this->getLessonPlan($lesson->parentId);

        return parent::createJsonResponse("lessonplan exception", false, 200, ["lesson" => self::getLesson($lesson->id), "lessonplan" => $lessonPlan]);
    }

    /**
     * @OA\Post (
     *     tags={"administration", "v1", "timetable"},
     *     path="/api/v1/administration/timetable/lesson/{id}/delete",
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
     *     description="id of the lesson exeception",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Removes a lessonplan")
     * )
     */
    public function deleteLesson($id, Request $request)
    {
        $lesson = Lesson::findOrFail($id);
        DB::table('klasse_lesson')->where([
            'lesson_id' => $lesson->id,
        ])->delete();
        DB::table('lehrer_lesson')->where([
            'lesson_id' => $lesson->id,
        ])->delete();
        DB::table('raum_lesson')->where([
            'lesson_id' => $lesson->id,
        ])->delete();
        $lesson->delete();
        return parent::createJsonResponse("lessonplan exception was deleted", false, 200);
    }

    /**
     * @OA\Post (
     *     tags={"administration", "v1", "timetable"},
     *     path="/api/v1/administration/timetable/lesson/update",
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
     *     name="start",
     *     required=true,
     *     in="query",
     *     description="timestamp of the new start",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="end",
     *     required=true,
     *     in="query",
     *     description="timestamp of the new ende",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="reason",
     *     required=true,
     *     in="query",
     *     description="text reason for the exeception",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="reason_type",
     *     required=true,
     *     in="query",
     *     description="type of the reeason: ausfall / vertretung",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="subject",
     *     required=true,
     *     in="query",
     *     description="id of the subject",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="room",
     *     required=true,
     *     in="query",
     *     description="id of the room",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="teacher",
     *     required=true,
     *     in="query",
     *     description="id of the teacher",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="schoolclasses",
     *     required=true,
     *     in="query",
     *     description="ids of the schoolclasses",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Creates a new exception of the lesson plan")
     * )
     */
    public function updateLesson($id, Request $request)
    {
        $lesson = Lesson::findOrFail($id);

        $lessonObj = $request->lesson;
        if (!$lessonObj)
            return parent::createJsonResponse("object invalid", true, 400);

        if ($lesson->id !== $lessonObj["id"])
            return parent::createJsonResponse("Ids do not match.", true, 400);

        $lesson->startDate = Carbon::createFromTimestamp($lessonObj["startDate"])->toDateTime();
        $lesson->endDate = Carbon::createFromTimestamp($lessonObj["endDate"])->toDateTime();
        $lesson->occurrenceDate = $lessonObj["old_start"] ? Carbon::createFromTimestamp($lessonObj["old_start"])->toDateTime() : null;
        $lesson->reason = $lessonObj["reason"];
        $lesson->subtitle = $lessonObj["subtitle"];
        $lesson->description = $lessonObj["description"];
        $lesson->fach_id = $lessonObj["subject_id"];
        $lesson->description = $lessonObj["description"];
        $lesson->planningState = $lessonObj["planningState"];
        if (array_key_exists("deviant_ue", $lessonObj)) {
            $lesson->deviant_ue = $lessonObj["deviant_ue"];
        }

        if ($lessonObj["reason_type"] == 'ausfall') {
            $lesson->type = 'ausfall';
        } else {
            $lesson->type = 'vertretung';
        }
        $lesson->save();
        // Speichere die Merkmale
        $custom_attributes = $lessonObj["custom_attributes"] ? $lessonObj["custom_attributes"] : [];
        foreach ($custom_attributes as $key => $value) {
            $lesson->setMerkmal($key, $value);
        }

        $lesson->save();
        if (array_key_exists("courses", $lessonObj)) {
            $lesson->klassen()->sync($lessonObj["courses"]);
        } else {
            $lesson->klassen()->sync([]);
        }
        if (array_key_exists("teacher_id", $lessonObj)) {
            $lesson->dozent()->sync($lessonObj["teacher_id"]);
        } else {
            $lesson->dozent()->sync([]);
        }
        if (array_key_exists("room_id", $lessonObj)) {
            $lesson->raum()->sync($lessonObj["room_id"]);
        } else {
            $lesson->raum()->sync([]);
        }

        return parent::createJsonResponse("lesson was updated", false, 200, ["lesson" => $lesson]);
    }

    public function endSeriesLessonPlan($id, Request $request)
    {
        $lessonPlan = LessonPlan::findOrFail($id);
        $lessonPlan->recurrenceUntil = Carbon::createFromTimestamp($request->input("recurrenceUntil"))->toDateTime();
        $lessonPlan->save();

        return parent::createJsonResponse("lesson was ended", false, 200, ["lessonPlan" => $lessonPlan]);
    }

    public function getLessonPlanStudents($id, Request $request)
    {
        $lessonObj = LessonPlan::findOrFail($id);
        if (!$lessonObj)
            return parent::createJsonResponse("object invalid", true, 400);


        $studentsIds = [];
        $datum = date("Y/m/d", strtotime($lessonObj->startDate));
        $lehrplansIds = [];
        foreach ($lessonObj->klassen as $klasse) {
            $lehrplansIds = array_merge($lehrplansIds, $klasse->getLehrplan->pluck("id")->toArray());
            // try to find lehrplan-einhehit id
            $lehrplanEinheit = null; // LehrplanEinheit::where('fach_id', $lessonObj->fach_id)->whereIn('lehrplan_id',$klasse->getLehrplan->pluck("id")->toArray())->first();
            $group = null;
//            if($lehrplanEinheit != null && LehrplanGroups::find($lehrplanEinheit->profil_id))
//            {
//                $group = LehrplanGroups::find($lehrplanEinheit->profil_id);
//            }
            //
            foreach ($klasse->schulerAtDatum($datum)->orderBy('lastname')->get() as $schuler) {
                if($group != null && !DB::table('lehrplan_groups_schuler')->where('schuler_id','=', $schuler->id)->where('lehrplan_groups_id','=',$group->id)->exists())
                {
                    continue;
                }
                $studentsIds[] = $schuler->id;
            }
        }
        $students = Schuler::whereIn('id', $studentsIds)->orderBy('lastname')->orderBy('firstname')->get();

        $data = [];
        foreach ($students as $student)
        {
            $student->profile = ""; // implode(",",$student->getLehrplanGroups($lehrplansIds)->pluck("name")->toArray());
            $data[] = $student;
        }
        return parent::createJsonResponse("", false, 200, ["students" => $data]);
    }

    public function saveLessonPlanStudents($id, Request $request)
    {
        $lessonObj = LessonPlan::findOrFail($id);
        if (!$lessonObj)
            return parent::createJsonResponse("object invalid", true, 400);

        $toggle = $request->input("isManualStudents");
        $lessonObj->isManualStudents = $toggle;
        $lessonObj->save();

        if (!$toggle) {
            $lessonObj->students()->sync([]);
        } else {
            if (!$request->input("student_ids"))
                return parent::createJsonResponse("students array is null", true, 400);
            $lessonObj->students()->sync($request->input("student_ids"));
        }


        return $this->getLessonPlanStudents($id, $request);
    }

    public function uploadExcel(Request $request)
    {
        Excel::import(new LessonImport($request->input("course_id"), $request->input("draft_id")), $request->file('file'));
        return parent::createJsonResponse("All good!", false, 200, []);
    }
}
