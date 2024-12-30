<?php

namespace App;

use App\ClingoModels\ClingoMapper;
use App\Http\Controllers\Shared\ClingoAtomConverter;
use App\Http\Controllers\Shared\ClingoConstant;
use App\Observers\FeedObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use phpseclib3\File\ASN1\Maps\netscape_ca_policy_url;
use StuPla\CloudSDK\formular\models\Formular;

class Klasse extends Model implements ClingoMapper, HasDocuments, HasFormular, HasContactHistory
{
    public static function createPlanungsKlasse(Schuljahr $schuljahr, Kohorte $kohorte, $studientage)
    {
        // if(Klasse::where('type','=','planning_group')->where('schuljahr_id','=', $schuljahr->id)->where('kohorte_id','=',$kohorte->id)->where('daysOfWork','=',$studientage)->exists())
        //    return Klasse::where('type','=','planning_group')->where('schuljahr_id','=', $schuljahr->id)->where('kohorte_id','=',$kohorte->id)->where('daysOfWork','=',$studientage)->first();

        $klasse = new Klasse();
        $klasse->schuljahr_id = $schuljahr->id;
        $klasse->kohorte_id = $kohorte->id;
        $klasse->type = "planning_group";
        $klasse->daysOfWork = $studientage;

        $klasse->fs = $schuljahr->fsForKohorte($kohorte);
        $studientageDispaly = static::getShortTranslationForWeekdays($studientage);
        $klasse->name = $kohorte->name . "_" . $studientageDispaly . "_FS" . $klasse->fs;
        $duplicate = true;
        $letters = range('b', 'z');
        $i = 0;
        while ($duplicate) {
            if (Klasse::where('name', '=', $klasse->name)->exists()) {
                $klasse->name = $kohorte->name . "_" . $studientageDispaly . "_FS" . $klasse->fs . "" . $letters[$i];
            } else {
                $duplicate = false;
            }
            $i++;
        }

        $klasse->von = new \DateTime($schuljahr->start);
        $klasse->bis = new \DateTime($schuljahr->ende);
        $klasse->save();

        // add lehrplan
        $klasse->getLehrplan()->sync($kohorte->lehrplan);
        //
        return $klasse;
    }

    public static function createFreeGroup(Schuljahr $schuljahr, $name)
    {
        $klasse = new Klasse();

        $klasse->type = "free_group";
        $klasse->name = $name;
        $klasse->schuljahr_id = $schuljahr->id;
        $klasse->von = new \DateTime($schuljahr->start);
        $klasse->bis = new \DateTime($schuljahr->ende);
        $klasse->save();

        return $klasse;
    }

    public static function createBlockungsgruppe(Schuljahr $schuljahr, LehrplanEinheit $lehrplanEinheit, $isCategory, Carbon $start, Carbon $end, $studyDays, $fs)
    {
        $klasse = new Klasse();
        $klasse->schuljahr_id = $schuljahr->id;

        if ($studyDays == "block" || $studyDays == null) {
            $klasse->type = "blocking_group";
            $klasse->von = $start->toDateTime();
            $klasse->bis = $end->toDateTime();
            $klasse->daysOfWork = "block";
        } else {
            $klasse->type = "special";
            $klasse->von = new \DateTime($schuljahr->start);
            $klasse->bis = new \DateTime($schuljahr->ende);
            $klasse->daysOfWork = $studyDays;
        }
        $klasse->lehrplan_einheit_id = $lehrplanEinheit->id;

        $klasse->fs = $fs;
        $standort = $schuljahr->schule->abk();
        if ($isCategory) // ggf. noch anpassen hier
        {
            $middle = $lehrplanEinheit->name;

            // try to get parent
            try {
                $parent = $lehrplanEinheit->parent();
                $additional_attributes = $parent != null ? ($parent->parent() ? json_decode($parent->parent()->additional_attributes) : null) : null;
                if ($additional_attributes != null && property_exists($additional_attributes, "shortcut")) {
                    $middle = $additional_attributes->shortcut . $middle;
                } else {
                    // $studiengang = $lehrplanEinheit->lehrplan->studiumRelation->subjectDirection;
                    // $middle = $studiengang->name_short.$middle;
                }
            } catch (\Exception $exception) {
                //
            }

            $studiengang = $lehrplanEinheit->lehrplan->studiumRelation->subjectDirection;
            $middle = $studiengang->name_short . "_" . $middle;
        } else {
            $middle = $lehrplanEinheit->name;
            $additional_attributes = json_decode($lehrplanEinheit->additional_attributes);
            if ($additional_attributes != null && property_exists($additional_attributes, "shortcut")) {
                $middle = $additional_attributes->shortcut;
            }
        }
        $schuljahr_name = $schuljahr->name;

        $klasse->name = $standort . "_" . $middle . "_" . $schuljahr_name . "_FS" . $fs;
        if ($klasse->daysOfWork != "block") {
            $klasse->name .= "_" . self::getShortTranslationForWeekdays($klasse->daysOfWork);
        }

        $klasse->save();

        $klasse->getLehrplan()->sync($lehrplanEinheit->lehrplan);
        return $klasse;
    }

    public static function createClusterGroup(Schuljahr $schuljahr, $courseIds)
    {
        $klasse = new Klasse();
        $standort = $schuljahr->schule->abk();
        $name = $standort . "_";
        $courses = Klasse::whereIn("id", $courseIds)->get();
        $course = $courses->first();

        $fs = [];
        $finaltype = $course->type;
        foreach ($courses as $cours) {
            $fs[] = $cours->fs;
            if ($finaltype != $cours->type) // error!
                return null;
        }
        asort($fs);

        $studiengang = $course->getLehrplan->first()->studiumRelation->subjectDirection;
        $name .= $studiengang->name_short . "_";

        if ($course->type == "blocking_group" || $course->type == "special") {
            $lehrplanEinheit = LehrplanEinheit::find($course->lehrplan_einheit_id);
            if ($lehrplanEinheit != null) {
                $parent = $lehrplanEinheit->parent();
                $nameLehrplan = $lehrplanEinheit->name;
                $additional_attributes = json_decode($lehrplanEinheit->additional_attributes);
                if($additional_attributes != null && property_exists($additional_attributes, "shortcut"))
                    $nameLehrplan = $additional_attributes->shortcut;

                $additional_attributes = $parent != null ? ($parent->parent() ? json_decode($parent->parent()->additional_attributes) : null) : null;
                if ($additional_attributes != null && property_exists($additional_attributes, "shortcut")) {
                    $name .= $additional_attributes->shortcut . $nameLehrplan. "_";
                } else {
                    $name .= $nameLehrplan . "_";
                }
            }
        }

        $name .= $schuljahr->name . "_";

        if ($course->daysOfWork == "block") {
            $name .= "BL_";
        } else {
            $name .= self::getShortTranslationForWeekdays($course->daysOfWork);
            $klasse->daysOfWork = $course->daysOfWork;
        }

        $name .= "_FS";
        foreach ($fs as $f) {
            $name .= $f;
        }
        $klasse->type = "cluster_group";
        $klasse->name = $name;
        $klasse->schuljahr_id = $schuljahr->id;
        $klasse->von = new \DateTime($schuljahr->start);
        $klasse->bis = new \DateTime($schuljahr->ende);
        $klasse->save();
        $klasse->getLehrplan()->sync($course->getLehrplan);

        $klasse->klassen()->sync($courseIds);
        return $klasse;
    }

    public static function getTranslationForWeekdays($weekdays)
    {
        if ($weekdays == "mo_tu")
            return "mo_di";
        if ($weekdays == "tu_we")
            return "di_mi";
        if ($weekdays == "we_th")
            return "mi_do";
        if ($weekdays == "th_fr")
            return "do_fr";
        if ($weekdays == "fr_sa")
            return "fr_sa";
    }

    public static function getShortTranslationForWeekdays($weekdays)
    {
        $shortWeekday = self::getTranslationForWeekdays($weekdays);
        return str_replace("/", "", str_replace("_", "", strtoupper($shortWeekday)));
    }

    public function getLehrplan()
    {
        return $this->belongsToMany("App\Lehrplan", "klasse_lehrplan");
    }

    public function schuljahr()
    {
        return $this->belongsTo("App\Schuljahr");
    }

    public function kohorte()
    {
        return $this->belongsTo("App\Kohorte");
    }

    public function lehrplanEinheit()
    {
        return $this->belongsTo("App\LehrplanEinheit");
    }

    public function klassen()
    {
        return $this->belongsToMany("App\Klasse", "klasse_klasse", "cluster_id", "klasse_id");
    }

    public function cluster()
    {
        return $this->belongsToMany("App\Klasse", "klasse_klasse", "klasse_id", "cluster_id");
    }

    const IDENTIFIER = "schoolclass";

    //
    public function loadFromClingo($string)
    {
        // TODO: Implement loadFromClingo() method.
    }

    public function getClingo($withRelationship)
    {
        $string = ClingoConstant::basicClingoTranslate($this::IDENTIFIER, [$this->getClingoID()]);
        $schuler_count = $this->schuler->count();
        if ($withRelationship) {
            foreach ($this->getLehrplan as $lehrplan) {
                foreach ($lehrplan->lehreinheiten() as $einheiten) {
                    $fach = $einheiten->fach;
                    if ($fach != null) {
                        $string .= ClingoConstant::basicClingoTranslate("takes", [$fach->getClingoID(), $this->getClingoID(), $schuler_count]);
                    }
                }
            }
        }
        return $string;
    }

    public function getClingoID()
    {
        return Klasse::IDENTIFIER . "_" . $this->id;
    }

    public function schuler()
    {
        return $this->belongsToMany('App\Schuler')->orderBy('lastname', 'asc');
    }

    public function schulerAktuell()
    {
        $today = date("Y/m/d");
        return $this->schulerAtDatum($today);
    }

    public function schulerAtDatum($date)
    {
        return Schuler::whereIn('id', DB::table('klasse_schuler')->where('klasse_id', $this->id)
            ->join('schulers', 'klasse_schuler.schuler_id', '=', 'schulers.id')
            ->where(function ($query) use ($date) {
                $query->where('klasse_schuler.from', '<=', $date)->orWhereNull('klasse_schuler.from');
            })
            ->where(function ($query) use ($date) {
                $query->where('klasse_schuler.until', '>=', $date)->orWhereNull('klasse_schuler.until');
            })
            ->pluck('schulers.id'));
    }

    public static function findByClingoId($clingo)
    {
        return Klasse::findOrFail(str_replace(Klasse::IDENTIFIER . "_", "", $clingo));
    }

    public function getDisplayNameAttribute()
    {
        return $this->name;
    }

    public function lehrabschnitte()
    {
        return $this->hasMany('App\LessonSection')->orderBy('begin');
    }

    public function lehrabschnittePraxis()
    {
        return $this->hasMany('App\LessonSection')->where('type', '!=', 'theorie')->orderBy('begin');
    }

    public function theorieAbschnitte()
    {
        return $this->hasMany('App\LessonSection')->where('type', '=', 'theorie')->orderBy('begin');
    }

    public function klassenraum()
    {
        return $this->hasOne('App\Raum', 'id', 'raum_id');
    }

    public function dokumente($parent_id = null)
    {
        $ids = DB::table('model_dokument')
            ->where('model_id', '=', $this->id)
            ->where('model_type', '=', 'klasse')
            ->pluck('dokument_id')->toArray();
        if ($parent_id == null) {
            return Dokument::find($ids);
        }
        return Dokument::where('parent_id', '=', 0)->whereIn('id', $ids)->get();
    }

    public function getMerkmal($key, $default = "")
    {
        $setting = Merkmal::where('model_id', '=', $this->id)->where('model_type', '=', 'klasse')->where('key', '=', $key)->first();
        if ($setting == null) {
            return $default;
        }
        return $setting->value;
    }

    public function setMerkmal($key, $value)
    {
        if ($value == null) {
            return;
        }
        $setting = Merkmal::where('model_id', '=', $this->id)->where('model_type', '=', 'klasse')->where('key', '=', $key)->first();
        if ($setting != null) {
            $setting->value = $value;
            $setting->save();
        } else {
            $setting = new Merkmal();
            $setting->model_id = $this->id;
            $setting->model_type = "klasse";
            $setting->key = $key;
            $setting->value = $value;
            $setting->save();
        }
    }

    public function getFormulare()
    {
        $ids = explode(",", $this->getMerkmal("formulare", ""));
        $formulare = Formular::whereIn('id', $ids)->get();
        $arrrray = [];
        foreach ($formulare as $s) {
            $arrrray[] = $s;
        }
        return $arrrray;
    }

    public function exams()
    {
        return $this->hasMany('App\Exam');
    }

    public function notifiyFeed(Dokument $dokument)
    {
        // maybe todo?
    }

    public function checkRights(Dokument $dokument, $cloudid, $type = "view"): bool
    {
        // TODO: Implement checkRights() method.
        return true;
    }


    public function getMembersAttribute()
    {
        return $this->schulerAktuell()->orderBy('lastname')->get();
    }

    public function getSchulerAttribute()
    {
        if ($this->type == "cluster_group") {
            $ids = [];
            foreach ($this->klassen as $klasse) {
                foreach ($klasse->getSchulerAttribute() as $schuler_id) {
                    $ids[] = $schuler_id;
                }
            }
            return Schuler::whereIn("id", $ids)->orderBy('lastname')->pluck("id");
        }

        return $this->schulerAktuell()->orderBy('lastname')->pluck("id");
    }

    public function getAllSchulerAttribute()
    {
        if ($this->type == "cluster_group") {
            $ids = [];
            foreach ($this->klassen as $klasse) {
                foreach ($klasse->getAllSchulerAttribute() as $schuler_id) {
                    $ids[] = $schuler_id;
                }
            }
            return Schuler::whereIn("id", $ids)->orderBy('lastname')->pluck("id");
        }

        return $this->schuler()->orderBy('lastname')->pluck("schulers.id as id");
    }

    public function getLatestFormulaDataFor($formula)
    {
        $final = null;
        foreach ($formula->revisions as $revision) {
            $data = $this->getFormulaDataFor($revision->id);
            if ($data != null) {
                $final = $data;
            }
        }
        if ($final == null) {
            return "[]";
        }
        return $final->formular_data;
    }


    public function getFormulaDataFor($revision)
    {
        return MasterDataExtension::where('model_id', '=', $this->id)->where('model_type', '=', 'schoolclass')->where('formular_revision_id', '=', $revision)->first();
    }

    public function saveFormulaDataFor($revision, $data)
    {
        $dbModel = $this->getFormulaDataFor($revision);
        if ($dbModel == null) {
            $dbModel = new MasterDataExtension;
            $dbModel->model_type = "schoolclass";
            $dbModel->model_id = $this->id;
            $dbModel->formular_revision_id = $revision;
        }
        $dbModel->formular_data = $data;
        $dbModel->save();
    }


    public function module()
    {
        if ($this->type == "planning_group") {
            $fs = $this->fs;
            $lehrplan = $this->getLehrplan()->first();
            if ($lehrplan == null)
                return [];
            $lehrplan_id = $lehrplan->id;
            $relevantLehrplanEinheiten = DB::select("SELECT einheit.module_id as m_id FROM stupla_lehrplan_einheits as einheit, stupla_modul_fach_curiculum as fachRelation WHERE " .
                "einheit.module_id = fachRelation.module_id AND einheit.lehrplan_id = fachRelation.lehrplan_id AND einheit.lehrplan_id = :lehrplan_id AND fachRelation.semester_occurrence = :fs",
                ['lehrplan_id' => $lehrplan_id, "fs" => $fs]);
            return Module::find(array_column($relevantLehrplanEinheiten, 'm_id'))->filter(function ($modul) use ($lehrplan) {
                return !$modul->isSchwerpunkt($lehrplan->id);
            });
        } else if ($this->type == "blocking_group" || $this->type == "special") {
            if ($this->lehrplan_einheit_id == null)
                return collect();

            $fs = $this->fs;
            // Hier alle Lehrplaneinheit, besser alle Module sammeln, die unter dem Knoten fallen, der für die Klasse eingeplant ist
            // load all lehrplan_einheiten recursive
            $lehrplan_einheiten = DB::select("select id,
        name,
        lehrplan_einheit_id,
        module_id
from    (select * from stupla_lehrplan_einheits
         order by 	lehrplan_einheit_id, id) stupla_lehrplan_einheits,
        (select @pv := ?) initialisation
where   find_in_set(lehrplan_einheit_id, @pv) > 0
and     @pv := concat(@pv, ',', id)", [$this->lehrplan_einheit_id]);

          //  Log::debug("Einheiten ".count($lehrplan_einheiten));
          //  print_r($fs);
          //  print_r($lehrplan_einheiten);
            // Filter for fs
            $relevantLehrplanEinheiten = DB::select("SELECT einheit.module_id as m_id FROM stupla_lehrplan_einheits as einheit, stupla_modul_fach_curiculum as fachRelation WHERE " .
                "einheit.module_id = fachRelation.module_id AND einheit.lehrplan_id = fachRelation.lehrplan_id AND einheit.id IN (".
                implode(',', array_fill(0, count($lehrplan_einheiten), '?')).") AND fachRelation.semester_occurrence = ?",
                array_merge(array_column($lehrplan_einheiten, 'id'), [$fs]));


          //  print_r("Planeinheit ".count($relevantLehrplanEinheiten));
            return Module::find(array_column($relevantLehrplanEinheiten, 'm_id'));
        } else if($this->type == "cluster_group")
        {
            $modul_meta_list = [];
            $first = true;
            foreach ($this->klassen as $klasse)
            {
                if($first) {
                    $modul_meta_list = $klasse->module()->pluck("id")->all();
                    $first = false;
                }
                $modul_meta_list = array_intersect($modul_meta_list,$klasse->module()->pluck("id")->all());
            }

            return Module::whereIn("id",$modul_meta_list)->get();

        } else if($this->type == "free_group")
        {
            return collect();
        }

        return collect();
    }

    public function moduleWithOutCluster()
    {
        $module = $this->module()->pluck("id")->all();
       // print_r("Before ".count($module));
        foreach ($this->cluster as $cluster)
        {
            $module_cluster = $cluster->module()->pluck("id")->all();
            $module = array_diff($module, $module_cluster);
        }
     //   print_r("After ".count($module));
        return Module::whereIn("id",$module)->get();
    }


    public function getSubjects()
    {
        $lehrplan = $this->getLehrplan()->first();
        if($lehrplan == null)
            return collect();

        $modul_ids = $this->moduleWithOutCluster()->pluck("id");
        if($this->type == "cluster_group")
        {
            return Fach::find(DB::table("modul_fach_curiculum")
                ->whereIn("module_id",$modul_ids)
                ->where("lehrplan_id",'=',$lehrplan->id)
                ->pluck("fach_id"));
        }
        return Fach::find(DB::table("modul_fach_curiculum")
            ->whereIn("module_id",$modul_ids)
            ->where("lehrplan_id",'=',$lehrplan->id)
            ->where("semester_occurrence",'=',$this->fs)
            ->pluck("fach_id"));
    }

    public function getStudiengangAttribute()
    {
        if($this->type == "free_group")
        {
            return [];
        }
        if($this->type == "planning_group")
        {
            return [$this->kohorte->studium];
        }
        if($this->type == "special" || $this->type == "blocking_group")
        {
            $einheitsKnoten = LehrplanEinheit::find($this->lehrplan_einheit_id);
            if($einheitsKnoten == null)
                return [];
            return [$einheitsKnoten->lehrplan->studiumRelation];
        }
        if($this->type == "cluster_group")
        {
            $all_studiengang = [];
            foreach ($this->klassen as $klasse)
            {
                $all_studiengang = array_merge($all_studiengang,$klasse->getStudiengangAttribute());
            }
            return $all_studiengang;
        }
        return [];
    }

    public function getAllFSAttribute ()
    {
        if($this->type == "cluster_group")
        {
            $all_fs = [];
            foreach($this->klassen as $klasse) {
                $all_fs[] = $klasse->fs;
            }
            return $all_fs;
        }
        return [$this->fs];
    }

    public function getAllTypesAttribute()
    {
        if($this->type == "cluster_group")
        {
            $all_types = [];
            $all_types[] = "cluster_group"; // add manually cluster_group
            foreach($this->klassen as $klasse) {
                $all_types[] = $klasse->type;
            }
            return $all_types;
        }
        return [$this->type];
    }

    public function getEmails()
    {
        $members = $this->schuler()->get();
        $emails = [];
        foreach ($members as $member)
            $emails = array_merge($emails, $member->getEmails());
        return $emails;
    }

    public function delete()
    {
         //TODO the rest

        $hentries = DB::table("contact_history_entries_models")->where(["model_type" => ContactHistoryEntry::$MODEL_TYPE_COURSE, "model_id" => $this->id]);
        $counts["contact_history_entries_models"] = $hentries->count();
        $hentries->delete();

        return parent::delete();
    }
}
