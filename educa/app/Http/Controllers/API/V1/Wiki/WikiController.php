<?php

namespace App\Http\Controllers\API\V1\Wiki;

use App\CloudID;
use App\Http\Controllers\API\ApiController;
use App\Models\EducaWikiPage;
use App\PermissionConstants;
use App\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WikiController extends ApiController
{
    private function getWikiModel(string $modelType, $modelId)
    {
        if($modelType == "section")
            return Section::find($modelId);
        if($modelType == "global")
            return true;
        return false;
    }

    private function hasPermission(CloudID $cloudUser, string $modelType, $modelId, string $permission)
    {
        if($modelType == "global")
           return $cloudUser->hasPermissionTo($permission);

        if($modelType == "section")
        {
            $section = Section::find($modelId);
            if(!$section)
                return false;
            return $section->isAllowed($cloudUser, $permission); //implement interface?
        }
        return false;
    }

    public function listWiki(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);

        $modelType = $request->input("modelType");
        $modelId = $request->input("modelId");
        $model = $this->getWikiModel($modelType, $modelId);

        if(!$model)
            return $this->createJsonResponse("model invalid", true,400);

        if(!$this->hasPermission($cloud_user,$modelType, $modelId,PermissionConstants::EDUCA_WIKI_OPEN))
            return $this->createJsonResponse("no permission", true,400);

        $pages = EducaWikiPage::where(["model_type" => $modelType, "model_id" => $modelId??null])->get();
        return $this->createJsonResponse("ok", false,200, [ "pages" => $pages]);
    }

    public function createWiki(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);

        $modelType = $request->input("modelType");
        $modelId = $request->input("modelId");
        $model = $this->getWikiModel($modelType, $modelId);

        if(!$model)
            return $this->createJsonResponse("model invalid", true,400);

        if(!$this->hasPermission($cloud_user,$modelType, $modelId,PermissionConstants::EDUCA_WIKI_EDIT))
            return $this->createJsonResponse("no permission", true,400);

        $page = new EducaWikiPage();
        $page->model_type = $modelType;
        $page->model_id = $modelId;
        $page->content = json_encode(json_decode('{"time":1635603431943,"blocks":[{"id":"sheNwCUP5A","type":"header","data":{"text":"educa Wiki-Seiten...","level":2}},{"id":"12iM3lqzcm","type":"paragraph","data":{"text":"Hey, mit den educa Wiki-Seiten können Inhalte zusammengefasst und als Lerninhalte aufbereitet werden. Dabei passen sich die Inhalte optimal an die Bildschirme an."}},{"id":"fvZGuFXHmK","type":"header","data":{"text":"Highlights","level":3}},{"id":"xnPuiC9Z8M","type":"list","data":{"style":"unordered","items":["It is a block-styled editor","It returns clean data output in JSON","Designed to be extendable and pluggable with a simple API"]}},{"id":"fvZGuFreXHmK","type":"delimiter"},{"id":"FF1iyF3VwN","type":"image","data":{"file":{"url":"/images/editorjs/sample1.jpg"},"caption":"","withBorder":false,"stretched":false,"withBackground":false}}]}'));
        $page->name = $request->input("name");
        $page->save();

        $pages = EducaWikiPage::where(["model_type" => $modelType, "model_id" => $modelId??null])->get();
        return $this->createJsonResponse("ok", false,200, [ "pages" => $pages, "newPage" => $page]);
    }

    public function deleteWiki(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        $modelType = $request->input("modelType");
        $modelId = $request->input("modelId");
        $model = $this->getWikiModel($modelType, $modelId);

        if(!$model)
            return $this->createJsonResponse("model invalid", true,400);

        if(!$this->hasPermission($cloud_user,$modelType, $modelId,PermissionConstants::EDUCA_WIKI_EDIT))
            return $this->createJsonResponse("no permission", true,400);

        $page = EducaWikiPage::findOrFail($request->input("page_id"));
        // delete page and children
        function deleteRecursive(EducaWikiPage $page){
            $children = EducaWikiPage::where(["parentId" => $page->id])->get();
            $page->delete();
                foreach ($children as $child)
                    deleteRecursive($child);
        }

        DB::transaction( function () use($page){
            deleteRecursive($page);
        });


        $pages = EducaWikiPage::where(["model_type" => $modelType, "model_id" => $modelId??null])->get();
        return $this->createJsonResponse("ok", false,200, [ "pages" => $pages]);
    }

    public function updateWiki(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);

        $modelType = $request->input("modelType");
        $modelId = $request->input("modelId");
        $model = $this->getWikiModel($modelType, $modelId);

        if(!$model)
            return $this->createJsonResponse("model invalid", true,400);

        if(!$this->hasPermission($cloud_user,$modelType, $modelId,PermissionConstants::EDUCA_WIKI_EDIT))
            return $this->createJsonResponse("no permission", true,400);


        $pageRequest = $request->input("page");

        $page = EducaWikiPage::findOrFail($request->input("page_id"));
        $page->content = $pageRequest["content"];
        $page->name = $pageRequest["name"];
        $page->parentId = $pageRequest["parentId"];
        $page->save();

        $pages = EducaWikiPage::where(["model_type" => $modelType, "model_id" => $modelId??null])->get();
        return $this->createJsonResponse("ok", false,200, [ "pages" => $pages, "newPage" => $page]);
    }

    public function uploadImage(Request $request)
    {
        $name = str_random(64);
        $file = $request->file('image');
        $path = $file->storeAs("/images/wiki",$name.".".$file->getClientOriginalExtension(),"public");

        return $this->createJsonResponse("ok", false,200, [ "image" => "/storage/".$path]);
    }

    public function search(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        $q = $request->input("q");

        $modelType = $request->input("modelType");
        $modelId = $request->input("modelId");
        $model = $this->getWikiModel($modelType, $modelId);

        if(!$model)
            return $this->createJsonResponse("model invalid", true,400);

        if(!$this->hasPermission($cloud_user,$modelType, $modelId,PermissionConstants::EDUCA_WIKI_OPEN))
            return $this->createJsonResponse("no permission", true,400);

        $resultBlocks = [];
        $pages = EducaWikiPage::where(["model_type" => $modelType, "model_id" => $modelId??null])->get();
        foreach ($pages as $page)
        {
            $data = json_decode($page->content);
            if($data == null)
                continue;

            $found = false;
            $foundCount = 0;
            foreach ($data->blocks as $block)
            {
                $blockString = json_encode($block,JSON_UNESCAPED_UNICODE);
                if(str_contains(strtolower($blockString),strtolower($q))) {
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
            {
                $resultBlocks[] = json_decode('{
    "type" : "delimiter",
    "data" : {}
}');
            }
        }

        return $this->createJsonResponse("ok", false,200, [ "searchResult" => $resultBlocks]);
    }
}
