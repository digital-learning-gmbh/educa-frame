<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use StuPla\CloudSDK\formular\models\FormularRevision;

class User extends Authenticatable implements HasContactHistory
{
    use Notifiable;

    protected $guard = 'verwaltung';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function schulen()
    {
        return $this->belongsToMany('App\Schule');
    }

    public function boards()
    {
        return $this->hasMany('App\Board');
    }

    public function boardsGeteilt()
    {
        return $this->belongsToMany('App\Board');
    }

    public function getDisplayNameAttribute()
    {
        return $this->firstname." ".$this->lastname;
    }

    public function getCloudID()
    {
        $relation = DB::table('model_cloud_id')->where('appName','LIKE','stupla')->where('loginId','=',$this->id)->first();
        if($relation != null)
        {
            return CloudID::find($relation->cloud_i_d_id);
        }
        return null;
    }

    public function getAddInfo()
    {
        if ($this->info_id == null) {
            $addInfo = new AdditionalInfo();
            $addInfo->save();
            $this->info_id = $addInfo->id;
            $this->save();
        }
        else{
            $addInfo = AdditionalInfo::find($this->info_id);
            if($addInfo == null)
            {
                // wer auch immer hier die Daten kaputt macht
                $this->info_id = null;
                $this->save();
                return $this->getAddInfo();
            }
        }
        return $addInfo;
    }

    public function getAddinfoAttribute()
    {
        return $this->hasOne("App\AdditionalInfo","id","info_id");
    }

    public function getEmails()
    {
        $addInfo = $this->getAddInfo();
        if($addInfo->email != null)
            return [$addInfo->email];

        if($this->email != null)
            return [$this->email];

        if($addInfo->email_private != null)
            return [$addInfo->email_private];

        if($addInfo->email_other != null)
            return [$addInfo->email_other];

        return [];
    }

    public function studium()
    {
        return $this->belongsToMany('App\Studium','user_studium');
    }

    public function dokumente($parent_id = null)
    {
        $ids = DB::table('model_dokument')
            ->where('model_id', '=', $this->id)
            ->where('model_type', '=', 'employee')
            ->pluck('dokument_id')->toArray();
        if ($parent_id == null) {
            return Dokument::find($ids);
        }
        return Dokument::where('parent_id', '=', '0')->whereIn('id', $ids)->get();
    }

    public function delete()
    {

        return DB::transaction( function (){
            $counts = [];

            //studies
            $studies = $this->studium();
            $counts["studies"] = $studies->count();
            $studies->sync([]);

            //studies
            $schools = $this->schulen();
            $counts["schools"] = $schools->count();
            $schools->sync([]);

            //boards
            $boards = $this->boards();
            $counts["boards"] = $boards->count();
            foreach($boards->get() as $b)
                $b->delete();

            //documents
            $docs = $this->dokumente();
            $counts["documents"] = $docs->count();
            $docs->each->delete();

            //additional info
            if ($this->info_id > 0) {
                $iId = $this->info_id;
                $this->info_id = null;
                $this->save();
                AdditionalInfo::findOrFail($iId)->delete();
            }

            //history entries

            $hentries = DB::table("contact_history_entries_models")->where(["model_type" => ContactHistoryEntry::$MODEL_TYPE_EMPLOYEE, "model_id" => $this->id]);
            $counts["contact_history_entries_models"] = $hentries->count();
            $hentries->delete();

            $formRevs = FormularRevision::where(["user_id" => $this->id])->get();
            $counts["formular_revisions"] = $formRevs->count();
            $formRevs->each->delete();

            parent::delete();
            return $counts;
        });
    }
}
