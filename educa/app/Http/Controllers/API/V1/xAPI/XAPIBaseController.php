<?php

namespace App\Http\Controllers\API\V1\xAPI;

use App\Beitrag;
use App\CloudID;
use App\Http\Controllers\API\ApiController;
use App\InteractiveCourse;
use App\InteractiveCourseChapter;
use App\Models\InteractiveCourseExecution;
use App\Models\InteractiveCourseProgress;
use App\Models\InteractiveCourseTopic;
use App\Models\InteractiveCourseTopicVariant;
use App\Models\LearnContent;
use App\xApiStatement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class XAPIBaseController extends ApiController
{
    public function create(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if ($cloud_user == null) {
            return parent::createJsonResponse("This token is not valid.", true, 400);
        }

        $context = $request->input("context");
        $object = $request->input("object");
        $verb = $request->input("verb");
        $result = $request->input("result");
        $created_at = $request->input("created_at");

        $statement = self::createStatement($cloud_user, $context, $object, $verb, $result, $created_at);

        return parent::createJsonResponse("successfully added", false, 200, ["statement" => $statement]);
    }


    private static function hook($context, $object, $verb, $result, $created_at, $cloud_user)
    {
        $contentId = $context && is_array($context) && array_key_exists("grouping", $context) ? (array_key_exists("learn_content_id", $context["grouping"]) ? $context["grouping"]["learn_content_id"] : null) : null;
        $interactiveCourseId = $context && is_array($context) && array_key_exists("grouping", $context) ? (array_key_exists("interactive_course_id", $context["grouping"]) ? $context["grouping"]["interactive_course_id"] : null) : null;
        $verbId = $verb && array_key_exists("id", $verb) ? $verb["id"] : null;


        if ($contentId && $interactiveCourseId && $verbId) {
            Log::info("Found xAPI Statement for contentId ".$contentId. " interactive course ".$interactiveCourseId);
            $variant = InteractiveCourseTopicVariant::whereIn("interactive_course_topic_id", InteractiveCourseTopic::whereIn("interactive_course_chapter_id", InteractiveCourseChapter::where("interactive_course_id", "=", $interactiveCourseId)->pluck("id"))->pluck("id"))
                ->whereIn("learn_content_id", LearnContent::where("id", "=", $contentId)->pluck("id"))->first();

            // use the last one
            $interactiveCourseExecution = InteractiveCourseExecution::where("cloud_id","=",$cloud_user->id)->where("interactive_course_id","=",$interactiveCourseId)->orderBy("created_at","DESC")->first();

            if ($variant && $variant->verb == $verbId) {
                Log::info("Verb is matched (".$verbId.") result object ".print_r($result,true));
                $scoreObject = $result && array_key_exists("score", $result) ? $result["score"] : null;
                if ($scoreObject == null)
                    return;

                if ($variant->finish_mode == "percent" &&
                    array_key_exists("scaled", $scoreObject)
                    && $scoreObject["scaled"]*100 >= $variant->required_score) {
                    // yes
                    $progress = new InteractiveCourseProgress();
                    $progress->interactive_course_execution_id = $interactiveCourseExecution->id;
                    $progress->interactive_course_topic_id = $variant->interactive_course_topic_id;
                    $progress->learn_content_id = $variant->learn_content_id;
                    $progress->success = true;
                    $progress->progress = 100;
                    $progress->save();
                }

                if ($variant->finish_mode == "raw" &&
                    array_key_exists("raw", $scoreObject)
                    && $scoreObject["raw"] >= $variant->required_score) {
                    // yes
                    $progress = new InteractiveCourseProgress();
                    $progress->interactive_course_execution_id = $interactiveCourseExecution->id;
                    $progress->interactive_course_topic_id = $variant->interactive_course_topic_id;
                    $progress->learn_content_id = $variant->learn_content_id;
                    $progress->success = true;
                    $progress->progress = 100;
                    $progress->save();
                }
            }

        }
    }

    public function createMulti(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if ($cloud_user == null) {
            return parent::createJsonResponse("This token is not valid.", true, 400);
        }

        $statements = json_decode($request->input("statements"));

        $results = [];
        foreach ($statements as $statement) {
            $context = property_exists($statement, "context") ? (array)$statement->context : null;
            $verb = property_exists($statement, "verb") ? (array)$statement->verb : null;
            $object = property_exists($statement, "object") ? (array)$statement->object : null;
            $created_at = property_exists($statement, "created_at") ? $statement->created_at : null;
            $result = property_exists($statement, "result") ? $statement->result : null;

            $results[] = self::createStatement($cloud_user, $context, $object, $verb, $result, $created_at);
        }

        return parent::createJsonResponse("successfully added", false, 200, ["statements" => $results]);
    }

    public static function createStatement(CloudID $actor, $context = null, $object = null, $verb = null, $result = null, $created_at = null)
    {
        self::hook($context, $object, $verb, $result, $created_at, $actor);
        $statement = new xApiStatement();
        $statement->actor =
            [
                "name" => $actor->name,
                "account" => [
                    "homepage" => "https://educa-portal.de",
                    "name" => $actor->email,
                ]
            ];
        $statement->actor_id = $actor->id;

        $statement->context = $context;

        if ($object != null) {
            if (array_key_exists("objectType", $object))
                $statement->object_type = $object["objectType"];
            if (array_key_exists("id", $object))
                $statement->object_id = $object["id"];
            $statement->object = $object;
        }

        $statement->verb = $verb;
        if ($verb != null && array_key_exists("display", $verb))
            $statement->verb_short = ((array)$verb["display"])["en-US"];

        if ($created_at != null)
            $statement->created_at = Carbon::parse($created_at);

        $statement->result = $result;

        $statement->save();

        // time for hooks


        // end time for hooks

        return $statement;
    }

    public static function objectFactory($object)
    {
        return [
            "objectType" => $object->getObjectType(),
            "id" => $object->getObjectId(),
            "definition" => [
                "name" => ["en-US" => $object->getObjectDisplay()]
            ]
        ];
    }
}
