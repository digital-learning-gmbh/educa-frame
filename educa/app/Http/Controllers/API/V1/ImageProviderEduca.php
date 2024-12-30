<?php

namespace App\Http\Controllers\API\V1;

use App\CloudID;
use App\Group;
use App\Http\Controllers\Controller;
use App\InteractiveCourse;
use App\Klasse;
use App\Models\InteractiveCourseBadge;
use App\Models\LearnContent;
use App\Models\LearnContentCategory;
use App\RCUser;
use App\Schuler;
use App\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use LasseRafn\InitialAvatarGenerator\InitialAvatar;

class ImageProviderEduca extends Controller
{
    /**
     * @OA\Get(
     *     tags={"images, user"},
     *     path="/api/image/schuler",
     *     description="",
     *     @OA\Parameter(
     *     name="user_id",
     *     required=true,
     *     in="query",
     *     description="identifier of the user",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description=")
     * )
     */
    public function getUserImage(Request $request)
    {
        $schuler = Schuler::findOrFail($request->user_id);
        $avatar = new InitialAvatar();

        $image = $avatar->name($schuler->displayName)->size($request->input("size", 250))->generate();

        return response($image->stream('png', 100))->setCache(["max_age" => 3600]);
    }

    /**
     * @OA\Get(
     *     tags={"images, cloud"},
     *     path="/api/image/cloud",
     *     description="",
     *     @OA\Parameter(
     *     name="cloud_id",
     *     required=true,
     *     in="query",
     *     description="identifier of the cloud id",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description=")
     * )
     */
    public function cloud(Request $request)
    {
        $schuler = CloudID::findOrFail($request->input("cloud_id"));
        $imgName = $request->name;

        if ($schuler->image == null) {
            $avatar = new InitialAvatar();
            $image = $avatar->name($schuler->name)->size($request->input("size", 400))->generate();
            $name = str_random(32);
            $imgName = $name;
            Storage::disk('public')->put('/images/user/' . $name . ".png", $image->stream('png', 90));
            $schuler->image = $name;
            $schuler->save();
        }

        if (!$imgName || $schuler->image != $imgName || !Storage::disk('public')->has('/images/user/' . $imgName . ".png")) {
            return response("image not found", 404);
        }

        if ($request->has("size")) {
            $size = $request->input("size") <= 400 ? $request->input("size") : 400; //cap auf 400
            try {
                $image = (new ImageManager);
                $image = $image->make(Storage::disk('public')->path('/images/user/' . $imgName . ".png"));
                $image = $image->fit($size);
                return $image->response()->setCache(["max_age" => 3600]);
            } catch (\Exception $exception) {
                Log::warning("not possible to cache images!");
                return redirect("/storage/images/user/" . $schuler->image . ".png")->setCache(["max_age" => 3600]);
            }
        } else {
            return redirect("/storage/images/user/" . $schuler->image . ".png")->setCache(["max_age" => 3600]);
        }

        return response($image->stream('png', 100))->setCache(["max_age" => 3600]);
    }

    /**
     * @OA\Get(
     *     tags={"images, rocket.chat"},
     *     path="/api/image/rocketchat",
     *     description="",
     *     @OA\Parameter(
     *     name="id",
     *     required=true,
     *     in="query",
     *     description="username of the rocket.chat",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description=")
     * )
     */
    public function rocketchat(Request $request)
    {
        $rcUser = RCUser::where('uid', '=', $request->input("id"))->first();
        if ($rcUser == null) {
            return "";
        }
        $avatar = new InitialAvatar();
        $schuler = $rcUser->cloudID;
        $image = $avatar->name($schuler->name)->size($request->input("size", 250))->generate();

        return $image->stream('png', 100);
    }

    /**
     * @OA\Get(
     *     tags={"images, school class"},
     *     path="/api/image/klasse",
     *     description="",
     *     @OA\Parameter(
     *     name="user_id",
     *     required=true,
     *     in="query",
     *     description="identifier of the school class",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description=")
     * )
     */
    public function getKlasseImage(Request $request)
    {
        $klasse = Klasse::findOrFail($request->user_id);
        $avatar = new InitialAvatar();

        $image = $avatar->name($klasse->name)->size($request->input("size", 250))->generate();

        return $image->stream('png', 100);
    }

    /**
     * @OA\Get(
     *     tags={"images, group"},
     *     path="/api/image/group",
     *     description="",
     *     @OA\Parameter(
     *     name="id",
     *     required=true,
     *     in="query",
     *     description="identifier of the group",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description=")
     * )
     */
    public function getGroupImage(Request $request)
    {
        $gruppe = Group::findOrFail($request->id);
        $imgName = $request->name;

        if ($gruppe->image == null) {
            $avatar = new InitialAvatar();
            $image = $avatar->name($gruppe->name)->size($request->input("size", 400))->rounded(false)->generate();
            $name = str_random(32);
            Storage::disk('public')->put('/images/groups/' . $name . ".png", $image->stream('png', 90));
            $imgName = $name;
            $gruppe->image = $name;
            $gruppe->save();
        }

        if (!$imgName || $gruppe->image != $imgName || !Storage::disk('public')->has('/images/groups/' . $imgName . ".png")) {
            return response("image not found", 404);
        }

        if ($request->has("size")) {
            try {
            $size = $request->input("size") <= 400 ? $request->input("size") : 400; //cap auf 400
            $image = (new ImageManager);
            $image = $image->make(Storage::disk('public')->path('/images/groups/' . $imgName . ".png"));
            $image = $image->fit($size);
            return $image->response()->setCache(["max_age" => 3600]);
            } catch (\Exception $exception) {
                return redirect("/storage/images/groups/" . $gruppe->image . ".png")->setCache(["max_age" => 3600]);
            }
        } else {
            return redirect("/storage/images/groups/" . $gruppe->image . ".png")->setCache(["max_age" => 3600]);
        }
    }

    private function getGroupImageString($gruppe)
    {
        if ($gruppe->image == null) {
            $avatar = new InitialAvatar();
            $image = $avatar->name($gruppe->name)->size(400)->rounded(false)->generate();
            $name = str_random(32);
            Storage::disk('public')->put('/images/groups/' . $name . ".png", $image->stream('png', 90));
            $imgName = $name;
            $gruppe->image = $name;
            $gruppe->save();
        }
        return redirect("/storage/images/groups/" . $gruppe->image . ".png")->setCache(["max_age" => 3600]);
    }

    public function getSectionImage(Request $request) {
        $section = Section::findOrFail($request->id);
        $imgName = $request->name;

        if ($section->image == null) {
            return $this->getGroupImageString($section->group);
        }

        if (!$imgName || $section->image != $imgName || !Storage::disk('public')->has('/images/sections/' . $imgName . ".png")) {
            return response("image not found", 404);
        }

        if ($request->has("size")) {
            try {
            $size = $request->input("size") <= 400 ? $request->input("size") : 400; //cap auf 400
            $image = (new ImageManager);
            $image = $image->make(Storage::disk('public')->path('/images/sections/' . $imgName . ".png"));
            $image = $image->fit($size);
            return $image->response()->setCache(["max_age" => 3600]);
            } catch (\Exception $exception) {
                return redirect("/storage/images/sections/" . $section->image . ".png")->setCache(["max_age" => 3600]);
            }
        } else {
            return redirect("/storage/images/sections/" . $section->image . ".png")->setCache(["max_age" => 3600]);
        }
    }

    public function getInteractiveCourseImage(Request $request)
    {
        $course = InteractiveCourse::findOrFail($request->id);
        $imgName = $request->name;


        if ($course->image == null) {
            $avatar = new InitialAvatar();
            $size = $request->input("size", 400) <= 400 ? $request->input("size", 400) : 400; //cap auf 400
            $courseName = "";
            if(json_decode($course->name) != null && property_exists(json_decode($course->name),"de"))
            {
                $courseName = json_decode($course->name)->de;
            }
            $image = $avatar->name($courseName)->size($size)->rounded(false)->generate();
            $name = str_random(32);
            Storage::disk('public')->put('/images/interactive_courses/' . $name . ".png", $image->stream('png', 90));
            $imgName = $name;
            $course->image = $name;
            $course->save();
        }

        if (!$imgName || $course->image != $imgName || !Storage::disk('public')->has('/images/interactive_courses/' . $imgName . ".png")) {
            return response("image not found", 404);
        }

        if ($request->has("size")) {
            try {
                $size = $request->input("size") <= 400 ? $request->input("size") : 400; //cap auf 400
                $image = (new ImageManager);
                $image = $image->make(Storage::disk('public')->path('/images/interactive_courses/' . $imgName . ".png"));
                $image = $image->fit($size);
                return $image->response()->setCache(["max_age" => 3600]);
            } catch (\Exception $exception) {
                return redirect("/storage/images/interactive_courses/" . $course->image . ".png")->setCache(["max_age" => 3600]);
            }
        } else {
            return redirect("/storage/images/interactive_courses/" . $course->image . ".png")->setCache(["max_age" => 3600]);
        }
    }

    public function getLearnContentCategoryImage(Request $request)
    {
        $category = LearnContentCategory::findOrFail($request->id);
        $imgName = $request->name;

        if (!$imgName || $category->image != $imgName || !Storage::disk('public')->has('/images/categories/' . $imgName . ".png")) {
            return response("image not found", 404);
        }

        if ($request->has("size")) {
            try {
                $size = $request->input("size") <= 400 ? $request->input("size") : 400; //cap auf 400
                $image = (new ImageManager);
                $image = $image->make(Storage::disk('public')->path('/images/categories/' . $imgName . ".png"));
                $image = $image->fit($size);
                return $image->response()->setCache(["max_age" => 3600]);
            } catch (\Exception $exception) {
                return redirect("/storage/images/categories/" . $category->image . ".png")->setCache(["max_age" => 3600]);
            }
        } else {
            return redirect("/storage/images/categories/" . $category->image . ".png")->setCache(["max_age" => 3600]);
        }
    }

    public function getInteractiveCourseBadgeImage(Request $request)
    {
        $badge = InteractiveCourseBadge::findOrFail($request->id);
        $imgName = $request->name;

        if (!$imgName || $badge->image != $imgName || !Storage::disk('public')->has('/images/interactive_course_badges/' . $imgName . ".png"))
            return response("image not found", 404);


        if ($request->has("size")) {
            try {
                $size = $request->input("size") <= 400 ? $request->input("size") : 400; //cap auf 400
                $image = (new ImageManager);
                $image = $image->make(Storage::disk('public')->path('/images/interactive_course_badges/' . $imgName . ".png"));
                $image = $image->fit($size);
                return $image->response()->setCache(["max_age" => 3600]);
            } catch (\Exception $exception) {
                return redirect("/storage/images/interactive_course_badges/" . $badge->image . ".png")->setCache(["max_age" => 3600]);
            }
        } else {
            return redirect("/storage/images/interactive_course_badges/" . $badge->image . ".png")->setCache(["max_age" => 3600]);
        }
    }

    public function getLearnContentImage(Request $request)
    {
        $lernContent = LearnContent::findOrFail($request->id);
        $metadata = json_decode($lernContent->metadata);
        if($metadata != null && property_exists($metadata,"preview_image") && $metadata->preview_image != null)
        {
            return redirect("/storage/images/learnContentThumbnails/" . $metadata->preview_image)->setCache(["max_age" => 3600]);
        }
        return "no image";
    }
}
