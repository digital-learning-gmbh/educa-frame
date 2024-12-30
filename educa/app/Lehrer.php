<?php

namespace App;

use App\ClingoModels\ClingoMapper;
use App\Http\Controllers\Shared\ClingoConstant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Lehrer extends Authenticatable implements ClingoMapper, HasContactHistory, HasDocuments
{
    use Notifiable;

    use LogsActivity;
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    protected $guard = 'dozent';

    protected $fillable = [
        'firstname', 'lastname', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function faecher()
    {
        return $this->belongsToMany("App\Fach","lehrer_fach");
    }

    public function schulen()
    {
        return $this->belongsToMany('App\Schule', "lehrer_schule");
    }

    public function schule()
    {
        return $this->belongsTo('App\Schule',"schule_id");
    }

    public function abwesenheit()
    {
        return $this->hasMany('App\LehrerFehlzeit', "dozent_id");
    }

    //
    public function loadFromClingo($string)
    {
        // TODO: Implement loadFromClingo() method.
    }

    public function getClingo($withRelationShips)
    {
        $string = ClingoConstant::basicClingoTranslate(ClingoConstant::LEHRER_IDENTIFIER,[$this->getClingoID()]);
        if($withRelationShips)
        {
            foreach ($this->faecher as $fach)
            {
                $string .= ClingoConstant::basicClingoTranslate(ClingoConstant::LEHRER_FACH_IDENTIFIER,[$this->getClingoID(), $fach->getClingoID()]);
            }
        }
        return $string;
    }

    public function getClingoID()
    {
        return ClingoConstant::LEHRER_IDENTIFIER ."_".$this->id;
    }

    public function getDisplayNameAttribute()
    {
        if($this->getAddInfo()->displayname != null && $this->getAddInfo()->displayname != "")
            return $this->getAddInfo()->displayname;
        return $this->lastname.", ".$this->firstname;
    }

    public static function findByClingoId($clingo)
    {
        return Lehrer::findOrFail(str_replace(ClingoConstant::LEHRER_IDENTIFIER."_","",$clingo));
    }

    public function addinfo()
    {
        return $this->hasOne("App\AdditionalInfo","id","info_id");
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
            $addInfo = AdditionalInfo::findOrFail($this->info_id);
        }
        return $addInfo;
    }


    public function getCloudID()
    {
        $relation = DB::table('model_cloud_id')->where('appName','LIKE','klassenbuch')->where('loginId','=',$this->id)->first();
        if($relation != null)
        {
            return CloudID::find($relation->cloud_i_d_id);
        }
        // check if we forget to match..
        if(CloudID::where('email', 'LIKE',$this->email)->first() != null)
        {
            $cloudId = CloudID::where('email', 'LIKE',$this->email)->first();
            // lets fix that
            DB::table('model_cloud_id')->insert([
                'model' => 'App\Lehrer',
                'appName' => 'klassenbuch',
                'loginId' => $this->id,
                'cloud_i_d_id' => $cloudId->id
            ]);

            return $cloudId;
        }
        return null;
    }

    // IBA Extension
    public function getIbaSettingsAttribute()
    {
        $extension = IBADozentExtension::where('lehrer_id', $this->id)->first();
        if($extension == null)
        {
            $extension = new IBADozentExtension;
            $extension->lehrer_id = $this->id;
            $extension->save();
        }
        return $extension;
    }

    public function getStudiengangAttribute()
    {
        return DB::select("SELECT stupla_studia.* from stupla_lehrer_fach, stupla_fach_studium, stupla_studia WHERE stupla_fach_studium.studium_id = stupla_studia.id AND stupla_fach_studium.fach_id = stupla_lehrer_fach.fach_id AND stupla_lehrer_fach.lehrer_id = ".$this->id." GROUP BY stupla_studia.id");
    }

    public function getEmails()
    {
        if($this->email != null)
            return [$this->email];

        $addInfo = $this->getAddInfo();
        if($addInfo->email != null)
            return [$addInfo->email];

        if($addInfo->email_private != null)
            return [$addInfo->email_private];

        if($addInfo->email_other != null)
            return [$addInfo->email_other];

        return [];
    }

    public function notifiyFeed(Dokument $dokument)
    {
        // nichts tun
    }

    public function checkRights(Dokument $dokument, $cloudid, $type = "view"): bool
    {
        return true;
    }

    public function getLatestFormulaDataFor($formula)
    {
        $final = null;
        foreach($formula->revisions as $revision) {
            $data = $this->getFormulaDataFor($revision->id);
            if($data != null)
            {
                $final = $data;
            }
        }
        if($final == null)
        {
            return "[]";
        }
        return $final->formular_data;
    }


    public function getFormulaDataFor($revision)
    {
        return MasterDataExtension::where('model_id','=',$this->id)->where('model_type','=', 'teacher')->where('formular_revision_id','=', $revision)->first();
    }

    public function saveFormulaDataFor($revision, $data)
    {
        $dbModel = $this->getFormulaDataFor($revision);
        if($dbModel == null)
        {
            $dbModel = new MasterDataExtension;
            $dbModel->model_type = "teacher";
            $dbModel->model_id = $this->id;
            $dbModel->formular_revision_id = $revision;
        }
        $dbModel->formular_data = $data;
        $dbModel->save();
    }

    public function dokumente($parent_id = null)
    {
        $ids = DB::table('model_dokument')
            ->where('model_id', '=', $this->id)
            ->where('model_type', '=', 'teacher')
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

            if($this->getCloudID())
                $this->getCloudID()->delete();

            // subjects
            $subjects = $this->faecher();
            $counts["subjects"] = $subjects->count();
            $subjects->sync([]);

            // schools
            $schools = $this->schulen();
            $counts["schools"] = $schools->count();
            $schools->sync([]);

            //absence
            $absence = $this->abwesenheit();
            $counts["absence"] = $absence->count();
            $absence->delete();

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
            $hentries = DB::table("contact_history_entries_models")->where(["model_type" => ContactHistoryEntry::$MODEL_TYPE_TEACHER, "model_id" => $this->id]);
            $counts["contact_history_entries_models"] = $hentries->count();
            $hentries->delete();

            parent::delete();
            return $counts;
        });
    }

}
