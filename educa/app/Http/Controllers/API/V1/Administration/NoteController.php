<?php

namespace App\Http\Controllers\API\V1\Administration;

use App\Note;
use App\Schuljahr;
use Illuminate\Http\Request;

class NoteController extends AdministationApiController
{

    public function setGrades(Request $request)
    {
        $user = parent::getUserForToken($request);
        if($request->has("schuljahr_id"))
            $schuljahr = Schuljahr::findOrFail($request->input("schuljahr_id"));
        else
            $schuljahr = Schuljahr::find(0); //Fallback (?)

        $model_id = $request->input("model_id");
        $model_type = $request->input("model_type");

        $noten = [];

        foreach($request->object->notes as $set)
        {
            $note = new Note;
            $note->schuler_id = $set->schuler_id;
            $note->schuljahr_id = $schuljahr->id;
            $note->note = $set->note;
            $note->datum = new \DateTime();
            $note->bemerkung = $set->bemerkung;
            $note->model_type = $model_type;
            $note->model_id = $model_id;
            $note->cloud_user_id = $user->id;
            $note->save();
            $noten[] = $note;
        }
        return parent::createJsonResponse("grades created",false, 200, [ "grades" => $noten]);
    }

    public function updateGrades(Request $request)
    {
        $user = parent::getUserForToken($request);
        if($request->has("schuljahr_id"))
            $schuljahr = Schuljahr::findOrFail($request->input("schuljahr_id"));
        else
            $schuljahr = null;

        if($request->has("model_id"))
            $model_id = $request->input("model_id");
        else
            $model_id = null;
        if($request->has("model_type"))
            $model_type = $request->input("model_type");
        else
            $model_type = null;

        $noten = [];

        foreach($request->object->notes as $set)
        {
            $note = Note::findOrFail($set->id);

            if($set->schuler_id)
                $note->schuler_id = $set->schuler_id;
            if($schuljahr !== null)
                $note->schuljahr_id = $schuljahr->id;
            if($set->note)
                $note->note = $set->note;

            $note->datum = new \DateTime();
            if($set->bemerkung)
                $note->bemerkung = $set->bemerkung;
            if($model_type !== null)
                $note->model_type = $model_type;
            if($model_id !== null)
                $note->model_id = $model_id;

            $note->cloud_user_id = $user->id;
            $note->save();
            $noten[] = $note;
        }
        return parent::createJsonResponse("grades updated",false, 200, [ "grades" => $noten]);
    }

    public function updateGrade($grade_id, Request $request)
    {
        $user = parent::getUserForToken($request);
        $note = Note::findOrFail($grade_id);

        if($request->has("model_id"))
            $note->model_id = $request->input("model_id");

        if($request->has("model_type"))
            $note->model_type = $request->input("model_type");

        if($request->has("schuler_id"))
            $note->schuler_id = $request->input("schuler_id");
        if($request->has("schuljahr_id"))
            $note->schuljahr_id = $request->input("schuljahr_id");
        if($request->has("note"))
            $note->note = $request->input("note");
        if($request->has("bemerkung"))
            $note->bemerkung = $request->input("bemerkung");
        $note->datum = new \DateTime();
        $note->cloud_user_id = $user->id;
        $note->save();
        return parent::createJsonResponse("grades updated",false, 200, [ "grade" => $note]);
    }

    public function getGrades(Request $request)
    {
        $model_id = $request->input("model_id");
        $model_type = $request->input("model_type");

        $noten = Note::where("model_id", "=", $model_id)->where("model_type", "=", $model_type)->get();
        return parent::createJsonResponse("grades for model",false, 200, [ "grades" => $noten]);
    }
}
