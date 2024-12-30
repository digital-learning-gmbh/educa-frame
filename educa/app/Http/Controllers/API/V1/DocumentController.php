<?php

namespace App\Http\Controllers\API\V1;

use App\Appointment;
use App\Dokument;
use App\DokumentKommentar;
use App\DokumentLike;
use App\Http\Controllers\API\ApiController;
use App\Http\VideoStream;
use App\Jobs\DocumentIndex;
use App\Models\DocumentSubtitle;
use App\Section;
use App\Task;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends ApiController
{

    /**
     * @OA\Post (
     *     tags={"v1","groups","document"},
     *     path="/api/v1/documents",
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
     *       name="document",
     *       required=true,
     *       in="query",
     *       description="the document",
     *         @OA\Schema(
     *           type="file"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="parent_id",
     *       required=true,
     *       in="query",
     *       description="the document's parent id",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="model_id",
     *       required=true,
     *       in="query",
     *       description="the id of the model which the document is to be attached to",
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
     *     @OA\Response(response="200", description="Create and upload a new document")
     * )
     */
    public function createDocument(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if ($cloud_user == null) {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }

        $file = $request->file('document');
        if ($file == null)
            return redirect()->back();

        if ($request->input('incremental') == "true") {
            $model_id = DB::table('model_dokument')
                ->where('model_type', '=', $request->input('model_type'))
                ->max('model_id');

            if ($model_id == null) $model_id = 1;
            else $model_id++;
        } else {
            $model_id = $request->input('model_id');
        }

        if (Dokument::where('name', '=', $file->getClientOriginalName())
            ->where('parent_id', '=', $request->input("parent_id"))
            ->whereIn('id', function ($query) use ($model_id, $request) {
                $query->select('dokument_id')->from('model_dokument')->where([
                    'model_id' => $model_id,
                    'model_type' => $request->input('model_type'),
                ]);
            })->exists()) {
            return $this->createJsonResponse("This file already exists.", true, 401);
        }
        $lastFolder = null;

        DB::beginTransaction();
        try {
        if ($request->has("path"))
        {
           $paths = explode("/",$request->input("path"));
           array_pop($paths);
           foreach ($paths as $path)
           {
               if($path == "" || $path == null)
                   continue;

               // check if there is a folder with that name
               $folder =  Dokument::where('name', '=', $path)
                   ->where("type","=","folder")
                   ->where('parent_id', '=', $lastFolder ? $lastFolder->id : $request->input('parent_id'))
                   ->whereIn('id', function ($query) use ($model_id, $request) {
                       $query->select('dokument_id')->from('model_dokument')->where([
                           'model_id' => $model_id,
                           'model_type' => $request->input('model_type'),
                       ]);
                   })->lockForUpdate()->first();
               if($folder == null)
               {
                   $dokument = new Dokument();
                   $dokument->name = $path;
                   $dokument->file_type = "Ordner";
                   $dokument->size = 0;
                   $dokument->parent_id = $lastFolder ? $lastFolder->id : $request->input('parent_id');
                   $dokument->owner_type = "cloudid";
                   $dokument->owner_id = $cloud_user->id;
                   $dokument->type = "folder";
                   $dokument->disk_name = "";
                   $dokument->save();

                   DB::table('model_dokument')->insert([
                       'model_id' => $request->input('model_id'),
                       'model_type' => $request->input('model_type'),
                       'dokument_id' => $dokument->id,
                   ]);

                   $lastFolder = $dokument;
               } else
               {
                   $lastFolder = $folder;
               }
           }
        }

        $path = $file->store("documents");
        $dokument = new Dokument();
        $dokument->name = $request->has("filename") ? $request->input("filename") : $file->getClientOriginalName();
        $dokument->file_type = $file->getClientOriginalExtension();
        $dokument->size = $file->getSize();
        $dokument->parent_id =$lastFolder ? $lastFolder->id : $request->input('parent_id');
        $dokument->owner_id = $cloud_user->id;
        $dokument->owner_type = "cloudid";
        $dokument->type = "file";
        $dokument->disk_name = $path;
        $dokument->checksum = sha1_file(storage_path("app/" . $path));
        $dokument->access_hash = Str::random(32);
        $dokument->save();


        DB::table('model_dokument')->insert([
            'model_id' => $model_id,
            'model_type' => $request->input('model_type'),
            'dokument_id' => $dokument->id,
        ]);

        // notifiy in the feed
        $model = $dokument->model();
        if ($model != null) {
            $model->notifiyFeed($dokument);
        }
            DB::commit();
            // all good
        } catch (\Exception $e) {
            DB::rollback();
            // something went wrong
        }

        try {
            DocumentIndex::dispatch($dokument);
        } catch (\Exception $e)
        {
            //
        }
        return $this->createJsonResponse("document created.", false, 200, ["document" => $dokument]);
    }

    /**
     * @OA\Post (
     *     tags={"v1","groups","document"},
     *     path="/api/v1/documents/folders",
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
     *       name="folder",
     *       required=true,
     *       in="query",
     *       description="the folder's name",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="parent_id",
     *       required=true,
     *       in="query",
     *       description="the folder's parent id",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="model_id",
     *       required=true,
     *       in="query",
     *       description="the id of the model which the folder is to be attached to",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="model_type",
     *       required=true,
     *       in="query",
     *       description="the type of the model which the folder is to be attached to",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Create a new folder")
     * )
     */
    public function createFolder(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if ($cloud_user == null) {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }

        if (Dokument::where('name', '=', $request->input('folder'))
            ->where('parent_id', '=', $request->input("parent_id"))
            ->whereIn('id', function ($query) use ($request) {
                $query->select('dokument_id')->from('model_dokument')->where([
                    'model_id' => $request->input('model_id'),
                    'model_type' => $request->input('model_type'),
                ]);
            })->exists()) {
            return $this->createJsonResponse("This folder already exists.", true, 400);
        }

        $dokument = new Dokument();
        $dokument->name = $request->input('folder');
        $dokument->file_type = "Ordner";
        $dokument->size = 0;
        $dokument->parent_id = $request->input('parent_id');
        $dokument->owner_type = "cloudid";
        $dokument->owner_id = $cloud_user->id;
        $dokument->type = "folder";
        $dokument->disk_name = "";
        $dokument->access_hash = Str::random(32);
        $dokument->save();

        DB::table('model_dokument')->insert([
            'model_id' => $request->input('model_id'),
            'model_type' => $request->input('model_type'),
            'dokument_id' => $dokument->id,
        ]);

        return $this->createJsonResponse("folder created.", false, 200, ["document" => $dokument]);
    }

    /**
     * @OA\Post (
     *     tags={"v1","groups","document"},
     *     path="/api/v1/documents/move",
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
     *       name="document_id",
     *       required=true,
     *       in="query",
     *       description="document id",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="parent_id",
     *       required=true,
     *       in="query",
     *       description="the document's new parent id",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Move a document to another parent")
     * )
     */
    public function moveDocument(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if ($cloud_user == null) {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }
        $document = Dokument::find($request->input('document_id'));
        if (!$document)
            return $this->createJsonResponse("Document not found.", true, 400);
        if (!$request->input('parent_id'))
            return $this->createJsonResponse("Parent is invalid.", true, 400);
        $document->parent_id = $request->input('parent_id');
        $document->save();

        return $this->createJsonResponse("document moved.", false, 200, ["document" => $document]);
    }

    public function moveOrCopyDocument(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if ($cloud_user == null) {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }
        $document = Dokument::find($request->input('document_id'));
        if (!$document)
            return $this->createJsonResponse("Document not found.", true, 400);

        if($request->input("mode") == "move") {
            $document->parent_id = $request->input('parent_id');
            $document->save();

            DB::table('model_dokument')->where([
                'dokument_id' => $document->id,
            ])->update([
                'model_id' => $request->input('newModelId'),
                'model_type' => $request->input('newModelType'),
            ]);
        }

        if($request->input("mode") == "copy") {
            $this->copyFolder($document,$request->input('newModelId'), $request->input('newModelType'), $request->input("parent_id"),$cloud_user);
        }
        return $this->createJsonResponse("document moved.", false, 200, ["document" => $document]);
    }

    public function copyFolder(Dokument $document, $model_id, $model_type, $parent_id, $cloud_user)
    {
        if($document->type == "folder") {

            $folder = new Dokument();
            $folder->name = $document->name;
            $folder->file_type = "Ordner";
            $folder->size = 0;
            $folder->parent_id = $parent_id;
            $folder->owner_type = "cloudid";
            $folder->owner_id = $cloud_user->id;
            $folder->type = "folder";
            $folder->disk_name = "";
            $folder->save();

            DB::table('model_dokument')->insert([
                'model_id' => $model_id,
                'model_type' => $model_type,
                'dokument_id' => $folder->id,
            ]);

            foreach ($document->childern() as $child)
            {
                $this->copyFolder($child,$model_id, $model_type, $folder->id,$cloud_user);
            }
        } else {
            $randomNewName = str_random(32) . $document->file_type;
            $fileName = $document->disk_name;
            $path = "documents/" . $randomNewName;
            Storage::copy($fileName, $path);

            $dokumentNew = new Dokument();
            $dokumentNew->name = $document->name;
            $dokumentNew->file_type = $document->file_type;
            $dokumentNew->size = $document->size;
            $dokumentNew->parent_id = $parent_id;
            $dokumentNew->owner_id = $cloud_user->id;
            $dokumentNew->owner_type = "cloudid";
            $dokumentNew->type = "file";
            $dokumentNew->disk_name = $path;
            $dokumentNew->checksum = sha1_file(storage_path("app/" . $path));
            $dokumentNew->save();


            DB::table('model_dokument')->insert([
                'model_id' => $model_id,
                'model_type' => $model_type,
                'dokument_id' => $dokumentNew->id,
            ]);
        }
    }
    /**
     * @OA\Post (
     *     tags={"v1","groups","document"},
     *     path="/api/v1/documents/rename",
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
     *       name="document_id",
     *       required=true,
     *       in="query",
     *       description="document id",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="name",
     *       required=true,
     *       in="query",
     *       description="the document's new name",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Rename a document to another parent")
     * )
     */
    public function renameDocument(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if ($cloud_user == null) {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }
        $document = Dokument::find($request->input('document_id'));
        if (!$document)
            return $this->createJsonResponse("Document not found.", true, 400);
        $document->name = $request->input('name');
        $document->save();

        return $this->createJsonResponse("document renamed.", false, 200, ["document" => $document]);
    }

    /**
     * @OA\Post (
     *     tags={"v1","groups","document"},
     *     path="/api/v1/documents/delete",
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
     *       name="document_id",
     *       required=true,
     *       in="query",
     *       description="document id",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Delete a document and all children")
     * )
     */
    public function deleteDocument(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if ($cloud_user == null) {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }

        $document = Dokument::find($request->input('document_id'));
        if (!$document)
            return $this->createJsonResponse("Document not found.", true, 400);

        $document->delete();
        return $this->createJsonResponse("document deleted.", false, 200, []);
    }

    /**
     * @OA\Post (
     *     tags={"v1","groups","document"},
     *     path="/api/v1/documents/{documentId}",
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
     *       name="documentId",
     *       required=true,
     *       in="query",
     *       description="document id",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="retrieve a document")
     * )
     */
    public function downloadDocument($documentId, Request $request)
    {
        //  $cloud_user = parent::getUserForToken($request);
        //  if($cloud_user == null)
        //  {
        //  return $this->createJsonResponse("This token is not valid.", true, 400);
        // }

        $document = Dokument::find($documentId);
        if (!$document)
            return $this->createJsonResponse("Document not found.", true, 400);

        set_time_limit(0);

        $fs = Storage::disk()->getDriver();

        $fileName = $document->disk_name;

        $mimeType = Storage::mimeType($fileName);
        $name = $document->name;
        if (str_contains($name, ".mp4")) {
            $stream = new VideoStream(storage_path("app/" . $fileName),$mimeType);
            return response()->stream(function () use ($stream) {
                $stream->start();
            });
        }
        $stream = $fs->readStream($fileName);

        if (ob_get_level()) ob_end_clean();

        return response()->stream(
            function () use ($stream) {
                fpassthru($stream);
            },
            200,
            [
                'Content-Type' => $mimeType,
                'Content-disposition' => 'attachment; filename="' . $document->name . '"',
            ]);
    }

    public function updateDocument($document_id, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if ($cloud_user == null) {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }
        $document = Dokument::find($document_id);
        if (!$document)
            return $this->createJsonResponse("Document not found.", true, 400);

        $file = $request->file('document');
        if ($file == null)
            return redirect()->back();

        try {
            $fileName = $document->disk_name;
            Storage::delete($fileName);
        } catch (\Exception $exception)
        {
            //
        }

        $path = $file->store("documents");
        $document->disk_name = $path;
        $document->checksum = sha1_file(storage_path("app/" . $path));

        $document->save();

        return $this->createJsonResponse("document moved.", false, 200, ["document" => $document]);
    }

    public function getDocument($documentId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if ($cloud_user == null) {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }
        //  $cloud_user = parent::getUserForToken($request);
        //  if($cloud_user == null)
        //  {
        //  return $this->createJsonResponse("This token is not valid.", true, 400);
        // }

        $document = Dokument::find($documentId);

        if (!$document)
            return $this->createJsonResponse("Document not found.", true, 400);

        return $this->createJsonResponse("ok", false, 200, ["document" => $document]);
    }

    public function zipDocument(Request $request)
    {
        $zip = new \ZipArchive();
        $cloud_user = parent::getUserForToken($request);
        if ($cloud_user == null) {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }

        @unlink(storage_path('dokument.zip'));
        $fileName = storage_path('dokument.zip');
        $documents = Dokument::whereIn("id",$request->input("documents_ids"))->get();
        if ($zip->open($fileName, \ZipArchive::CREATE)== TRUE) {
            foreach ($documents as $document) {
                $zip->addFile( storage_path("app/" . $document->disk_name),$document->name);
            }
            $zip->close();
        }

        return response()->download($fileName);
    }

    /**
     * @OA\Get (
     *     tags={"v1","groups","document"},
     *     path="/api/v1/documents/list",
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
     *       name="model_id",
     *       required=true,
     *       in="query",
     *       description="the id of the model",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="model_type",
     *       required=true,
     *       in="query",
     *       description="the type of the model",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="withPath",
     *       required=false,
     *       in="query",
     *       description="add this parameter if you need the full path",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Lists all documents for a given model")
     * )
     */
    public function listDocuments(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if ($cloud_user == null) {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }
        $model_type = $request->input("model_type");
        $model_id = $request->input("model_id");
        $model = null;
        if($model_type == "section")
        {
            $model = Section::find($model_id);
        }
        if($model_type == "event")
        {
            $model = Appointment::find($model_id);
        }
        if($model_type == "task")
        {
            $model = Task::find($model_id);
        }

        if($model != null && !$model->checkRights(new Dokument(), $cloud_user))
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }

        $ids = DB::table('model_dokument')
            ->where('model_id', '=', $request->input("model_id"))
            ->where('model_type', '=', $request->input("model_type"))
            ->pluck('dokument_id');

        $documents = Dokument::whereIn('id',$ids)->get();
        if($request->has("withPath") && $request->input("withPath") == true) {
            $documents->each->append('parent_key');
        }
        return $this->createJsonResponse("ok", false, 200, ["documents" => $documents]);
    }

    public function callbackDocument($documentId, Request $request)
    {
        if (!$request->has("key"))
            return response()->json(["error" => 1]);

        $dokument = Dokument::findOrFail($documentId);
        $user = parent::getUserForToken($request);

        //check Tokens
        $documentToken = hash("sha256", $request->getHost()) . $dokument->id;
        if ($documentToken != $request->input("key"))
            return response()->json(["error" => 1]);

        $status = $request->input("status");
        if ($status == 1) //document being edited
        {
            //do nothing here (maybe lock for other edits?)
        } elseif ($status == 2) {
            $url = $request->input("url");
            if (config('stupla.documents.onlyoffice.isDocker', false)) {
                $url = str_replace("localhost:8080", "onlyoffice:80", $url); //needed for Docker
            }
            $content = file_get_contents($url);
            Storage::put($dokument->disk_name, $content);
            $dokument->updated_at = new DateTime();
            $dokument->save(); //update timestamps
        } elseif ($status == 3) { //some error during saving
            //do nothing here
        } elseif ($status == 4) // closed with no changes
        {
            //do nothing here
        } elseif ($status == 6) // like 1, but current state already saved
        {
            //do nothing here
        } elseif ($status == 7) //error while force-saving
        {
            //do nothing here
        } else { // unknown status code
            return response()->json(["error" => 1]);
        }
        return response()->json(["error" => 0]);
    }

    public function getSubtitles($documentId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if ($cloud_user == null) {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }

        $subtitles = DocumentSubtitle::where("dokument_id", "=", $documentId)->get();

        return $this->createJsonResponse("ok", false, 200, ["subtitles" => $subtitles]);
    }

    public function addSubtitle($documentId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if ($cloud_user == null)
            return $this->createJsonResponse("This token is not valid.", true, 400);

        if (!$request->has("language"))
            return $this->createJsonResponse("Language not provided.", true, 400);

        if (!$request->has("content"))
            return $this->createJsonResponse("Content not provided.", true, 400);

        $document = Dokument::findOrFail($documentId);

        if (DocumentSubtitle::where("dokument_id", "=", $document->id)->where("language", "=", $request->input("language"))->count())
            return $this->createJsonResponse("Language already exists.", true, 400);

        $subtitle = new DocumentSubtitle();
        $subtitle->dokument_id = $document->id;
        $subtitle->language = $request->input("language");
        $subtitle->subtitle = $request->input("content");
        $subtitle->save();

        return $this->createJsonResponse("ok", false, 200, ["subtitle" => $subtitle]);
    }

    public function getSubtitle($documentId, $subtitleId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if ($cloud_user == null) {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }

        $subtitle = DocumentSubtitle::findOrFail($subtitleId);
        if ($subtitle->dokument_id != $documentId)
            return $this->createJsonResponse("Subtitle not in provided dokument.", true, 400);

        return $subtitle->subtitle;
    }

    public function editSubtitle($documentId, $subtitleId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if ($cloud_user == null) {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }

        $subtitle = DocumentSubtitle::findOrFail($subtitleId);
        if ($subtitle->dokument_id != $documentId)
            return $this->createJsonResponse("Subtitle not in provided dokument.", true, 400);

        $subtitle->subtitle = $request->input("content", $subtitle->subtitle);
        $subtitle->save();

        return $this->createJsonResponse("ok", false, 200, []);
    }

    public function detailsDocument($documentId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if ($cloud_user == null) {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }
        $document = Dokument::find($documentId);

        if (!$document)
            return $this->createJsonResponse("Document not found.", true, 400);

        return $this->createJsonResponse("ok", false, 200, ["details" => ["parts" => $document->documentParts, "inIndex" => $document->documentParts->count() > 0 ]]);
    }
}
