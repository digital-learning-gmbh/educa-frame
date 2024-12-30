<?php

namespace App;

use App\Http\Controllers\API\ApiController;
use App\Observers\FeedObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;
use StuPla\CloudSDK\formular\models\Formular;

class Task extends Model implements HasDocuments, HasMeetings
{

    public static $FEED_CREATE = "task.create";
    public static $FEED_DELETED = "task.deleted";
    public static $FEED_SUBMITTED = "task.submitted";
    public static $FEED_RATED = "task.rated";
    public static $FEED_RESET = "task.reset";

    protected $casts = [
        'form_template' => 'json'
    ];

    protected $appends = ['state','archived','documentCount'];

    public function creator()
    {
        return $this->belongsTo('App\CloudID','cloud_id');
    }

    public function dokumente($parent_id = null)
    {
        $ids = DB::table('model_dokument')
            ->where('model_id', '=', $this->id)
            ->where('model_type', '=', 'task')
            ->pluck('dokument_id')->toArray();
        if($parent_id == null)
        {
            return Dokument::find($ids);
        }
        return Dokument::where('parent_id', '=', '0')->whereIn('id', $ids)->get();
    }

    public function getDocumentCountAttribute()
    {
        return $this->dokumente()->count();
    }

    public function allAffectedPersonen()
    {
        foreach ($this->attendees as $attendee)
        {
            $this->einreichungForUser($attendee->id);
        }
        foreach ($this->sections as $section)
        {
            $group = $section->group;
            foreach ($group->members() as $member) {
                if($section->isAllowed($member, PermissionConstants::EDUCA_SECTION_TASK_RECEIVE) && $member->id != $this->cloud_id)
                {
                    $this->einreichungForUser($member->id);
                }
            }
        }
    }

    public function sections()
    {
        return $this->belongsToMany('App\Section','task_section');
    }

    public function attendees()
    {
        return $this->belongsToMany('App\CloudID', "task_cloud_i_d", "task_id","cloud_id");
    }

    public function submissions()
    {
        return $this->hasMany('App\Submission');
    }

    public function submissiontemplate()
    {
        return $this->hasOne('App\SubmissionTemplate', 'task');
    }

    public function einreichungForUser($cloudId)
    {
        $einreichung = Submission::where('task_id', '=', $this->id)->where('cloudid','=',$cloudId)->get()->first();
        if($einreichung != null) {
            // Dokumente aus Template kopieren, falls noch nicht geschehen
            // Da unmittelbar nach dem Erstellen der Aufgabe alle Aufgaben neu geladen werden, müsste dieser Ansatz immer klappen
            if($this->submissiontemplate != null && $this->submissiontemplate->documentCount > 0 && $einreichung->documentCount == 0)
            {
                $ids = [];
                foreach($this->submissiontemplate->dokumente(0) as $document)
                {
                    $d = $document->duplicate($cloudId);
                    $ids = array_merge($ids, $d);
                }
                $relations = [];
                foreach ($ids as $id)
                {
                    $relations[] = ["model_id" => $einreichung->id, "model_type" => "submission", "dokument_id" => $id];
                }
                DB::table('model_dokument')->insert($relations);
            }
            return $einreichung;
        }
        $einreichung = new Submission;
        $einreichung->stage = "draft";
        $einreichung->task_id = $this->id;
        $einreichung->cloudid = $cloudId;
        $einreichung->save();
        return $einreichung;
    }

    public function getStateAttribute()
    {
        if(ApiController::user() == null)
            return "unknown";
        if(ApiController::user()->id == $this->cloud_id)
        {
            $this->allAffectedPersonen();
            $collector = "completed";
            foreach ($this->submissions as $submission)
            {
                if($submission->stage != "completed" && $collector == "completed")
                    $collector = "review";
                if($submission->stage == "draft")
                    $collector = "draft";
            }
            if($collector == "completed")
                return "completed";
            if(Carbon::now()->isAfter(Carbon::parse($this->end)) || $collector == "review")
            {
                return "review";
            }
            return "draft";
        }
        $einreichung = $this->einreichungForUser(ApiController::user()->id);
        return $einreichung->stage;
    }

    public function getArchivedAttribute()
    {
        if($this->state != "completed")
            return false;

        $end = Carbon::parse($this->end);
        if ($end->isPast() && $end->diffInDays(now()) >= Config::get("stupla.taskArchive.duration", 28)) {
            return true;
        }
        return false;
    }

    public function addTeilnehmer($cloudId)
    {
        $this->addTeilnehmerById($cloudId->id);
    }

    public function addTeilnehmerById($cloudId)
    {
        if(!$this->isTeilnehmerInvited($cloudId)) {
            \Illuminate\Support\Facades\DB::table('task_cloud_i_d')->insert([
                'task_id' => $this->id,
                'cloud_id' => $cloudId,
            ]);
            FeedObserver::addUserAcitivty($cloudId, Auth::user(), "cloud", self::$FEED_CREATE, $this->id, $this);
        }
    }

    public function addSection($group)
    {
        $this->addSectionById($group->id);
    }

    public function addSectionById($groupId)
    {
        if(!$this->isSectionInvited($groupId)) {
            \Illuminate\Support\Facades\DB::table('task_section')->insert([
                'task_id' => $this->id,
                'section_id' => $groupId,
            ]);
            FeedObserver::addSectionActivity($groupId, Auth::user(), "App\CloudID", self::$FEED_CREATE, $this->id,  $this);
        }
    }

    public function isSectionInvited($groupId)
    {
        return \Illuminate\Support\Facades\DB::table('task_section')->where([
            'task_id' => $this->id,
            'section_id' => $groupId,
        ])->exists();
    }

    public function isTeilnehmerInvited($cloudId)
    {
        return \Illuminate\Support\Facades\DB::table('task_cloud_i_d')->where([
            'task_id' => $this->id,
            'cloud_id' => $cloudId,
        ])->exists();
    }

    public function delete()
    {
        // notifiy in the feed
        $this->allAffectedPersonen();
        foreach ($this->submissions as $submission) {
            $attendee = $submission->ersteller;
            FeedObserver::addUserAcitivty($attendee->id, $this->creator, "App\CloudID", self::$FEED_DELETED, $this->id, $this);
        }
        //

        \Illuminate\Support\Facades\DB::table('task_cloud_i_d')->where([
            'task_id' => $this->id,
        ])->delete();
        \Illuminate\Support\Facades\DB::table('task_section')->where([
            'task_id' => $this->id,
        ])->delete();
        \Illuminate\Support\Facades\DB::table('submission_templates')->where([
            'task' => $this->id,
        ])->delete();
        foreach($this->submissions()->get() as $submission) $submission->delete();
        foreach($this->dokumente() as $document) $document->delete();
        foreach(Formular::where(["id" => $this->formular_id])->get() as $formular)
            $formular->deleteAll();
        return parent::delete();
    }

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'start' => $this->start,
            'end' => $this->end,
            'description' => $this->description,
        ];
    }

    public function notifiyFeed(Dokument $dokument)
    {
        $user = $dokument->creator;
        $id = ($user == null) ? "" : $user->id;
        foreach ($this->attendees as $attendee)
        {
            FeedObserver::addUserAcitivty($attendee->id, $dokument->creator,"App\CloudID",Dokument::$FEED_INFO,$this->id."_".$id, $dokument);
        }
        foreach ($this->sections as $section)
        {
            FeedObserver::addSectionActivity($section->id, $dokument->creator,"App\CloudID",Dokument::$FEED_INFO,$this->id."_".$id, $dokument);
        }
    }

    public function checkRights(Dokument $dokument, $cloudid, $type = "view"): bool
    {
        if($type == "view")
            return true;
        if($this->creator->id == $cloudid->id)
            return true;
        return false;
    }


    public function checkMeetingRights(CloudID $user): bool
    {
        return $user->id == $this->creator->id;
    }

    public function name()
    {
        return $this->title;
    }

    public function welcomeText()
    {
        return "";
    }
}
