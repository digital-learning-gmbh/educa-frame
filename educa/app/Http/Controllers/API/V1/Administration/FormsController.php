<?php

namespace App\Http\Controllers\API\V1\Administration;

use App\Klasse;
use App\ModulPartExamDate;
use App\ModulPartExamLabel;
use App\Submission;
use Illuminate\Http\Request;
use StuPla\CloudSDK\formular\models\Formular;
use StuPla\CloudSDK\formular\models\FormularRevision;

class FormsController extends AdministationApiController
{

    public function getRevision($form_id,Request $request) {
        $form = Formular::findOrFail($form_id);
        $revision = $form->lastRevision;
        $model = null;
        if($request->input("model_type") == "schoolclass")
        {
            $model = Klasse::find($request->input("model_id"));
        }
        if($request->input("model_type") == "submission")
        {
            $model = Submission::find($request->input("model_id"));
        }
        if($request->input("model_type") == "modul_part_exam_dates")
        {
            $model = ModulPartExamDate::find($request->input("model_id"));
        }

        if($model == null)
            return parent::createJsonResponse("ok, empty form data",false, 200,["revision" => $revision]);

        $data = $model->getLatestFormulaDataFor($form);
        return parent::createJsonResponse("ok",false, 200,["revision" => $revision, "data" => json_decode($data)]);
    }

    public function saveFilledRevision($form_id, $revision_id, Request $request) {
        $revision = FormularRevision::findOrFail($revision_id);
        $model = null;
        if($request->input("model_type") == "schoolclass")
        {
            $model = Klasse::find($request->input("model_id"));
        }
        if($request->input("model_type") == "submission")
        {
            $model = Submission::find($request->input("model_id"));
        }
        if($request->input("model_type") == "modul_part_exam_dates")
        {
            $model = ModulPartExamDate::find($request->input("model_id"));
        }

        if($model == null)
            return parent::createJsonResponse("no model found",true, 400);

        $data = json_encode($request->input("form_data"));
        $model->saveFormulaDataFor($revision->id, $data);
        return parent::createJsonResponse("form was saved",false, 200,[]);
    }
}
