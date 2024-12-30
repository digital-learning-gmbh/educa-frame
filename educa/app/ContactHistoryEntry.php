<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Mixed_;

class ContactHistoryEntry extends Model
{
    protected $appends = ["sender_names", "receiver_names"];
    protected $casts = ["additional_receivers" => "array"];

    public static string $MODEL_TYPE_STUDENT = "student";
    public static string $MODEL_TYPE_TEACHER = "teacher";
    public static string $MODEL_TYPE_CONTACT = "contact";
    public static string $MODEL_TYPE_EMPLOYEE = "employee";
    public static string $MODEL_TYPE_COURSE = "schoolclass";


    private function getModelRelations()
    {
        return DB::table("contact_history_entries_models")->where(["che_id" => $this->id]);
    }

    /**
     * @param $subject string required
     * @param $content  string required
     * @param $additional_receivers required. null or String-Array of email addresses
     * @param $senderMap array Mapping [modelType => modelIds]
     * @param $receiverMap array Mapping [modelType => modelIds]
     * @param $time Carbon
     * @param $type string
     * @param $isFromAdministration bool
     * @return ContactHistoryEntry
     */
    public static function new(
        string $subject,
        string $content,
        $additional_receivers,
        array $senderMap,
        array $receiverMap,
        Carbon $time = null,
        string $type = "email",
        bool $isFromAdministration = true) : ContactHistoryEntry
    {
            if($time == null)
                $time = Carbon::now();

            $elRodrigoCorrespondez = new ContactHistoryEntry;
            $elRodrigoCorrespondez->isFromAdministration = $isFromAdministration;
            $elRodrigoCorrespondez->unique_id = uniqid("msg-", true);
            $elRodrigoCorrespondez->time = $time;
            $elRodrigoCorrespondez->type = $type;
            $elRodrigoCorrespondez->subject = $subject;
            $elRodrigoCorrespondez->content = $content;
            $elRodrigoCorrespondez->additional_receivers = $additional_receivers;

            $elRodrigoCorrespondez->save();

            foreach($senderMap as $modelType => $modelIds)
                foreach ($modelIds as $modelId)
                    $elRodrigoCorrespondez->addSender($modelType, $modelId);

            foreach($receiverMap as $modelType => $modelIds)
                foreach ($modelIds as $modelId)
                    $elRodrigoCorrespondez->addReceiver($modelType, $modelId);

            return $elRodrigoCorrespondez;
    }

    public static function getByModel($modelType, $modelId )
    {
        return ContactHistoryEntry::select("contact_history_entries.*")
            ->leftJoin("contact_history_entries_models", "contact_history_entries.id", "=", "contact_history_entries_models.che_id")
            ->where([
                "model_type" => $modelType,
                "model_id" => $modelId,
            ])
            ->orderBy('time','DESC')->get();
    }

    public static function getModel($model_type, $model_id)
    {
        switch($model_type)
        {
            case ContactHistoryEntry::$MODEL_TYPE_STUDENT:
                return Schuler::find($model_id);

            case ContactHistoryEntry::$MODEL_TYPE_TEACHER:
                return Lehrer::find($model_id);

            case ContactHistoryEntry::$MODEL_TYPE_CONTACT:
                return Kontakt::find($model_id);

            case ContactHistoryEntry::$MODEL_TYPE_EMPLOYEE:
                return User::find($model_id);

            case ContactHistoryEntry::$MODEL_TYPE_COURSE:
                return Klasse::find($model_id);
        }
        return null;
    }

    public function getModels(): array
    {
        $relations = $this->getModelRelations()->get();
        $models = [];
        $receivers = [];
        $senders = [];
        foreach ($relations as $relation)
        {
            $model = $this->getModel($relation->model_type, $relation->model_id);
            if($model)
            {
                $models[] = $model;
                if($relation->is_sender)
                    $senders[] = $model;
                else
                    $receivers[] = $model;
            }

        }
        return [ "models" => $models, "receivers" => $receivers, "senders" => $senders];
    }

    private function addModel( $model_type, $model_id, $isSender )
    {
        if( $this->getModelRelations()->where(["model_type" => $model_type, "model_id" => $model_id])->count() > 0 )
            return null; //exists

        // Check if model exist
        if( !$this->getModel($model_type, $model_id) )
            throw new \Error("Model does not exist. ".$model_id."_".$model_type);

        DB::table("contact_history_entries_models")->insert(
            [
                "che_id" => $this->id,
                "model_type" => $model_type,
                "model_id" => $model_id,
                "is_sender" => $isSender,
                "is_cc" => false
            ]);
    }

    public function addReceiver( $model_type, $model_id)
    {
        return $this->addModel($model_type, $model_id, false);
    }

    public function addSender( $model_type, $model_id)
    {
        return $this->addModel($model_type, $model_id, true);
    }

    public function removeModel( $model_type, $model_id)
    {
       $this->getModelRelations()->where(
            [   "model_type" => $model_type,
                "model_id" => $model_id,
                "che_id" => $this->id
            ])->delete();
    }

    //Senders

    public function getSenderNames() {

        $names = [];
        foreach($this->getModels()["senders"] as $model)
            $names[] = $model->getDisplayNameAttribute();

        return count($names) > 0? join(",", $names) : "Unbekannter Nutzer";
    }

    public function getSenderEmails() {

        $addresses = [];
        foreach($this->getModels()["senders"] as $model)
            $addresses = array_merge($addresses, $model->getEmails());
        return $addresses;
    }

    public function getSenderNamesAttribute() {
        return $this->getSenderNames();
    }

    //Receivers

    public function getReceiverNames() {

        $names = [];
        foreach($this->getModels()["receivers"] as $model)
            $names[] = $model->getDisplayNameAttribute();

        return count($names) > 0? join(",", $names) : "Unbekannter Nutzer";
    }

    public function getReceiverEmails() {

        $addresses = [];
        foreach($this->getModels()["receivers"] as $model)
            $addresses = array_merge($addresses, $model->getEmails());
        return /*is_array($this->additional_receivers)? array_merge($addresses, $this->additional_receivers) :*/ $addresses;
    }

    public function getReceiverNamesAttribute() {
        return $this->getReceiverNames();
    }


}
