<?php

namespace App\Http\Controllers\API\V1\Administration\Correspondence;

use App\ContactHistoryEntry;
use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\V1\Administration\AdministationApiController;
use App\Jobs\ProcessMail;
use App\Mail\CorrespondenceCopyMail;
use App\Mail\CorrespondenceMail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CorrespondenceController extends AdministationApiController
{

    public function listCorrespondence(Request $request)
    {
        $model_type = $request->input("model_type");
        $model_id = $request->input("model_id");

        if(!$model_id || !$model_type)
            return parent::createJsonResponse("Rodrigo Correspondenz: el model typo ese no set", true, 400);

        $correspondences = ContactHistoryEntry::getByModel($model_type, $model_id);
        return parent::createJsonResponse("Correspondez load", false, 200, ["correspondences" => $correspondences]);
    }

    public function createCorrespondence(Request $request)
    {
        $model_type = $request->input("model_type");
        $model_id = $request->input("model_id");

        $additional_receivers = $request->input("additional_receivers");

        if($additional_receivers && !is_array($additional_receivers))
            return parent::createJsonResponse("Additional receivers not set properly.", true, 400);

        if(config("stupla.system","test") == "test") {
            return parent::createJsonResponse("Erfolg, aber im Testsystem werden keine Nachrichten verschickt", true, 400);
        }

        if(!$model_id || !$model_type)
            return parent::createJsonResponse("el model typo ese no set", true, 400);

        $elRodrigoCorrespondez = ContactHistoryEntry::new(
            $request->input("subject"),
            $request->input("content"),
            $additional_receivers,
            [ContactHistoryEntry::$MODEL_TYPE_EMPLOYEE => [parent::getAdministationUser()->id]],
            [$model_type => [$model_id]],
            Carbon::createFromTimestamp($request->input("time", Carbon::now()->unix())),
            $request->input("type", ""),
            true
        );

        $elRodrigoCorrespondez->save();

        if($request->input("sendToAdministrationUser"))
        {
            $email = parent::getAdministationUser()->email;
            if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
                //Mail::to($email)->send(new CorrespondenceCopyMail($elRodrigoCorrespondez));
                ProcessMail::dispatch($email,$elRodrigoCorrespondez)->onQueue("mails");
            }

            if(is_array($additional_receivers) && count($additional_receivers) > 0)
                foreach ($additional_receivers as $email)
                    if(filter_var($email, FILTER_VALIDATE_EMAIL))
                        ProcessMail::dispatch($email,$elRodrigoCorrespondez)->onQueue("mails");

        }

        if($request->input("sendToReceiver"))
        {
            $emails = $elRodrigoCorrespondez->getReceiverEmails();
            foreach ($emails as $email)
            {
                if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
                  //  Mail::to($email)->send(new CorrespondenceMail($elRodrigoCorrespondez));
                    ProcessMail::dispatch($email,$elRodrigoCorrespondez)->onQueue("mails");
                }
            }

            if(is_array($additional_receivers) && count($additional_receivers) > 0)
                foreach ($additional_receivers as $email)
                    if(filter_var($email, FILTER_VALIDATE_EMAIL))
                        ProcessMail::dispatch($email,$elRodrigoCorrespondez)->onQueue("mails");

        }

        return parent::createJsonResponse("Correspondez created", false, 200, ["correspondence" => $elRodrigoCorrespondez]);
    }


    public function createMultiCorespondeMulti(Request $request)
    {
        $model_type = $request->input("model_type");
        $model_ids = $request->input("model_ids");
        $additional_receivers = $request->input("additional_receivers");

        if($additional_receivers && !is_array($additional_receivers))
            return parent::createJsonResponse("Additional receivers not set properly.", true, 400);

       if(config("stupla.system","test") == "test") {
            return parent::createJsonResponse("Erfolg, aber im Testsystem werden keine Nachrichten verschickt", true, 400);
        }

        if(!$model_ids || !$model_type || !is_array($model_ids))
            return parent::createJsonResponse("el model typo ese no set", true, 400);

        //Check
        foreach ($model_ids as $model_id) {
            if (!$model_id || $model_id == "0" || !ContactHistoryEntry::getModel($model_type, $model_id))
                return parent::createJsonResponse("At least one model not found.", true, 400);
        }

        //Process
        foreach ($model_ids as $model_id) {

            $elRodrigoCorrespondez = ContactHistoryEntry::new(
                $request->input("subject"),
                $request->input("content"),
                $additional_receivers,
                [ContactHistoryEntry::$MODEL_TYPE_EMPLOYEE => [parent::getAdministationUser()->id]],
                [$model_type => [$model_id]],
                );

            $emails = $elRodrigoCorrespondez->getReceiverEmails();
            foreach ($emails as $email)
            {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                  //  Mail::to($email)->send(new CorrespondenceMail($elRodrigoCorrespondez));
                    ProcessMail::dispatch($email,$elRodrigoCorrespondez)->onQueue("mails");
                }
            }
            if(is_array($additional_receivers) && count($additional_receivers) > 0)
                foreach ($additional_receivers as $email)
                    if(filter_var($email, FILTER_VALIDATE_EMAIL))
                        ProcessMail::dispatch($email,$elRodrigoCorrespondez)->onQueue("mails");
        }

        return parent::createJsonResponse("Correspondez sent", false, 200);
    }

    /*
    public function updateCorrespondence($correspondence_id, Request $request)
    {
        $correspondez = ContactHistoryEntry::findOrFail($correspondence_id);
        $correspondez->time = Carbon::parse($request->input("time"));
        $correspondez->type = $request->input("type");
        $correspondez->subject = $request->input("subject");
        $correspondez->content = $request->input("content");
        $correspondez->save();

        return parent::createJsonResponse("Correspondez load", false, 200, $correspondez);
    }

    public function deleteCorrespondence($correspondence_id, Request $request)
    {
        $correspondez = ContactHistoryEntry::findOrFail($correspondence_id);
        $correspondez->delete();

        return parent::createJsonResponse("Correspondez deleted", false, 200);
    }
    */
}
