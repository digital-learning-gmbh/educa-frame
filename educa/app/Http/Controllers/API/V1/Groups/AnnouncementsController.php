<?php


namespace App\Http\Controllers\API\V1\Groups;


use App\Beitrag;
use App\BeitragComment;
use App\BeitragMedia;
use App\BeitragTemplate;
use App\CloudID;
use App\Group;
use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\V1\Push\PushNotificationsController;
use App\Http\Controllers\API\V1\xAPI\XAPIBaseController;
use App\Http\Controllers\API\V1\xAPI\XAPIVerbs;
use App\Jobs\CompressImageJob;
use App\Jobs\PushNotificationBackgroundJob;
use App\Jobs\PushNotificationSingleJob;
use App\Models\BeitragMention;
use App\Jobs\PushNotificationSectionJob;
use App\PermissionConstants;
use App\Section;
use App\SectionGroupApp;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use stdClass;
use function Amp\Iterator\concat;

class AnnouncementsController extends ApiController
{
    /**
     * @OA\Get(
     *     tags={"v1","announcements"},
     *     path="/api/v1/groups/section/{sectionId}/announcements",
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
     *       name="sectionId",
     *       required=true,
     *       in="path",
     *       description="id of the section",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="")
     * )
     */
    public function announcements($sectionId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);

        //  $group = Group::findOrFail($groupId);
        $section = Section::findOrFail($sectionId);

        if(!$section->isAllowed($cloud_user,PermissionConstants::EDUCA_SECTION_ANNOUNCEMENT_OPEN))
        {
            return $this->createJsonResponse("No permission.", true, 400);
        }

        $groupApp = $section->getGroupApp('announcement');
        if($groupApp == null)
        {
            return response()->json(["status" => -1, "error" => "Wrong tab ID supplied"]);
        }

        $announcements = [];

        foreach(Beitrag::where('app_id','=',$groupApp->id)->where(function ($query) {
            $query->whereNull('planned_for')->orWhereRAW('planned_for <= NOW()');
        })->orderBy('created_at','DESC')->get() as $beitrag)
        {
            if( DB::table("blocked_users")->where("cloudid","=",$beitrag->cloudid)->where("by_cloudid","=",$cloud_user->id)->exists())
                continue;
            //$beitrag->load("comments");
            $beitrag->load("likes");
            $beitrag->load("media");
            $beitrag->load("mentions");
            if($beitrag->planned_for != null)
            {
                $beitrag->created_at = $beitrag->planned_for;
            }
            $beitrag->commentsFiltered();

            array_push($announcements , $beitrag);
        }

        $announcements_planned = [];

        if($section->isAllowed($cloud_user,PermissionConstants::EDUCA_SECTION_ANNOUNCEMENT_CREATE)) {
            foreach (Beitrag::where('app_id', '=', $groupApp->id)->where(function ($query) {
                $query->whereNotNull('planned_for')->whereRAW('planned_for > NOW()');
            })->orderBy('created_at', 'DESC')->get() as $beitrag) {
                if (DB::table("blocked_users")->where("cloudid", "=", $beitrag->cloudid)->where("by_cloudid", "=", $cloud_user->id)->exists())
                    continue;
                // $beitrag->load("comments");
                $beitrag->load("likes");
                $beitrag->load("media");

                $beitrag->commentsFiltered();
                array_push($announcements_planned, $beitrag);
            }
        }

        $announcement_templates = [];
        foreach(BeitragTemplate::where("cloudid", "=", $cloud_user->id)->orderBy('created_at','DESC')->get() as $beitrag)
        {
            $beitrag->fromhere = "nein";
            if($beitrag->app_id == $groupApp->id)
            {
                $beitrag->fromhere = "ja";
            }
            $beitrag->load("media");
            array_push($announcement_templates , $beitrag);
        }

        // TODO
        //$beitragReiter->markAllRead($cloud_user->id);

        return $this->createJsonResponse("ok", false, 200, [ "announcements" => $announcements, "planned" => $announcements_planned, "templates" => $announcement_templates]);
    }

    public function getSectionAnnouncementsPreview($sectionId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if ($cloud_user == null)
            return parent::createJsonResponse("no user or no permission", true, 400);

        $section = Section::findOrFail($sectionId);

        if(!$section->isAllowed($cloud_user,PermissionConstants::EDUCA_SECTION_ANNOUNCEMENT_OPEN))
        {
            return $this->createJsonResponse("No permission.", true, 400);
        }

        $groupApp = $section->getGroupApp('announcement');
        if($groupApp == null)
        {
            return $this->createJsonResponse("Wrong tab or ID supplied.", true, 400);
        }

        $announcements = [];

        foreach(Beitrag::where('app_id','=',$groupApp->id)->where(function ($query) {
            $query->whereNull('planned_for')->orWhereRAW('planned_for <= NOW()');
        })->orderBy('created_at','DESC')->limit(4)->get() as $beitrag)
        {
            if( DB::table("blocked_users")->where("cloudid","=",$beitrag->cloudid)->where("by_cloudid","=",$cloud_user->id)->exists())
                continue;

            $beitrag->load("likes");
            $beitrag->liked = false;
            foreach ($beitrag->likes as $like) {
                if ($cloud_user->id == $like->cloudid)
                    $beitrag->liked = true;
            }
            $beitrag->likeCount = count($beitrag->likes);

            $beitrag->load("media");
            if ($beitrag->media != null)
                $beitrag->attachmentCount = count($beitrag->media);

            $beitrag->load("mentions");
            $beitrag->mentionsMe = false;
            foreach ($beitrag->mentions as $mention) {
                if ($cloud_user->id == $mention->cloud_id)
                    $beitrag->mentionsMe = true;
            }

            if($beitrag->planned_for != null)
            {
                $beitrag->created_at = $beitrag->planned_for;
            }
            $beitrag->commentCount = count($beitrag->comments()->get());
            $beitrag->author = CloudID::where("id", $beitrag->cloudid)->first(["id", "name", "image"])->makeHidden(["has2FaKey"]);

            $beitrag->unsetRelation("comments");
            $beitrag->unsetRelation("likes");
            $beitrag->unsetRelation("media");
            $beitrag->unsetRelation("mentions");

            $announcements[] = $beitrag;
        }
        return $this->createJsonResponse("first announcements of section", false, 200, [ "announcements" => $announcements ]);
    }

    /**
     * @OA\Post (
     *     tags={"v1","announcements"},
     *     path="/api/v1/groups/section/{sectionId}/announcements",
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
     *       name="content",
     *       required=true,
     *       in="query",
     *       description="announcement content",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="file",
     *       required=false,
     *       in="query",
     *       description="optional media",
     *         @OA\Schema(
     *           type="file"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="sectionId",
     *       required=true,
     *       in="path",
     *       description="id of the section",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="")
     * )
     */
    public function createAnnouncement($sectionId, Request $request)
    {
        if($request->text == null || $request->text == "")
            return parent::createJsonResponse("Text is empty.", true,400);


        $cloud_user = parent::getUserForToken($request);
        $section = Section::findOrFail($sectionId);;

        if(!parent::isSectionInGroupOfCloudUser($cloud_user, $sectionId))
        {
            return parent::createJsonResponse("No Permission", true,400);
        }
        if(!$section->isAllowed($cloud_user,PermissionConstants::EDUCA_SECTION_ANNOUNCEMENT_CREATE))
        {
            return $this->createJsonResponse("No permission.", true, 400);
        }

        $groupApp = $section->getGroupApp('announcement');
        if($groupApp == null)
        {
            return parent::createJsonResponse("Group app is undefined", true,400);
        }

        $beitrag = new Beitrag();
        $beitrag->cloudid = $cloud_user->id;
        $beitrag->app_id = $groupApp->id;
        $comments_active = $request->input("comments_active") == "true";
        $beitrag->comments_active = $comments_active;
        $beitrag->comments_hide = !$comments_active;
        $beitrag->planned_for = $request->input("planned_for") == 'null' || $request->input("planned_for") == null ||  $request->input("planned_for") == 'undefined' ? null :  Carbon::createFromTimestamp($request->input("planned_for"))->toDateTime();

        $beitrag->save();

        if (!!strpos($request->text, 'data-mention')) {
            preg_match_all('/data-mention="@(.*?)"/', $request->text, $matches);
            $matches = $matches[1];


            $cloudUsersMentions = CloudID::whereIn("cloud_i_d_s.email", $matches)->select("id", "email")->get();

            $textCleaned = $request->text;
            $m_idx = 0;
            foreach ($cloudUsersMentions as $user) {
                $mention_idx = ($beitrag->id)."_".($m_idx);
                $textToReplace = "<span class=\"mention\" data-mention=\"@".($user->email)."\">@".($user->email)."</span>";
                $replacement = "<span id=".($mention_idx)." style=\"height: fit-content\">"."@".($user->email)."</span>";
                $textCleaned = str_replace($textToReplace, $replacement, $textCleaned);

                $beitragMentions = new BeitragMention();
                $beitragMentions->beitrag_id = $beitrag->id;
                $beitragMentions->cloud_id = $user->id;
                $beitragMentions->mention_idx = $mention_idx;
                $beitragMentions->save();
                $m_idx++;
            }

            $beitrag->content = $textCleaned;
        }
        else {
            $beitrag->content = $request->text;
        }
        $beitrag->save();

        if($request->hasfile('media'))
        {
            foreach($request->file('media') as $file)
            {
                $this->createNewAnnouncementMedia($file, $beitrag->id);
            }
        }


        $shouldPush = $request->input("shouldPush") == "true";
        $beitrag->notify = $shouldPush;
        $beitrag->notify_sent = false;
        if($shouldPush && ($beitrag->planned_for == null || Carbon::now()->subSecond()->greaterThanOrEqualTo($beitrag->planned_for))) {
            PushNotificationSectionJob::dispatch($section,"Neue Ankündigung von ".$beitrag->author->name,$beitrag->content, PermissionConstants::EDUCA_SECTION_ANNOUNCEMENT_OPEN);
            $beitrag->notify_sent = true;
        }
        $beitrag->save();


        XAPIBaseController::createStatement($cloud_user,["grouping" => [ "section_id" => $beitrag->section()->id]],XAPIBaseController::objectFactory($beitrag),XAPIVerbs::$ADD);


        //$group->addActivityFromApi("hat eine Ankündigung erstellt",$beitrag_id, "beitrag", $cloud_user);
        $section = Section::where(["id" => $sectionId])->with("sectionGroupApps")->first();
        $beitrag = Beitrag::where(["id" => $beitrag->id])->with("comments")->with("likes")->with("media")->with("mentions")->first();
        $section->append('permissions');
        return $this->createJsonResponse("ok", false, 200, [ "announcement" => $beitrag, "section" => $section] );
    }


    /**
     * @OA\Post (
     *     tags={"v1", "announcements"},
     *     path="/api/v1/announcements/{beitragId}/like",
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
     *       name="beitragId",
     *       required=true,
     *       in="path",
     *       description="id of the announcement",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="")
     * )
     */
    public function like($beitragId, Request $request)
    {

        if (!$beitragId)
            return $this->createJsonResponse("No announcement Id given in request.",true);
        $user = parent::getUserForToken($request);
        /**
         * TODO Check ob der Nutzer das darf!
         */
        $beitrag = Beitrag::find($beitragId);
        if($beitrag == null)
        {
            return parent::createJsonResponse("Beitrag does not exists", true,400);
        }

        if($beitrag->section() == null || !$beitrag->section()->isAllowed($user, PermissionConstants::EDUCA_SECTION_ANNOUNCEMENT_LIKE))
        {
            return $this->createJsonResponse("No permission.", true, 400);
        }


        $prevlike = DB::table('beitrag_likes')->where('beitrag_id','=', $beitragId)->where("cloudid", "=", $user->id);
            if($prevlike->count() > 0)
            { //unliken
                XAPIBaseController::createStatement($user,"section=".$beitrag->section()->id,XAPIBaseController::objectFactory($beitrag),XAPIVerbs::$DISLIKE);
                DB::table("beitrag_likes")->delete($prevlike->pluck("id"));
            }
            else
            { //liken
                XAPIBaseController::createStatement($user,"section=".$beitrag->section()->id,XAPIBaseController::objectFactory($beitrag),XAPIVerbs::$LIKE);
                $data = Array(
                    "created_at" => new \DateTime(),
                    "cloudid" => $user->id,
                    "beitrag_id" => $beitragId);
                DB::table("beitrag_likes")->insert($data);
            }
            //nochmal ranholen den otto vong update her
            $beitrag = Beitrag::where(["id" => $beitragId])->with("comments")->with("likes")->with("media")->first();
            return $this->createJsonResponse("Like toggled", false, 200,["announcement" => $beitrag]);
    }

    /**
     * @OA\Post (
     *     tags={"v1", "announcements"},
     *     path="/api/v1/announcements/{beitragId}/comment",
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
     *       name="comment",
     *       required=true,
     *       in="query",
     *       description="comment content",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="beitragId",
     *       required=true,
     *       in="path",
     *       description="id of the announcement",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="")
     * )
     */
    public function createComment($beitragId, Request $request)
    {
        $comment = $request->comment;
        if(!$comment)
            return $this->createJsonResponse("No comment given, or empty.",true,401);
        $user = parent::getUserForToken($request);

        $beitrag = Beitrag::find($beitragId);
        if($beitrag == null)
        {
            return parent::createJsonResponse("Beitrag does not exists", true,400);
        }

        if($beitrag->section() == null || !$beitrag->section()->isAllowed($user, PermissionConstants::EDUCA_SECTION_ANNOUNCEMENT_COMMENT))
        {
            return $this->createJsonResponse("No permission.", true, 400);
        }


            $data = Array(
                "created_at" => new \DateTime(),
                "cloudid" => $user->id,
                "beitrag_id" => $beitragId,
                "content" => $comment);
            DB::table("beitrag_comments")->insert($data);
        //nochmal ranholen den otto vong update her
        $beitrag = Beitrag::where(["id" => $beitragId])->with("comments")->with("likes")->with("media")->first();
        XAPIBaseController::createStatement($user,"section=".$beitrag->section()->id,XAPIBaseController::objectFactory($beitrag),XAPIVerbs::$COMMENT);

        return $this->createJsonResponse("Comment created", false, 200,["announcement" => $beitrag]);
    }


    public function updateAnnouncement($announcementId, Request $request)
    {
        $newFiles = $request->file("new_files");
        $fileIdsToDelete = $request->has("file_ids_to_delete")? json_decode($request->input("file_ids_to_delete")) : null;
        $newContent = $request->input("content");

        $user = parent::getUserForToken($request);
        $announcement = Beitrag::findOrFail($announcementId);
        if(!$announcement)
            return $this->createJsonResponse("Announcement not found",true, 400);

        if($announcement->cloudid !== $user->id)
            return $this->createJsonResponse("No permission. You are not the creator.",true, 400);

       if($newContent)
        {
            $announcement->content = $newContent;
        }

        if($request->has("comments_hide"))
        {
            $announcement->comments_hide = ($request->input("comments_hide") == "true" || $request->input("comments_hide") == true) && $request->input("comments_hide") != "false";
        }
        if($request->has("comments_active"))
        {
            $announcement->comments_active = ($request->input("comments_active") == "true" || $request->input("comments_active") == true) && $request->input("comments_active") != "false";
            $announcement->comments_hide  =  $announcement->comments_active ? false : ($request->input("comments_hide") == "true" || $request->input("comments_hide") == true) && $request->input("comments_hide") != "false";
        }


       //Delete files
        if(is_array( $fileIdsToDelete ))
        {
            foreach ($fileIdsToDelete as $file_id )
            foreach ( $announcement->media()->get() as $medium )
                if($medium->id == $file_id)
                    $medium->delete();
        }
        //new files
        if($newFiles)
        {
            foreach($newFiles as $file)
            {
                    $this->createNewAnnouncementMedia($file, $announcement->id);
            }
        }

        $announcement->save();
        $announcement->load("media");
        $announcement->load("likes");

        XAPIBaseController::createStatement($user,"section=".$announcement->section()->id,XAPIBaseController::objectFactory($announcement),XAPIVerbs::$UPDATED);
        $announcement->commentsFiltered();
        return $this->createJsonResponse("Announcement Updated", false, 200,["announcement" => $announcement]);
    }

    private function  createNewAnnouncementMedia($file, $announcementId) {
        $path = $file->storePublicly("announcement_media", [ "disk" => "public" ]);

        $fileEnding = $file->getClientOriginalExtension();
        $fileOriginalName = $file->getClientOriginalName();
        $fileOriginalName = explode('.',$fileOriginalName)[0];
        $metadata = [];
        $metadata["originalName"] = $fileOriginalName;
        $imageTypes = array("png" => 0, "jpg" => 1, "jpeg" => 2, "gif" => 3, "heif" => 4, "heic" => 5, "webp" => 6 );
        $videoTypes = array("mp4" => 0, "mov" => 1, "avi" => 2, "wvm" => 3, "mkv" => 4, "webm" => 4);
        $documentTypes = array("txt" => 0, "pdf" => 1, "docx" => 2, "csv" => 3); // currently not used in default software
        $type = "document";
        if (isset($imageTypes[$fileEnding])) {
            $type = "image";
        }
        else if (isset($videoTypes[$fileEnding]))
            $type = "video";
        // maybe we restrict our types here
//                else if (isset($documentTypes[$fileEnding]))
//                    $type = "document";

        $b_media= new BeitragMedia();
        $b_media->beitrag_id = $announcementId;
        $b_media->disk_name = $path;
        $b_media->content_type = $type;
        $b_media->metadata = json_encode($metadata);
        $b_media->save();
        if ($type == "image")CompressImageJob::dispatch(storage_path("app/public/".$b_media->disk_name));

    }

    public function deleteAnnouncement($announcementId, Request $request)
    {
        $user = parent::getUserForToken($request);
        $announcement = Beitrag::findOrFail($announcementId);

        if(!$announcement)
            return $this->createJsonResponse("Announcement not found",true, 400);

        if($announcement->cloudid !== $user->id)
            return $this->createJsonResponse("No permission. You are not the creator.",true, 400);

        $announcement->delete();

        return $this->createJsonResponse("Announcement deleted",false, 200, ["id" => $announcementId]);
    }

    /**
     * @OA\Get(
     *     tags={"v1","announcements"},
     *     path="/api/v1/announcements/{announcementId}",
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
     *       name="announcementId",
     *       required=true,
     *       in="path",
     *       description="id of the announcement",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="")
     * )
     */
    public function getById($announcementId, Request $request)
    {

        $user = parent::getUserForToken($request);
        $announcement = Beitrag::findOrFail($announcementId);

        $grpApp = SectionGroupApp::findOrFail( $announcement->app_id );
        $section = $grpApp->section()->first();

        if(!parent::isSectionInGroupOfCloudUser($user, $section->id))
        {
            return parent::createJsonResponse("No Permission", true,400);
        }

        if(!$section->isAllowed($user,PermissionConstants::EDUCA_SECTION_ANNOUNCEMENT_OPEN))
        {
            return $this->createJsonResponse("No permission.", true, 400);
        }

        if(!$announcement)
            return $this->createJsonResponse("Announcement not found",true, 400);


        $announcement->load("media");
        $announcement->load("likes");
        $announcement->load("comments");
        return $this->createJsonResponse("ok", false, 200, ["announcement" => $announcement]);
    }


    public function hideComment($announcementId,$commentId, Request $request)
    {
        $user = parent::getUserForToken($request);

        $beitrag = Beitrag::find($announcementId);
        if($beitrag == null)
        {
            return parent::createJsonResponse("Beitrag does not exists", true,400);
        }

        $comment = BeitragComment::find($commentId);
        if($comment == null)
        {
            return parent::createJsonResponse("Comment does not exists", true,400);
        }

        if($beitrag->section() == null || !$beitrag->section()->isAllowed($user, PermissionConstants::EDUCA_SECTION_ANNOUNCEMENT_COMMENT) || ($user->id != $comment->cloudid && $user->id != $beitrag->cloudid))
        {
            return $this->createJsonResponse("No permission.", true, 400);
        }
        $comment->hidden = !$comment->hidden;
        $comment->save();

        return $this->createJsonResponse("comment Updated", false, 200,["comment" => $comment]);
    }

    public function editComment($announcementId,$commentId, Request $request)
    {
        $commentText = $request->comment;
        if(!$commentText)
            return $this->createJsonResponse("No comment given, or empty.",true,401);
        $user = parent::getUserForToken($request);

        $beitrag = Beitrag::find($announcementId);
        if($beitrag == null)
        {
            return parent::createJsonResponse("Beitrag does not exists", true,400);
        }

        $comment = BeitragComment::find($commentId);
        if($comment == null)
        {
            return parent::createJsonResponse("Comment does not exists", true,400);
        }

        if($beitrag->section() == null || !$beitrag->section()->isAllowed($user, PermissionConstants::EDUCA_SECTION_ANNOUNCEMENT_COMMENT) || ($user->id != $comment->cloudid && $user->id != $beitrag->cloudid))
        {
            return $this->createJsonResponse("No permission.", true, 400);
        }
        $comment->content = $commentText;
        $comment->edited = true;
        $comment->save();

        XAPIBaseController::createStatement($user,"section=".$beitrag->section()->id,XAPIBaseController::objectFactory($comment),XAPIVerbs::$UPDATED);

        return $this->createJsonResponse("comment Updated", false, 200,["comment" => $comment]);
    }



    public function getHistoryBeitrag($beitragId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }
        $announcement = Beitrag::findOrFail($beitragId);
        if(!$announcement)
            return $this->createJsonResponse("Announcement not found",true, 400);


        $announcement->load("media");
        $announcement->load("likes");
        $announcement->load("comments");
        return $this->createJsonResponse("beitrag history", false, 200,["announcement" => $announcement, "history" => $announcement->activities]);
    }

    public function getHistoryComment($beitragId, $commentId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }

        $comment = BeitragComment::find($commentId);
        if($comment == null)
        {
            return parent::createJsonResponse("Comment does not exists", true,400);
        }
        $comment->activities->load("causer");
        return $this->createJsonResponse("comment history", false, 200,["comment" => $comment, "history" => $comment->activities]);
    }
}
