<?php


namespace App\Http\Controllers\API\V1\Groups;


use App\Beitrag;
use App\BeitragMedia;
use App\BeitragTemplate;
use App\BeitragTemplateMedia;
use App\Group;
use App\Http\Controllers\API\ApiController;
use App\Jobs\CompressImageJob;
use App\PermissionConstants;
use App\Section;
use App\SectionGroupApp;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AnnouncementTemplatesController extends ApiController
{
    /**
     * @OA\Get(
     *     tags={"v1","announcementtemplates"},
     *     path="/api/v1/groups/section/{sectionId}/announcementtemplates",
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
     *     @OA\Response(response="200", description="get a list of all available announcement templates for a given section and user")
     * )
     */
    public function announcementtemplates($sectionId, Request $request)
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

        $templates = BeitragTemplate::where("cloudid", "=", $cloud_user->id)->where("app_id", "=", $groupApp->id)->get();

        return $this->createJsonResponse("ok", false, 200, [ "announcementTemplates" => $templates]);
    }

    /**
     * @OA\Get(
     *     tags={"v1","announcementtemplates"},
     *     path="/api/v1/announcementtemplates/templates",
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
     *     @OA\Response(response="200", description="get a list of all available announcement templates for a given user")
     * )
     */
    public function getAllAnnouncementTemplates(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);

        $templates = [];
        foreach(BeitragTemplate::where("cloudid", "=", $cloud_user->id)->orderBy('created_at','DESC')->get() as $template)
        {
            $template->fromhere = "nein";
            $template->load("media");
            $templates[] = $template;
        }

        return $this->createJsonResponse("ok", false, 200, [ "templates" => $templates]);
    }

    /**
     * @OA\Post (
     *     tags={"v1","announcementtemplates"},
     *     path="/api/v1/groups/section/{sectionId}/announcementtemplates",
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
     *       name="title",
     *       required=true,
     *       in="query",
     *       description="announcement template title",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="content",
     *       required=true,
     *       in="query",
     *       description="announcement template content",
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
     *     @OA\Response(response="200", description="create an announcement template")
     * )
     */
    public function createAnnouncementTemplate($sectionId, Request $request)
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

        $beitrag = new BeitragTemplate();
        $beitrag->cloudid = $cloud_user->id;
        $beitrag->app_id = $groupApp->id;
        $beitrag->content = $request->text;
        $beitrag->title = $request->title;

        $beitrag->save();

        if($request->hasfile('media'))
        {
            foreach($request->file('media') as $file)
            {
                $this->createNewAnnouncementTemplateMedia($file, $beitrag->id);
            }
        }
        $beitrag->load("media");
        return $this->createJsonResponse("ok", false, 200, [ "announcementTemplate" => $beitrag] );
    }


    private function createNewAnnouncementTemplateMedia($file, $announcementId) {
        $path = $file->storePublicly("announcement_template_media", [ "disk" => "public" ]);

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

        $b_media= new BeitragTemplateMedia();
        $b_media->beitrag_template_id = $announcementId;
        $b_media->disk_name = $path;
        $b_media->content_type = $type;
        $b_media->metadata = json_encode($metadata);
        $b_media->save();
        if ($type == "image")CompressImageJob::dispatch(Storage::disk("public")->getAdapter()->applyPathPrefix($b_media->disk_name));

    }

    public function updateAnnouncementTemplate($announcementTemplateId, Request $request)
    {
        $newFiles = $request->file("new_files");
        $fileIdsToDelete = $request->has("file_ids_to_delete")? json_decode($request->input("file_ids_to_delete")) : null;
        $newTitle = $request->input("title");
        $newContent = $request->input("content");

        $user = parent::getUserForToken($request);
        $announcement = BeitragTemplate::findOrFail($announcementTemplateId);
        if(!$announcement)
            return $this->createJsonResponse("Announcement template not found",true, 400);

        if($announcement->cloudid !== $user->id)
            return $this->createJsonResponse("No permission. You are not the creator.",true, 400);

        if($newTitle)
        {
            $announcement->title = $newTitle;
        }

        if($newContent)
        {
            $announcement->content = $newContent;
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
                $this->createNewAnnouncementTemplateMedia($file, $announcement->id);
            }
        }

        $announcement->save();
        $announcement->load("media");

        return $this->createJsonResponse("Announcement template updated", false, 200,["announcementTemplate" => $announcement]);
    }

    public function deleteAnnouncementTemplate($announcementTemplateId, Request $request)
    {
        $user = parent::getUserForToken($request);
        $announcement = BeitragTemplate::findOrFail($announcementTemplateId);

        if(!$announcement)
            return $this->createJsonResponse("Announcement not found",true, 400);

        if($announcement->cloudid !== $user->id)
            return $this->createJsonResponse("No permission. You are not the creator.",true, 400);

        $announcement->delete();

        return $this->createJsonResponse("Announcement template deleted",false, 200, ["id" => $announcementTemplateId]);
    }
}
