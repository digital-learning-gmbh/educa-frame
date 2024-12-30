<?php

namespace App;

use App\Models\TaskTemplateSubmissionTemplate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TaskTemplate extends Model
{
    public function creator()
    {
        return $this->belongsTo('App\CloudID','cloud_id');
    }

    public function dokumente($parent_id = null)
    {
        $ids = DB::table('model_dokument')
            ->where('model_id', '=', $this->id)
            ->where('model_type', '=', 'task_template')
            ->pluck('dokument_id')->toArray();
        if($parent_id === null)
        {
            return Dokument::find($ids);
        }
        return Dokument::where('parent_id', '=', $parent_id)->whereIn('id', $ids)->get();
    }

    public function submissiontemplate()
    {
        return $this->hasOne(TaskTemplateSubmissionTemplate::class, 'task_template_id');
    }

    public function delete()
    {
        if($this->submissiontemplate != null)
            $this->submissiontemplate->delete();
        parent::delete();
    }
}
