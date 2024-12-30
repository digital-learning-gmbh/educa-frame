<?php

namespace App;

use App\Exceptions\ModalTransactionException;
use App\Exceptions\ModalTransactionExceptionSchuljahrNotFoundException;
use App\Exceptions\ModalTransactionExceptionStandortNotFoundException;
use App\Providers\AppServiceProvider;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Schuler extends Model implements HasContactHistory, HasDocuments
{
    use SoftDeletes;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function klassen()
    {
        return $this->belongsToMany('App\Klasse');
    }

    public function klassenNurZukunft()
    {
        $stichtag = date("Y/m/d");
        $relation = DB::table('klasse_schuler')->where('schuler_id', '=', $this->id)
            ->where(function ($query) use($stichtag) {
                $query->where('until', '>', $stichtag)->orWhere('until', '=', null);
            })->pluck("klasse_id");
        return Klasse::find($relation);
    }

    public function klassenGroupBy()
    {
        return $this->belongsToMany('App\Klasse')->orderBy('schuljahr_id')->get();
    }

    public function schuljahre()
    {
        $klassen = $this->klassenGroupBy();
        $schuljahre = [];
        foreach ($klassen as $klasse)
        {
            $schuljahre[] = $klasse->schuljahr;
        }
        return $schuljahre;
    }

    public function klasseForSchuljahr($year)
    {
        return $this->belongsToMany('App\Klasse')->where('schuljahr_id', '=', $year)->get();
    }

    public function klassenRelationObjects()
    {
        return DB::table('klasse_schuler')->where('schuler_id', '=', $this->id)->orderBy('from')->get();
    }

    public function klassenAktuell()
    {
        $today = date("Y/m/d");
        return $this->klassenInArea($today);
    }

    public function klassenInArea($stichtag)
    {
        $relation = DB::table('klasse_schuler')->where('schuler_id', '=', $this->id)
            ->where(function ($query) use($stichtag) {
                $query->where('from', '<', $stichtag)->orWhere('from', '=', null);
                })
            ->where(function ($query) use($stichtag) {
                $query->where('until', '>', $stichtag)->orWhere('until', '=', null);
                })->pluck("klasse_id");
        return Klasse::find($relation);
    }

    public function getDisplayNameAttribute()
    {
        return $this->firstname." ".$this->lastname;
    }

    public function getVonBisInKlasse($klasse_id)
    {
        $relation = DB::table('klasse_schuler')->where('klasse_id', '=', $klasse_id)->where('schuler_id', '=', $this->id)->select(["from", "until"])->first();
        return $relation;
    }

    public function getFormatedVonBisInKlasse($klasse_id)
    {
        $relation = $this->getVonBisInKlasse($klasse_id);
        if ($relation->until != null) {
            $tmp =new \DateTime($relation->from);
            $tmp2 =new \DateTime($relation->until);
            return $tmp->format('d.m.Y') . " - " . $tmp2->format('d.m.Y');
        }
        return "";
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

    public function addinfo()
    {
        return $this->hasOne("App\AdditionalInfo","id","info_id");
    }

    public function klassenbuchTeilnahme()
    {
        return $this->hasMany('App\KlassenbuchTeilnahme');
    }

    public function klassenbuchVorgemerkt()
    {
        return $this->hasMany('App\KlassenbuchTeilnahmeVorgemerkt')->where("schuler_id", "=", $this->id)->get();
    }

    public function note()
    {
        return $this->hasMany('App\Note');
    }
    public function noteForSchuljahr($schuljahr_id, $isSchriftlich = null)
    {
        if($isSchriftlich == null){
            return $this->hasMany('App\Note')->where('schuljahr_id','=', $schuljahr_id)->orderBy('fach_id', 'asc')->get();

        }else{
            return $this->hasMany('App\Note')->where([
                ['schuljahr_id','=', $schuljahr_id],
                ['typ', '=', $isSchriftlich]
            ])->orderBy('fach_id', 'desc')->get();
        }

    }

    public function getSchuleAttribute()
    {
        return $this->schulen()->first();
    }

    public function schulen()
    {
        return $this->belongsToMany('App\Schule',"schuler_schule");
    }
    public function kenntnisse()
    {
        return $this->hasMany('App\Kenntniss');
    }

    public function dokumente($parent_id = null)
    {
        $ids = DB::table('model_dokument')
            ->where('model_id', '=', $this->id)
            ->where('model_type', '=', 'student')
            ->pluck('dokument_id')->toArray();
        if($parent_id == null)
        {
            return Dokument::find($ids);
        }
        return Dokument::where('parent_id', '=', '0')->whereIn('id', $ids)->get();
    }

    /*
    // merkmal
    public function getMerkmal($key, $default = "")
    {
        $setting = Merkmal::where('model_id', '=', $this->id)->where('model_type','=','schuler')->where('key', '=', $key)->first();
        if($setting == null)
        {
            return $default;
        }
        return $setting->value;
    }

    public function setMerkmal($key, $value)
    {
        if($value == null)
        {
            return;
        }
        $setting = Merkmal::where('model_id', '=', $this->id)->where('model_type','=','schuler')->where('key', '=', $key)->first();
        if($setting != null)
        {
            $setting->value = $value;
            $setting->save();
        } else {
            $setting = new Merkmal();
            $setting->model_id = $this->id;
            $setting->model_type = "schuler";
            $setting->key = $key;
            $setting->value = $value;
            $setting->save();
        }
    }

    public function deleteMerkmal($key)
    {
        $setting = Merkmal::where('model_id', '=', $this->id)->where('model_type','=','schuler')->where('key', '=', $key)->first();
        if($setting != null)
        {
            $setting->delete();
        }
    }
*/
    public function praxisEinsatze($abschnitt)
    {
        return PraxisEinsatz::where('schuler_id','=', $this->id)->where('lesson_section_id','=', $abschnitt->id)->get();
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
       return MasterDataExtension::where('model_id','=',$this->id)->where('model_type','=', 'schuler')->where('formular_revision_id','=', $revision)->first();
    }

    public function saveFormulaDataFor($revision, $data)
    {
        $dbModel = $this->getFormulaDataFor($revision);
        if($dbModel == null)
        {
            $dbModel = new MasterDataExtension;
            $dbModel->model_type = "schuler";
            $dbModel->model_id = $this->id;
            $dbModel->formular_revision_id = $revision;
        }
        $dbModel->formular_data = $data;
        $dbModel->save();
    }

    public function setNote($exam, $skala, $note_angabe)
    {
        $note = $this->getNote($exam,$skala);
        if($note == null)
        {
            $note = new Note;
            $note->schuler_id = $this->id;
            $note->exam_id = $exam->id;
            $note->rating_key = $skala->id;
            $note->schuljahr_id = AppServiceProvider::getSchoolYear()->id;

        }
        $note->note = $note_angabe;
        $note->datum = $exam->date;
        $note->save();

    }

    public function clearNote($exam, $skala)
    {
        Note::where('schuler_id','=', $this->id)
            ->where('exam_id','=',$exam->id)
            ->where('rating_key','=',$skala->id)
            ->delete();
    }

    public function getNote($exam, $skala)
    {
        return Note::where('schuler_id', '=', $this->id)
            ->where('exam_id', '=', $exam->id)
            ->where('rating_key', '=', $skala->id)
            ->first();
    }

    public function getCloudID()
    {
        $relation = DB::table('model_cloud_id')->where('appName','LIKE','student')->where('loginId','=',$this->id)->first();
        if($relation != null)
        {
            return CloudID::find($relation->cloud_i_d_id);
        }
        return null;
    }

    public function getEmails()
    {
        $addInfo = $this->getAddInfo();
        if($addInfo->email != null)
            return [$addInfo->email];

        if($addInfo->email_private != null)
            return [$addInfo->email_private];

        if($addInfo->email_other != null)
            return [$addInfo->email_other];

        return [];
    }

    // USE THIS FUNCTION TO CHANGE THE USER; HOUSTON WE HAVE A CACHE
    public function widerruf($date, $user)
    {
        $schuljahr = $this->getFirstStudyInformation()->schuljahr;

        DB::table('study_progress_entries')->where([
            'schuler_id' => $this->id
        ])->delete();

        DB::table('study_progress_entries')->insert([
            'schuljahr_id' => $schuljahr->id,
            'schuler_id' => $this->id,
            'kohorte_id' => null,
            'status' => 'canceled',
            'fs' => 0,
            'hs' => 0,
            'changed_by' => 'geändert von '.$user->name,
            'notes' => 'Studium wiederrufen',
            'study_attributes' => json_encode([
                "date_of_cancel" => $date
            ]),
        ]);

        DB::table("klasse_schuler")->where("schuler_id","=",$this->id)->delete();
    }

    // Die Kohorte gibt hier alles vor:
    // Studiengang, erstes Semester
    public function immaToStudiengang(Kohorte $kohorte, $hs = 1, $fs = 1, $fachrichtung = [], $klasse = null)
    {
        $ids = [];
        $curriculm = $kohorte->lehrplan;
        $firstSchuljahr = $kohorte->schuljahr;
        $this->schulen()->sync([$kohorte->schuljahr->schule->id]);
        if($klasse != null) {
            $schuljahrKlasse = $klasse->schuljahr;
            DB::table("klasse_schuler")->insert([
                "klasse_id" => $klasse->id,
                "schuler_id" => $this->id,
                "from" => $schuljahrKlasse->start,
                "until" => $schuljahrKlasse->ende
            ]);
        }

        $schuljahre = Schuljahr::where('schule_id','=',$firstSchuljahr->schule_id)->where('year','>=',$firstSchuljahr->year)->orderBy('year','ASC')->get();
        for ($i = 0; $i < $curriculm->studiumRelation->normal_period; $i++)
        {
            if($schuljahre->count() <= $i)
                break;

            DB::table('study_progress_entries')->where([
                'schuljahr_id' => $schuljahre[$i]->id,
                'schuler_id' => $this->id
            ])->delete();

            $attributes = [];
            if($fachrichtung != null && is_array($fachrichtung))
            {
                $attributes = [ "direction_of_study" => $fachrichtung ];
            }

            if($i == 0)
            {
                $attributes = array_merge($attributes,[ "date_of_imma" =>  $schuljahre[$i]->start]);
            }

            if($i+1 == $curriculm->studiumRelation->normal_period)
            {
                $attributes =  array_merge($attributes,[ "date_of_exmatrikulation" => $schuljahre[$i]->ende]);
            }

            $ids[] = DB::table('study_progress_entries')->insertGetId([
                'schuljahr_id' => $schuljahre[$i]->id,
                'schuler_id' => $this->id,
                'kohorte_id' => $kohorte->id,
                'status' => $hs == 1 ? 'freshmen' : ($i == 0 ? 'newcomer' : 're-registrants'),
                'fs' => $fs,
                'hs' => $hs,
                'changed_by' => 'Automatisch erstellt',
                'study_attributes' => json_encode($attributes)
            ]);
            $fs++;
            $hs++;
        }

        $this->guest_student = "none";
        $this->save();

        return $ids;
    }

    public function changeStudyDirection($fachrichtung)
    {
        $studyEntries = $this->studyInformation();
        foreach ($studyEntries as $studyEntry)
        {
            $attribute = json_decode($studyEntry->study_attributes);
            if($attribute == null)
                $attribute =  new \stdClass();
            if($fachrichtung == null)
            {
                unset($attribute->direction_of_study);
            } else {
                $attribute->direction_of_study = $fachrichtung;
            }
            $studyEntry->study_attributes = json_encode($attribute);
            $studyEntry->save();
        }
    }

    public function getDirectionOfStudy($year_id)
    {
        $studyInformation = $this->getCurrentStudyInformation($year_id);
        if($studyInformation != null ) {
            return $studyInformation->getDirectionOfStudyAttribute();
        }
        return [];
    }

    public function changeStudientage(Schuljahr $firstSchuljahr, $days)
    {
        if($firstSchuljahr == null)
            throw new \Exception("Invalid state, the first schoolyear is null");
        $currentSchuljahr = $firstSchuljahr;
        while ($currentSchuljahr != null)
        {
            $originalEntry = $this->getCurrentStudyInformation($currentSchuljahr->id);
            if($originalEntry != null) {
                // Löschen der alten Planungsgruppe
                DB::table("klasse_schuler")->where(["klasse_id" => $originalEntry->klasse_id,"schuler_id" => $this->id])->delete();

                // search Planungsgruppe
                $klasse = Klasse::where('daysOfWork','=',$days)
                    ->where('type','=','planning_group')
                    ->where('fs', '=', $originalEntry->fs)
                    ->join('klasse_lehrplan','klasse_lehrplan.klasse_id','klasses.id')
                    ->where('klasse_lehrplan.lehrplan_id','=',$originalEntry->kohorte->lehrplan->id)->first();

                // ENDE search Planungsgruppe
                if($klasse != null) {
                    $originalEntry->klasse_id = $klasse->id;
                } else {
                    $originalEntry->klasse_id = null;
                }
                $originalEntry->save();

            }
            $currentSchuljahr = $currentSchuljahr->nextSchuljahr();
        }
    }

    public function getKohorteForDowngrade(Schuljahr $firstSchuljahr, $numberOfDowngrades) {
        $originalEntry = $this->getCurrentStudyInformation($firstSchuljahr->id);
        $schuljahreKohorte = $originalEntry->kohorte->schuljahr;
        for($i = 0; $i < $numberOfDowngrades; $i++)
        {
            if($schuljahreKohorte == null)
            {
                throw new \Exception("Invalid state, the target schoolyear is null");
            }
            $schuljahreKohorte = $schuljahreKohorte->nextSchuljahr();
        }
        $newKohorte = Kohorte::createWithAttributes($originalEntry->kohorte->schule, $originalEntry->kohorte->studium,$schuljahreKohorte);
        return $newKohorte;
    }

    public function downgrading(Schuljahr $firstSchuljahr, $numberOfDowngrades, $reason = null, $klasse = null)
    {
        if($firstSchuljahr == null)
            throw new \Exception("Invalid state, the first schoolyear is null");


        $originalEntry = $this->getCurrentStudyInformation($firstSchuljahr->id);
        // Löschen der alten Planungsgruppe
        DB::table("klasse_schuler")->where(["klasse_id" => $originalEntry->klasse_id,"schuler_id" => $this->id])->delete();
        if($klasse != null)
            DB::table("klasse_schuler")->insert([
                "klasse_id" => $klasse->id,
                "schuler_id" => $this->id,
                "from" => $firstSchuljahr->start,
                "until" => $firstSchuljahr->ende
            ]);

        $schuljahreKohorte = $originalEntry->kohorte->schuljahr;
        for($i = 0; $i < $numberOfDowngrades; $i++)
        {
            if($schuljahreKohorte == null)
            {
                throw new \Exception("Invalid state, the target schoolyear is null");
            }
            $schuljahreKohorte = $schuljahreKohorte->nextSchuljahr();
        }

        $newKohorte = Kohorte::createWithAttributes($originalEntry->kohorte->schule, $originalEntry->kohorte->studium,$schuljahreKohorte);

        $originalEntry = $this->getCurrentStudyInformation($firstSchuljahr->id);
        $originalEntry->fs -= $numberOfDowngrades;
        $originalEntry->kohorte_id = $newKohorte->id;
        $originalEntry->notes = $reason;
        $originalEntry->status = "downgrading";
        if($klasse != null)
            $originalEntry->klasse_id = $klasse->id;
        else
            $originalEntry->klasse_id = null;

        $originalEntry->save();

        // correct the fs
        $attributes = $originalEntry->study_attributes;
        $fs = $originalEntry->fs;
        $hs = $originalEntry->hs;
        $firstSchuljahr = $firstSchuljahr->nextSchuljahr();
        while ($firstSchuljahr != null)
        {
            $originalEntry = $this->getCurrentStudyInformation($firstSchuljahr->id);
            if($originalEntry == null) {
               // print_r("not found".$firstSchuljahr->name);
                break;
            }
            $fs++;
            $hs++;
            $originalEntry->fs = $fs;
            $originalEntry->hs = $hs;
            $originalEntry->kohorte_id = $newKohorte->id;
            $originalEntry->klasse_id = null; // wird neu berechnet über den Cache dann
            $originalEntry->save();
            $attributes = $originalEntry->study_attributes;
            $fs = $originalEntry->fs;
            $hs = $originalEntry->hs;

            $firstSchuljahr = $firstSchuljahr->nextSchuljahr();
        }

        $fs++;
        $hs++;
        // add additional fs
        for($i = 0; $i < $numberOfDowngrades; $i++)
        {
            DB::table('study_progress_entries')->insertGetId([
                'schuljahr_id' => $firstSchuljahr->id,
                'schuler_id' => $this->id,
                'kohorte_id' => $newKohorte->id,
                'status' => 're-registrants',
                'fs' => $fs,
                'hs' => $hs,
                'changed_by' => 'Automatisch verlängert',
                'study_attributes' => $attributes
            ]);
            $fs++;
            $hs++;
            $firstSchuljahr = $firstSchuljahr->nextSchuljahr();
        }
    }

    public function vacation(Schuljahr $schuljahr, $reason = null, $note = null) {
        // Update the semester count
        $standort = $this->schulen->first();
        if($standort == null)
            throw new ModalTransactionExceptionStandortNotFoundException("Invalid state, the student has no school");

        if($schuljahr == null)
            throw new ModalTransactionExceptionSchuljahrNotFoundException("Invalid state, the final schoolyear for this date does not exists");

        $originalEntry = $this->getCurrentStudyInformation($schuljahr->id);

        if($originalEntry == null)
            throw new ModalTransactionExceptionSchuljahrNotFoundException("Invalid state, this schoolyear there is no study... ");

        $originalEntry->fs = $originalEntry->fs-1;
        // $originalEntry->hs = $originalEntry->hs; // bleibt so wie es ist
        //$originalEntry->kohorte_id = null;
        $originalEntry->status = "vacation";
        $originalEntry->notes = $note;
        $tempOriginalEntry = json_decode($originalEntry->study_attributes);
        if($reason != null)
            $originalEntry->study_attributes = json_encode(array_merge([ "reason" => $reason], json_decode($originalEntry->study_attributes, TRUE)));
        $originalEntry->save();

        $currentSchuljahr = $schuljahr->nextSchuljahr();

        while ($currentSchuljahr != null)
        {
            $originalEntry = $this->getCurrentStudyInformation($currentSchuljahr->id);
            if($originalEntry != null) {
                $originalEntry->fs = $originalEntry->fs - 1;
                $originalEntry->save();
            }
            $currentSchuljahr = $currentSchuljahr->nextSchuljahr();
        }

        $lastInfo = $this->getLastStudyInformation();
        $currentSchuljahr = $lastInfo->schuljahr->nextSchuljahr();

        if($currentSchuljahr != null) {
            DB::table('study_progress_entries')->insert([
                'schuljahr_id' => $currentSchuljahr->id,
                'schuler_id' => $this->id,
                'kohorte_id' => $lastInfo->kohorte->id,
                'status' => 're-registrants',
                'fs' => $lastInfo->fs + 1,
                'hs' => $lastInfo->hs + 1,
                'changed_by' => 'Automatisch erstellt, da Semester beurlaubt',
                'study_attributes' => json_encode($tempOriginalEntry)
            ]);
        }
    }

    public function changeStudienort(Schuljahr $firstSchuljahr, Schule $standort)
    {
        if($standort == null)
            throw new ModalTransactionExceptionStandortNotFoundException("Invalid state, the student has no school");

        $studyInformation = $this->getCurrentStudyInformation($firstSchuljahr->id);
        if($studyInformation == null)
            throw new ModalTransactionException("The studyInformation for this schoolyear is empty");

        $newSchuljahr = Schuljahr::where('name','=',$firstSchuljahr->name)->where('schule_id','=',$standort->id)->first();
        if($newSchuljahr == null)
            throw new ModalTransactionException("The schoolyear does not exists on the other school");

        $info = $this->getCurrentStudyInformation($firstSchuljahr->id);
        $newKohorte = Kohorte::where('schule_id','=',$standort->id)->where('lehrplan_id','=',$info->kohorte->lehrplan->id)->first();

        $studyInformation->schuljahr_id = $newSchuljahr->id;
        $studyInformation->kohorte_id = $newKohorte->id;
        $studyInformation->notes = "Standort gewechselt";
        $studyInformation->save();
        $this->schulen()->sync([$newKohorte->schuljahr->schule->id]);

        $firstSchuljahr = $firstSchuljahr->nextSchuljahr();
        $newSchuljahr = $newSchuljahr->nextSchuljahr();
        while ($firstSchuljahr != null)
        {
            $studyInformation = $this->getCurrentStudyInformation($firstSchuljahr->id);
            if($studyInformation != null && $newSchuljahr != null)
            {
                $studyInformation->schuljahr_id = $newSchuljahr->id;
                $studyInformation->kohorte_id = $newKohorte->id;
                $studyInformation->save();
            }

            $newSchuljahr = $newSchuljahr->nextSchuljahr();
            $firstSchuljahr = $firstSchuljahr->nextSchuljahr();
        }

    }

    public function changeStudiengang(Kohorte $kohorte, Schuljahr $firstSchuljahr, $fachrichtung = [])
    {
        $attributes = [];
        if($fachrichtung != null && is_array($fachrichtung))
        {
            $attributes = [ "direction_of_study" => $fachrichtung ];
        }
        $prevSchuljahr = $firstSchuljahr->previousSchuljahr();
        $studyInformationLast = $this->getCurrentStudyInformation($prevSchuljahr->id);
        if($studyInformationLast != null) {
            $studyInformationLast->status = "canceled";
            $studyInformationLast->save();
        }

        $studyInformation = $this->getCurrentStudyInformation($firstSchuljahr->id);
        $i = 0;
        $fs = 1;
        $hs = 1;
        while ($studyInformation != null) {

            // set the new kohorte and therefore the studiengang
            $studyInformation->kohorte_id = $kohorte->id;
            $studyInformation->status = $i == 0 ? "newcomer" : "re-registrants";
            $studyInformation->changed_by = "Studiengang gewechselt";
            $studyInformation->fs = $fs;
            $hs = $studyInformation->hs;
            $studyInformation->study_attributes = json_encode($attributes);
            $studyInformation->save();

            $firstSchuljahr = $firstSchuljahr->nextSchuljahr();
            if ($firstSchuljahr != null) {
                $studyInformation = $this->getCurrentStudyInformation($firstSchuljahr->id);
            } else {
                $studyInformation = null;
                break;
            }
            $i++;
            $fs++;
        }
        $curriculm = $kohorte->lehrplan;
        // should extend
        $fs++;
        $hs++;
        while ($i <= $curriculm->studiumRelation->normal_period && $firstSchuljahr != null) {

            DB::table('study_progress_entries')->insert([
                'schuljahr_id' => $firstSchuljahr->id,
                'schuler_id' => $this->id,
                'kohorte_id' => $kohorte->id,
                'status' => 're-registrants',
                'fs' => $fs,
                'hs' => $hs,
                'study_attributes' => json_encode($attributes)
            ]);

            $firstSchuljahr = $firstSchuljahr->nextSchuljahr();
            $i++;
            $hs++;
            $fs++;
        }
    }

    public function extendStudiengang(Carbon $extendUntil, Carbon $lastDateOfExam)
    {
        // In den Attributen speichern wir dann die Verlängerung bis , Letzter Prüfungstag und die Module, die noch gemacht werden müssen
        $attributes = [ "extendUntil" => $extendUntil, "lastDateOfExam" => $lastDateOfExam];
        $standort = $this->schulen->first();
        if($standort == null)
            throw new ModalTransactionExceptionStandortNotFoundException("Invalid state, the student has no school");

        $finalesSchuljahr = Schuljahr::findForDate($standort->id,$extendUntil);
        if($finalesSchuljahr == null)
           throw new ModalTransactionExceptionSchuljahrNotFoundException("Invalid state, the final schoolyear for this date does not exists");

        $lastStudyInformation = $this->getLastStudyInformation();
        $hs = $lastStudyInformation->hs;
        $fs = $lastStudyInformation->fs;
        $lastSchuljahr = $lastStudyInformation->schuljahr;

        if($lastStudyInformation != null)
            $attributes = array_merge($attributes, json_decode($lastStudyInformation->study_attributes, true));

        // insert for the between
        $schuljahres = Schuljahr::where('schule_id','=',$standort->id)->where('start','>=', $lastSchuljahr->ende)->where('ende','<=',$finalesSchuljahr->ende)->get();
        // print_r("Extend for ". $schuljahres->count());
        foreach ($schuljahres as $schuljahre)
        {
            $fs++;
            $hs++;

            DB::table('study_progress_entries')->insert([
                'schuljahr_id' => $schuljahre->id,
                'schuler_id' => $this->id,
                'kohorte_id' => $lastStudyInformation->kohorte_id,
                'status' => 'extended',
                'fs' => $fs,
                'hs' => $hs,
                'changed_by' => 'Studiengang verlängert',
                'study_attributes' => json_encode($attributes)
            ]);
        }

        // done
    }

    public function cancelStudiengang($reason, Carbon $date, $notes)
    {
        $attributes = [ "cancel_date" => $date, "reason" => $reason];
        $standort = $this->schulen->first();
        if($standort == null)
            throw new ModalTransactionExceptionStandortNotFoundException("Invalid state, the student has no school");

        $finalesSchuljahr = Schuljahr::findForDate($standort->id,$date);
        if($finalesSchuljahr == null)
            throw new ModalTransactionExceptionSchuljahrNotFoundException("Invalid state, the final schoolyear for this date does not exists");

        // delete for schuljahre, that ends after the date
        $schuljahres = Schuljahr::where('start','>=',$date)->get();
        foreach ($schuljahres as $schuljahre)
        {
            DB::table('study_progress_entries')->where([
                'schuljahr_id' => $schuljahre->id,
                'schuler_id' => $this->id,
            ])->delete();
        }

        $lastStudyInformation = $this->getLastStudyInformation();


        DB::table("klasse_schuler")->where("schuler_id","=",$this->id)->delete();

        if($lastStudyInformation != null)
        {
            $finalesSchuljahr = $lastStudyInformation->schuljahr;
            DB::table('study_progress_entries')->where([
                'id' => $lastStudyInformation->id,
                'schuler_id' => $this->id,
            ])->delete();
            $attributes = array_merge((array)json_decode($lastStudyInformation->study_attributes),$attributes);
        }


        $lastStudyInformation2 = $this->getLastStudyInformation();

        // insert information for the last date
        DB::table('study_progress_entries')->insert([
            'schuljahr_id' => $finalesSchuljahr->id,
            'schuler_id' => $this->id,
            'kohorte_id' => $lastStudyInformation != null ? $lastStudyInformation->kohorte_id : null,
            'status' => 'canceled',
            'fs' => $lastStudyInformation2 != null ? $lastStudyInformation2->fs +1 : 1,
            'hs' => $lastStudyInformation2 != null ? $lastStudyInformation2->hs +1 : 1,
            'changed_by' => 'Studiengang abgebrochen',
            'notes' => $notes,
            'study_attributes' => json_encode($attributes)
        ]);
    }

    public function finishStudium(Carbon $date)
    {
        $attributes = [ "finish_date" => $date];
        $standort = $this->schulen->first();
        if($standort == null)
            throw new ModalTransactionExceptionStandortNotFoundException("Invalid state, the student has no school");

        $finalesSchuljahr = Schuljahr::findForDate($standort->id,$date);
        if($finalesSchuljahr == null)
            throw new ModalTransactionExceptionSchuljahrNotFoundException("Invalid state, the final schoolyear for this date does not exists");

        if(!DB::table('study_progress_entries')->where([
            'schuljahr_id' => $finalesSchuljahr->id,
            'schuler_id' => $this->id,
        ])->exists())
        {
            throw new ModalTransactionException("Invalid state, the final date of the studium is not in the studium progress");
        }


        $lastStudyInformation = $this->getCurrentStudyInformation($finalesSchuljahr->id);

        if($lastStudyInformation != null)
            $attributes = array_merge($attributes, json_decode($lastStudyInformation->study_attributes, true));

        DB::table('study_progress_entries')->where([
            'schuljahr_id' => $finalesSchuljahr->id,
            'schuler_id' => $this->id,
        ])->update([
            'status' => 'finish',
            'notes' => 'Studium wurde beendet',
            'study_attributes' => json_encode($attributes)
        ]);

        // alles zukünftige löschen
        $schuljahr = $finalesSchuljahr->nextSchuljahr();
        while ($schuljahr != null)
        {
            DB::table('study_progress_entries')->where([
                'schuljahr_id' => $schuljahr->id,
                'schuler_id' => $this->id,
            ])->delete();
            $schuljahr = $schuljahr->nextSchuljahr();
        }
    }

    public function changeFromGastStudent(Carbon $dateToChange)
    {
        $standort = $this->schulen->first();
        if($standort == null)
            throw new ModalTransactionExceptionStandortNotFoundException("Invalid state, the student has no school");
        $finalesSchuljahr = Schuljahr::findForDate($standort->id,$dateToChange);
        if($finalesSchuljahr == null)
            throw new ModalTransactionExceptionSchuljahrNotFoundException("Invalid state, the final schoolyear for this date does not exists");

        while ($finalesSchuljahr != null)
        {
            $originalEntry = $this->getCurrentStudyInformation($finalesSchuljahr->id);
            if($originalEntry != null) {
                $originalEntry->status = $originalEntry->fs == 1 ? "freshmen" : "re-registrants";
                $originalEntry->save();
            }

            $finalesSchuljahr = $finalesSchuljahr->nextSchuljahr();
        }

        $this->guest_student = "none";
        $this->save();
    }

    public function immaAsGuestStudent(Kohorte $kohorte)
    {
        $ids = $this->immaToStudiengang($kohorte);

        foreach ($ids as $id)
        {
            $studyInfo = StudyProgressEntry::find($id);
            $studyInfo->status = "guest";
            $studyInfo->save();
        }
        $this->guest_student = "guest";
        $this->save();
    }

    // END OF THIS USE FUNCTIONS

    // HELPER FUNCTION

    public function getFirstKohorteAttribute()
    {
        if($this->getFirstStudyInformation())
            return $this->getFirstStudyInformation()->kohorte();
        return null;
    }

    public function getLastKohorteAttribute()
    {
        return $this->getLastStudyInformation()->kohorte();
    }

    public function getKohorteForSchuljahr($schuljahr_id)
    {
        return $this->kohorten()->where('study_progress_entries.schuljahr_id','=', $schuljahr_id)->first();
    }

    public function getCurrentStudyInformation($schuljahr_id)
    {
        return StudyProgressEntry::where('schuler_id','=',$this->id)->where('schuljahr_id','=',$schuljahr_id)->first();
    }

    public function getDateStudyInformation(Carbon $date)
    {
        $schuljahrIds = Schuljahr::where("start","<=",$date)->where("ende",">=",$date)->pluck("id");
        return StudyProgressEntry::where('schuler_id','=',$this->id)->whereIn('schuljahr_id',$schuljahrIds)->first();
    }

    public function getLastStudyInformation()
    {
        return StudyProgressEntry::select('study_progress_entries.*')->where('schuler_id','=',$this->id)
            ->join('schuljahrs', 'schuljahrs.id', '=', 'study_progress_entries.schuljahr_id')->orderBy('schuljahrs.year','DESC')->first();
    }

    public function getFirstStudyInformation()
    {
        return StudyProgressEntry::select('study_progress_entries.*')->where('schuler_id','=',$this->id)
            ->join('schuljahrs', 'schuljahrs.id', '=', 'study_progress_entries.schuljahr_id')->orderBy('schuljahrs.year','ASC')->first();
    }

    public function studyInformation()
    {
        return StudyProgressEntry::select('study_progress_entries.*')->where('schuler_id','=',$this->id)
            ->join('schuljahrs', 'study_progress_entries.schuljahr_id', '=', 'schuljahrs.id')->orderBy('schuljahrs.year','ASC')->get();
    }

    public function kohorten()
    {
        return $this->belongsToMany('App\Kohorte', 'study_progress_entries', 'schuler_id', 'kohorte_id');
    }

    public function ansprechpartner()
    {
        return $this->belongsTo('App\Kontakt','kontakt_id');
    }

    public function company()
    {
        return $this->belongsTo('App\Kontakt','company_id');
    }

    public function notifiyFeed(Dokument $dokument)
    {
        // ignore
    }

    public function checkRights(Dokument $dokument, $cloudid, $type = "view"): bool
    {
        return true;
    }

    public function calculateOpenModuls(StudyProgressEntry $studyInformation, $lehrplan_id, $fs)
    {
        $relevantLehrplanEinheiten = DB::select("SELECT einheit.module_id as m_id FROM stupla_lehrplan_einheits as einheit, stupla_modul_fach_curiculum as fachRelation WHERE " .
        "einheit.module_id = fachRelation.module_id AND einheit.lehrplan_id = fachRelation.lehrplan_id AND einheit.lehrplan_id = :lehrplan_id AND fachRelation.semester_occurrence <= :fs",
            ['lehrplan_id' => $lehrplan_id,  "fs" => $fs]);

        //print_r(array_column($relevantLehrplanEinheiten, 'm_id'));
        // TODO alle Module abziehen, wo er schon eine Prüfung gemacht hat

        return Module::find(array_column($relevantLehrplanEinheiten, 'm_id'));
   }

   public function calculateOpenExamParts(Module $modul, $lehrplan_id, $fs, ModulExam $modulExam)
   {
       $relevantModulExamParts = DB::select("SELECT part_exam.id as part_exam_id FROM".
           " stupla_modul_fach_curiculum as fachRelation, stupla_modul_part_exam_fach as examFach, stupla_modul_part_exams as part_exam ".
           "WHERE examFach.fach_id = fachRelation.fach_id AND fachRelation.lehrplan_id = :lehrplan_id ".
           "AND fachRelation.semester_occurrence <= :fs AND fachRelation.module_id = :modul_id AND ".
           "examFach.modul_part_exam_id = part_exam.id AND part_exam.modul_exam_id = :exam_id",
           ['lehrplan_id' => $lehrplan_id,  "fs" => $fs, "modul_id" => $modul->id ,"exam_id" => $modulExam->id]);


       return ModulPartExam::find(array_column($relevantModulExamParts, 'part_exam_id'));
   }

   public function generateMartikelnummer()
   {
       $maxLoops = 100;
       for ($i = 0; $i < $maxLoops; $i++)
       {
           $num =  sprintf("%06d", mt_rand(1, 999999));
           // check if duplicate
           if(!AdditionalInfo::where('personalnummer','=',$num)->exists())
           {
               $addInfo = $this->getAddInfo();
               $addInfo->personalnummer = $num;
               $addInfo->save();
           }
       }
   }

    public function getLehrplanGroups($lehrplanIds)
    {
        $ids = DB::table('lehrplan_groups_schuler')->where('schuler_id','=', $this->id)->pluck("lehrplan_groups_id");
        return LehrplanGroups::whereIn('lehrplan_id',$lehrplanIds)->whereIn('id',$ids)->get();
    }

    public function getTemplateJSON()
    {
        $addInfo = $this->getAddInfo();
        foreach($addInfo->toArray() as $key=>$value)
        {
            $this->$key = $value;
        }

        $this->praxispartner = "Kein Praxispartner";
        $praxisPartner = $this->getMerkmal("praxisPartner", "-1");
        $kontakt = Kontakt::find($praxisPartner);
        if ($praxisPartner != "-1" && $kontakt != null) {
            $this->praxispartner = $kontakt->name;
        }


        $formulare=[];
        $formular_templates=[];
        foreach($this->schulen()->get() as $schule) {
            foreach ($schule->formulare()->get() as $formular) {
                $formulare[$formular->id] = json_decode($this->getLatestFormulaDataFor($formular));
                $formular_templates[$formular->id] = json_decode($formular->getLastRevisionAttribute()->data);
            }
        }
        return  $this;
        }

        public function delete()
        {
            return DB::transaction( function (){
                $counts = [];

                if($this->getCloudID())
                    $this->getCloudID()->delete();

                // subjects
                $classes = $this->klassen();
                $counts["classes"] = $classes->count();
                $classes->sync([]);

                // schools
                $schools = $this->schulen();
                $counts["schools"] = $schools->count();
                $schools->sync([]);

                /* deleted at classes
                //school years
                $schoolyears = $this->schuljahre();
                $counts["schoolyears"] = count($schoolyears);
                $schoolyears->sync([]);
*/
                //documents
                $docs = $this->dokumente();
                $counts["documents"] = $docs->count();
                $docs->each->delete();

                //practical duties
                $practicalDuties = PraxisEinsatz::where('schuler_id','=', $this->id);
                $counts["practicalDuties"] = $practicalDuties->count();
                $practicalDuties->delete();

                //grades
                $grades = $this->note();
                $counts["grades"] = $this->note->count();
                $grades->delete();

                //documents
                $docs = $this->dokumente();
                $counts["documents"] = $docs->count();
                $docs->each->delete();

                //classbookAttendance
                $cba = $this->klassenbuchTeilnahme();
                $counts["classbookAttendance"] = $cba->count();
                $cba->delete();

                //knowledge
                $know = $this->kenntnisse();
                $counts["knowledge"] = $know->count();
                $know->delete();


                $entries = DB::table('study_progress_entries')->where([
                    'schuler_id' => $this->id
                ]);
                $counts["progress_entries"] = $entries->count();
                $entries->delete();


            //cohorts
            $cohorts = $this->kohorten();
            $counts["cohorts"] = $this->kohorten->count();
            $cohorts->sync([]);


            //ansprechpartner
            $contact = $this->ansprechpartner();
            $counts["contact"] = !!$contact;

            //company
            $company = $this->note();
            $counts["company"] = !!$company;

                //additional info
                if ($this->info_id > 0) {
                    $iId = $this->info_id;
                    $this->info_id = null;
                    $this->save();
                    AdditionalInfo::findOrFail($iId)->delete();
                }

                //history entries
                $hentries = DB::table("contact_history_entries_models")->where(["model_type" => ContactHistoryEntry::$MODEL_TYPE_STUDENT, "model_id" => $this->id]);
                $counts["contact_history_entries_models"] = $hentries->count();
                $hentries->delete();

                parent::delete();
                return $counts;
            });
        }

}
