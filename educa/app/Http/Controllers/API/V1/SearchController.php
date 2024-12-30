<?php

namespace App\Http\Controllers\API\V1;

use App\Appointment;
use App\Beitrag;
use App\CloudID;
use App\Dokument;
use App\Group;
use App\Http\Controllers\API\ApiController;
use App\Models\EducaWikiPage;
use App\Section;
use App\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class SearchController extends ApiController
{
    /**
     * @OA\Post (
     *     tags={"v1","search"},
     *     path="/api/v1/search",
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
     *       name="q",
     *       required=true,
     *       in="query",
     *       description="search string",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="")
     * )
     */
    public function search(Request $request)
    {
        $user = parent::getUserForToken($request);
        if($user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        if(!$request->has("q") || strlen($request->input("q")) < 3)
            return parent::createJsonResponse("Enter search with a least 3 characters.", true, 400);

        $q = $request->input("q");

        $categories = null;
        if( $request->has("categories"))
            $categories = $request->input("categories");

            $cloudUser =  null;
            $groups =  !$categories || in_array(SearchCategories::GROUPS, $categories) ? $this->searchGroups($q,$user) : null;
            $annoucements =  !$categories || in_array(SearchCategories::ANNOUNCEMENTS, $categories)?  $this->searchBeitrag($q,$user) : null;
            $tasks =  !$categories || in_array(SearchCategories::TASKS, $categories)? $this->searchTask($q,$user) : null;
            $events =  !$categories ||  in_array(SearchCategories::EVENTS, $categories)? $this->searchTermine($q,$user) : null;
            $documents =  !$categories ||  in_array(SearchCategories::DOCUMENTS,$categories )?  $this->searchDocuments($q, $user) : null;
            $wikiPages = !$categories ||  in_array(SearchCategories::WIKI_PAGES,$categories )?  $this->searchWikiPages($q) : null;
            $resp = [];

        if($groups) $resp[SearchCategories::GROUPS] = $groups;
        if($annoucements) $resp[SearchCategories::ANNOUNCEMENTS] = $annoucements;
        if($tasks) $resp[SearchCategories::TASKS] = $tasks;
        if($events) $resp[SearchCategories::EVENTS] = $events;
        if($documents) $resp[SearchCategories::DOCUMENTS] = $documents;
        if($wikiPages) $resp[SearchCategories::WIKI_PAGES] = $wikiPages;
        if($cloudUser) $resp[SearchCategories::USERS] = $cloudUser;

        return $this->createJsonResponse( "ok", false, 200, ["search" => $resp]);
    }

    private function searchGroups($term, $user)
    {
        return Group::where('name','LIKE','%'.$term.'%')->join('cloudid_group','cloudid_group.group_id','groups.id')->where('cloudid_group.cloudid', '=',$user->id)->get();
    }

    private function searchBeitrag($term, $user)
    {
        return Beitrag::whereIn("id",DB::table("beitrags")->where('content','LIKE','%'.$term.'%')
            ->join('section_group_apps','section_group_apps.id','beitrags.app_id')
            ->join('sections','sections.id','section_group_apps.section_id')
            ->join('cloudid_group','cloudid_group.group_id','sections.group_id')
            ->where('cloudid_group.cloudid', '=',$user->id)->pluck("beitrags.id"))
            ->orderBy("created_at","DESC")
            ->take(30)
            ->get();
    }

    private function searchDocuments($term, $user)
    {
        $relevant_group_ids = DB::table("cloudid_group")->where("cloudid","=",$user->id)->pluck("group_id");
        $relevant_documents_ids = DB::table("model_dokument")
            ->where("model_type","=","section")
            ->whereIntegerInRaw("model_id",DB::table("sections")->whereIntegerInRaw("group_id",$relevant_group_ids)->pluck("id"))
            ->pluck("dokument_id");
        $documents = Dokument::where("name","LIKE",'%'.$term.'%')->whereIntegerInRaw("id",$relevant_documents_ids)
            ->where("type","=","file")->take(30)->get();
        $d2 = collect();
        if(count($documents) < 30 && config("educa.search.fullSearch"))
        {
            $d2 =  Dokument::whereRaw(
                "MATCH(metadata) AGAINST(?)",
                array($term)
            )->whereIntegerInRaw("id",$relevant_documents_ids)->where("type","=","file")->take(30)->get();
            foreach ($d2 as $d)
            {
                $d->fullText = true;
            }
        }
        return collect()->merge($documents)->merge($d2);
    }

    private function searchTermine($term, $user)
    {
        $ids = DB::table('appointment_cloud_i_d')->where([
            'cloudid' => $user->id,
        ])->pluck('appointment_id');
        $sectionIds = Section::whereIn('group_id',  $user->gruppen()->pluck("id"))->pluck('id');
        $ids2 = DB::table('appointment_section')
            ->whereIn('section_id', $sectionIds)
            ->pluck('appointment_id');
        return Appointment::where('title','LIKE','%'.$term.'%')
            ->where(function ($query) use ($ids, $ids2) {
                $query->whereIn('id', $ids)->orWhereIn('id', $ids2);
            })
            ->orderBy("startDate","DESC")
            ->take(30)
            ->get();
    }

    private function searchTask($term, $user)
    {
        $ids = DB::table('task_cloud_i_d')->where([
            'cloud_id' => $user->id,
        ])->pluck('task_id');
        $sectionIds = Section::whereIn('group_id',  $user->gruppen()->pluck("id"))->pluck('id');
        $ids2 = DB::table('task_section')
            ->whereIn('section_id', $sectionIds)
            ->pluck('task_id');

        $id3 = Task::where('cloud_id', '=', $user->id)->pluck('id');
        return Task::where('title','LIKE','%'.$term.'%')
            ->where(function ($query) use ($ids, $ids2, $id3) {
                $query->whereIn('id', $ids)->orWhereIn('id', $ids2)->orWhereIn('id', $id3);
            })
            ->orderBy("created_at","DESC")
            ->take(30)
            ->get();
    }

    private function searchWikiPages($text){

        $pages = EducaWikiPage::where(["model_type" => "global", "model_id" => null])->get();
        $relevantPages = [];
        foreach ($pages as $page)
        {
            $data = json_decode($page->content);
            if($data == null || !$text)
                continue;

            $found = false;
            $foundCount = 0;
            foreach ($data->blocks as $block)
            {
                $blockString = json_encode($block,JSON_UNESCAPED_UNICODE);
                if(str_contains(strtolower($blockString),strtolower($text))) {
                    $resultBlocks[] = $block;
                    $found = true;
                    $foundCount = 3;
                } else if($foundCount > 0)
                {
                    $resultBlocks[] = $block;
                    $foundCount--;
                }
            }

            if($found)
                $relevantPages[] = $page;
        }

        return $relevantPages;

    }
}
