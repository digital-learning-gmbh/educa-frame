<?php
namespace App;

use App\Models\DokumentParts;
use App\Models\TaskTemplateSubmissionTemplate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Laravel\Scout\Searchable;

class Dokument extends Model
{
    public static $models_types = ["section", "task", "event"];
    protected $perviewTypes = ["doc", "docx", "jpg", "jpeg", "png", "pdf"];
    protected $onlyOfficeTypes= [
        "word" => ["doc", "docx", "odt", "rtf", "txt"],
        "cell" => ["xls", "xlsx", "ods", "csv"],
        "slide" => ["ppt", "pptx", "odp"]];
    protected $sperator = "/";

    protected $appends = ["with_external_viewer"];

    public static $FEED_INFO = "document.updated";

    public function duplicate($newOwnerId, $parentId = 0)
    {
        $ids = [];

        $duplicate = new Dokument;
        $duplicate->name = $this->name;
        $duplicate->file_type = $this->file_type;
        $duplicate->parent_id = $parentId;
        $duplicate->type = $this->type;
        $duplicate->owner_id = $newOwnerId;
        $duplicate->owner_type = $this->owner_type;
        $duplicate->size = $this->size;

        if($this->type == "file")
        {
            $pathinfo = pathinfo($this->name);
            $extension = "";
            if(array_key_exists("extension", $pathinfo))
                $extension = ".".$pathinfo["extension"];
            $newfilename = "documents/".uniqid().$extension;
            Storage::copy($this->disk_name, $newfilename);
            $duplicate->disk_name = $newfilename;
        }

        $duplicate->save();
        $ids[] = $duplicate->id;

        $children = Dokument::where("parent_id", "=", $this->id)->get();
        foreach($children as $child)
        {
            $ids = array_merge($ids, $child->duplicate($newOwnerId, $duplicate->id));
        }
        return $ids;
    }

    public function supportPreview()
    {
        return in_array($this->file_type, $this->perviewTypes);
    }

    public function isWord(){
        return $this->file_type == "doc" || $this->file_type == "docx";
    }

    public function childern()
    {
        return Dokument::where('parent_id', '=', $this->id)->get();
    }

    public function getOOType()
    {
        if(in_array($this->file_type, $this->onlyOfficeTypes["word"]))
            return "word";
        if(in_array($this->file_type, $this->onlyOfficeTypes["cell"]))
            return "cell";
        if(in_array($this->file_type, $this->onlyOfficeTypes["slide"]))
            return "slide";

        return false;
    }

    public function delete()
    {
        if($this->type == "file") { // if file, we will delete this
            Storage::delete($this->disk_name);
        } else { // folders have children, delete them too
            $children = Dokument::where("parent_id", "=", $this->id)->get();
            foreach($children as $child)
            {
                $child->delete();
            }
        }
        // delete references
        DB::table('model_dokument')->where('dokument_id', '=', $this->id)->delete();
        return parent::delete();
    }

    public function childernDocumentsHook()
    {
        return $this->hasMany('App\Dokument','parent_id','id');
    }

    public function childernDocuments()
    {
        return $this->childernDocumentsHook()->with('childernDocuments');
    }

    public function creator()
    {
        return $this->belongsTo('App\CloudID','owner_id')->withTrashed();
    }

    public function parent()
    {
        return $this->belongsTo('App\Dokument','parent_id');
    }

    public function getParentKeyAttribute()
    {
        if($this->parent_id == null || $this->parent_id == 0 || $this->parent == null)
            return $this->name;
        return $this->parent->parent_key.$this->sperator.$this->name;
    }

    public function modelInformation()
    {
        return DB::table('model_dokument')->where([
            'dokument_id' => $this->id,
        ])->first();
    }

    public function model() : ?HasDocuments
    {
        $modelInformation = $this->modelInformation();
        if($modelInformation == null)
            return null;

        if($modelInformation->model_type == "group")
        {
            return Group::find($modelInformation->model_id);
        }
        if($modelInformation->model_type == "section")
        {
            return Section::find($modelInformation->model_id);
        }
        if($modelInformation->model_type == "event")
        {
            return Appointment::find($modelInformation->model_id);
        }
        if($modelInformation->model_type == "task")
        {
            return Task::find($modelInformation->model_id);
        }
        if($modelInformation->model_type == "klasse")
        {
            return Klasse::find($modelInformation->model_id);
        }
        if($modelInformation->model_type == "submission")
        {
            return Submission::find($modelInformation->model_id);
        }
        if($modelInformation->model_type == "student")
        {
            return Schuler::find($modelInformation->model_id);
        }
        if($modelInformation->model_type == "teacher")
        {
            return Lehrer::find($modelInformation->model_id);
        }
        if($modelInformation->model_type == "curriculum")
        {
            return Lehrplan::find($modelInformation->model_id);
        }
        if($modelInformation->model_type == "task_template_submission_template")
        {
            return TaskTemplateSubmissionTemplate::find($modelInformation->model_id);
        }
    	return null;
    }
    public function getWithExternalViewerAttribute()
    {
        return $this->file_type == "doc" || $this->file_type == "docx" || $this->file_type == "ppt" || $this->file_type == "pptx" || $this->file_type == "xlsx"  || $this->file_type == "xls";
    }

    public function documentParts()
    {
        return $this->hasMany(DokumentParts::class);
    }
}
